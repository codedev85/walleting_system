<?php

namespace App\Http\Controllers\Api\User;

use App\Events\VerificationMail;
use App\Helper\Otp;
use App\Helper\Wallet;
use App\Helpers\VerifyToken;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Jobs\SendMailJob;
use App\Models\User;
use App\Notifications\VerificationTokenMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use App\Models\Wallet as Account;
use App\Models\Otp as OtpToken;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends BaseController
{

    public function createAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'         => 'required',
            'email'        => 'required|string|email|unique:users,email',
            'password'     => 'required|string|min:6|confirmed',
            'phone_number' => 'required|string|unique:users,phone_number'

        ]);


        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        DB::beginTransaction();

        $accountNumber = Wallet::generate();
        $token = Otp::generate();

        $user = User::create([
            'name'         => $request->name,
            'password'     => bcrypt($request->password),
            'email'        => $request->email,
            'phone_number' => $request->phone_number,
        ]);

        $walletCreation                 = new Account();
        $walletCreation->account_number = $accountNumber;
        $walletCreation->user_id        = $user->id;
        $walletCreation->save();

        $newToken          = new OtpToken();
        $newToken->id      = Str::uuid();
        $newToken->token   =  $token;
        $newToken->user_id = $user->id;
        $newToken->save();

        //generate virtual wallet
        $success['token']  =  $user->createToken('MyAuthApp')->plainTextToken;
        $success['user']   =  $user;
        $success['wallet'] = $walletCreation;
//        event(new VerificationMail($user, $token))

        dispatch(new SendMailJob($user , $token));

        DB::commit();

        return $this->sendResponse($success, 'User created successfully' ,Response::HTTP_CREATED);

    }
    //use this method to signin users
    public function signin(Request $request)
    {
        $attr = Validator::make($request->all(), [
            'email' => 'required|string|email|',
            'password' => 'required|string|min:6'
        ]);

        if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            return $this->sendError('Credentials does not match', $attr->errors(),Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $success['token'] = auth()->user()->createToken('API Token')->plainTextToken;
        $success['user'] = auth()->user()->wallet;


        return $this->sendResponse($success, 'User signed in',Response::HTTP_OK);

    }


    public function myProfile(){
        $user                     = auth()->user();
        $success['user']          = $user ;
        $success['wallet']        = $user->wallet;
        $success['banks']         = $user->banks;
        $success['transactions']  = $user->MyTransaction;
        $success['walletBalance'] = $user->MyWalletBalance;

        return $this->sendResponse($success, 'User profile fetched',Response::HTTP_OK);
    }


    public function verifyOtp(Request $request){

        $validator = Validator::make($request->all(), ['otp' => 'required']);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors());
        }

        DB::beginTransaction();

        $userId = auth()->user()->id;

        $checkToken =  OtpToken::where(['user_id' =>  $userId, 'token' => $request->otp])->first();

        if(!$checkToken){
            return $this->sendError('Token not found', ['Token not found']);
        }

        if($checkToken->expires_at !== null){
            return $this->sendError('Token has expired', ['Token has expired']);
        }

        $userVerification =  User::where('id',$userId)
                                    ->update(['email_verified_at' => now()]);

        if($userVerification){
            OtpToken::where('token',$request->otp)
                                ->update(['expires_at' => now()]);
         }
        $success['user'] = auth()->user()->name;

        DB::commit();

        return $this->sendResponse($success, 'Email verification successful');


    }

    // this method signs out users by removing tokens
    public function signout()
    {
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Tokens Revoked'
        ];
    }

}
