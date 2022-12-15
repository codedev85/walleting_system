<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Interfaces\CashoutInterface;
use App\Jobs\AdminNotifier;
use App\Jobs\AdminNotifierJob;
use App\Jobs\SuspensionMailJob;
use App\Models\MyBank;
use App\Models\WithdrawalPin;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CashOutController extends BaseController
{
    private CashoutInterface $cashoutRepository;

    public function __construct(CashoutInterface  $cashoutRepository)
    {
        $this->cashoutRepository = $cashoutRepository;
    }

    public function verifyBank(Request $request)
//    : JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'bank_id'=> 'required|integer',
            'pin' => 'required|min:4|max:4',
            'password' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors());
        }

        $userId = auth()->user()->id;

        $checkAttempt = $this->checkUserAttempt();

        if(!$checkAttempt){
            return $this->sendError('Oops an Error Occurred',  ['Your wallet has been suspended']);
        }

        $validatePin = $this->validateWithdrawalPin($userId ,$request->pin);

        if(!$validatePin){
            //check attempt count
            $this->triggerAttemptCount();
            return $this->sendError('Oops an Error Occurred',  ['Invalid pin']);
        }

        $validatePassword =  $this->validatePassword($request->password);

        if(!$validatePassword){
            //check attempt count
            $this->triggerAttemptCount();
            return $this->sendError('Oops an Error Occurred',  ['Invalid password']);
        }

        $getBank =  MyBank::where(['user_id' => $userId , 'id' => $request->bank_id])->with('bank')->first();

        if(!$getBank){
            return $this->sendError('Oops',  ['Please select a bank assigned to you']);
        }

        $verifyBank =  $this->cashoutRepository->verifyBank($request->amount, $getBank->account_number);

        $isSuccess = $verifyBank['success'];

        if(!$isSuccess) {
            return $this->sendError('Oops an Error Occurred',  $verifyBank);
        }

        $success['data'] = $verifyBank;

        //after successful cash-out invalidate the withdrawal token and notify admin
        $this->invalidateWithdrawalToken();

        $this->adminNotifierTrigger($request->amount , auth()->user() , $getBank);

        return $this->sendResponse($success, 'Cash withdrawal is successful');
    }


    protected function validateWithdrawalPin($userId , $pin){

      $hasdhedPin =  WithdrawalPin::where(['user_id' => $userId])->latest()->first();

      $unhashedPin = Crypt::decryptString($hasdhedPin->token);

      if($unhashedPin === $pin && $hasdhedPin->expires_at === null){
          return true;
      }
        return false;
    }

    protected function validatePassword($password){
        if(!Hash::check($password, auth()->user()->password)){
            return false;
        }
        return true;
    }

    private function checkUserAttempt(){
        $user = auth()->user();
        $wallet = $user->wallet;
        if((int) $wallet->failed_withdrawal_attempt  === (int)  config('withdrawal.max_attempt')){
             $wallet->status = 'SUSPENDED';
             $wallet->save();
             $type = 'Wallet';
             dispatch(new SuspensionMailJob($user, $type));
             return false;
        }
        return true;
    }

    protected function triggerAttemptCount(){
        $user = auth()->user();
        $wallet = $user->wallet;
        $wallet->failed_withdrawal_attempt += 1;
        $wallet->save();
        return $wallet;
    }

    private function invalidateWithdrawalToken(){
        $userId = auth()->user()->id;
        $hasdhedPin =  WithdrawalPin::where(['user_id' => $userId])->latest()->first();
        $hasdhedPin->update(['expires_at' => now()]);
        return true;
    }

    private function adminNotifierTrigger($amount , $user , $bank){

        if(config('admin.notify_admin')){
            dispatch(new AdminNotifierJob(config('admin.notifier_email'), $amount ,$user ,$bank));
            return true ;
        }
        return false;
    }

}
