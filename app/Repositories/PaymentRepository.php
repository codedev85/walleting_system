<?php

namespace App\Repositories;

use App\Helper\Wallet;
use App\Interfaces\Payment;
use App\Models\Transaction as Tranx;
use App\Models\User;
use App\Models\Wallet as BankWallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentRepository implements Payment
{
    public function makePayment($amount , $walletID){

        $url = "https://api.paystack.co/transaction/initialize";

        $headers = [
            "Authorization" => "Bearer " . env('PAYSTACK_SECRET_KEY'),
            "Content-Type" => "application/json",
        ];

        $wallet =  BankWallet::where('account_number',$walletID)->first();

        $response = Http::withHeaders($headers)->post($url, [
            'email'  => $wallet->user->email,
            'amount' => $amount * 100,
            "metadata" => [
              //  "transaction_type" => 'funding',
            ]]);

        $paymentLink = json_decode($response);

        return  $paymentLink;
    }

    public function verifyPayment($ref , $email)
    {

        $url = "https://api.paystack.co/transaction/verify/" . $ref;

        $headers = [
            "Authorization" => "Bearer " . env('PAYSTACK_SECRET_KEY'),
            "Content-Type" => "application/json"
        ];

        $response = Http::withHeaders($headers)->get($url);

        $verify = json_decode($response);

        if ($verify->status) {
            //find user based on email

            $findUser = User::where('email', $verify->data->customer->email)->with('wallet')->first();
            $info = 'Credited successfully at '. now();
            $transactionType='top-up';
            $wallet = Wallet::credit($findUser , $verify->data->amount/100, $creditedBy=1 ,$info, $transactionType);
             Log::info($wallet);
            if(!$wallet['success']){
                $response = [
                    'success' => false,
                    'message' => 'Wallet crediting not successful'
                ];

            }else{
                $response = [
                    'success' => true,
                    'message' => $wallet['message']
                ];
            }
            return  $response;
        }
    }
}
