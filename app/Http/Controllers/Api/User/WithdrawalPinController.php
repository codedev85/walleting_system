<?php

namespace App\Http\Controllers\Api\User;

use App\Helper\Otp;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Mail\WithdrawalPin as WithdrawalOtp;
use App\Models\WithdrawalPin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Response;


class WithdrawalPinController extends BaseController
{
    public function generatePin(){

        DB::beginTransaction();
        $otp = Otp::generate();
        $user =  auth()->user();
        $hashToken =  Crypt::encryptString($otp);
        $withdrawalPin = new WithdrawalPin();
        $withdrawalPin->user_id = auth()->user()->id;
        $withdrawalPin->token =  $hashToken;
        $withdrawalPin->save();
        Mail::to($user->email)->send(new  WithdrawalOtp($user ,$otp));
        $success['otp'] ='Otp generated  successfully';
        DB::commit();
        return $this->sendResponse($success, 'Otp generated  successfully' , Response::HTTP_CREATED);
    }
}
