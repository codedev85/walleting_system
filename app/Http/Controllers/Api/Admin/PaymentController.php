<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Interfaces\Payment;
use App\Models\Wallet as BankWallet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaymentController extends BaseController
{
    private Payment $paymentRepository;

    public function __construct(Payment  $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    ///admin to fund users wallet
    public function makePayment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount'    => 'required',
            'walletID' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors());
        }


        $wallet =  BankWallet::where('account_number',$request->walletID)->first();

        if(!$wallet){
            return $this->sendError('Oops!!!.', ['error'=>'Wallet ID is incorrect']);
        }

        $paymentLink =  $this->paymentRepository->makePayment($request->amount , $request->walletID);


        $success['data'] = $paymentLink;

        $success['payment_url'] = $success['data']->data->authorization_url;

        return $this->sendResponse($success, 'Payment Link sent successfully');

    }

    //using paystack
    //if the charge is successful then credit wallet

    public function verifyPaymentWithWebHook(Request $request)
    {

        if($request->event == 'charge.success')
        {
            $userEmail =  $request->data['customer']['email'];
            $reference =   $request->data['reference'];
            $isPaymentSuccessful =  $this->paymentRepository->verifyPayment($reference , $userEmail);

            if(!$isPaymentSuccessful['success']){
                $success['data'] =  $isPaymentSuccessful['success'];
//                Log::info($isPaymentSuccessful['message']);
                return $this->sendError($success,  $isPaymentSuccessful['message']);

            }else{
                $success['data'] =  $isPaymentSuccessful;
//                Log::info($isPaymentSuccessful['message']);
                return $this->sendResponse($success,  $isPaymentSuccessful['message']);
            }

        }

    }

    public function verifyReference($reference)
    {
        $url = "https://api.paystack.co/transaction/verify/" . $reference;
        $headers = [
            "Authorization" => "Bearer " . env('PAYSTACK_SECRET_KEY'),
            "Content-Type" => "application/json"
        ];

        $response = Http::withHeaders($headers)->get($url);

        $verify = json_decode($response);

        $email = $verify->data->customer->email;

        $isSuccess = $verify->data->gateway_response;

        if ($verify->status) {
            $success['data']      =   $email;
            $success['amount']    =  $verify->data->amount/100;
            $success['isSuccess'] =  $isSuccess;
            $success['reference'] = $reference;
            return $this->sendResponse($success, 'Payment made successfully');
        }else{
            $success['data']      = $email;
            $success['amount']    = 0;
            $success['isSuccess'] =  $isSuccess;
            $success['reference'] = $reference;
            return $this->sendError($success, ['Payment not  successful']);
        }


    }
}
