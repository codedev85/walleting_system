<?php

namespace App\Repositories;

use App\Helper\Wallet as BankWallet;
use App\Interfaces\CashoutInterface;
use App\Models\Bank;
use App\Models\MyBank;
use Exception;
use Illuminate\Support\Facades\Http;

class CashOutRepository implements CashoutInterface
{
    private $amount;

    public function verifyBank($amount, $accountNumber)
    {
        $user = auth()->user();
        $selectedBank = MyBank::where('account_number', $accountNumber)->first();

        $this->amount = $amount;

        if(is_null($accountNumber))
        {
            $response  = ['success' => false ,
                'message' => 'Please ensure you add account details'];

            return  $response;
        }

        //check negative value
        if($amount < 1)
        {
            $response =  ['success' => false ,
                "message" => "you cant withdraw zero balance"];
            return $response;
        }

        //check wallet balance
        if( $amount > $user->MyWalletBalance)
        {
            $response =  ['success' => false ,
                'message' =>  'The amount you want to withdraw is more than your wallet balance'];

            return $response;
        }

        $this->fetchBank();

        $bankResolver =  $this->banKResolver($user, $selectedBank);

        if(isset($bankResolver->status))
        {
            $this->initiateTransfer($bankResolver);

            $info             =  'Cash Withdrawal from wallet to bank  of N' . $this->amount . ' at ' . now();
            $transactionType  =  'Cashout';
            $debitWallet     =    BankWallet::debit($user, $this->amount, $info, $transactionType);

            return  $debitWallet ;
        }else{
            $response = ['success' => false ,
                'message' =>  'Could resolve bank issuer'];

            return $response;
        }



    }


    public function fetchBank()
    {

        try{
            $url = "https://api.paystack.co/bank?currency=NGN";

            $verify =  $this->makeApiCallsGet($url);

            if(count($verify->data) !=  Bank::count())
            {
                foreach($verify->data as $bank)
                {
                    Bank::firstOrCreate([
                        'bank_name' => $bank->name,
                        'bank_code' => $bank->code,
                        'country'   => $bank->country,
                        'currency'  => $bank->currency,
                        'type'      => $bank->type,
                    ]);
                }
            }

            return   Bank::all();


        }catch(Exception $e)
        {
            return $e->getMessage();
        }
    }

    public function banKResolver($user , $selectedBank)
    {
        $bankCode = Bank::where('id', $selectedBank->bank_id)->first();

        if(!$bankCode) {return  ['success' => false , "message" => 'Your please update your bank information'];}

        try{

            $url = 'https://api.paystack.co/bank/resolve?account_number='. $selectedBank->account_number.'&bank_code='.$bankCode->bank_code;

            $resolveBank =  $this->makeApiCallsGet($url);

            return   $this->transferToRecipient($resolveBank->data , $user , $bankCode);

        }catch(Exception $e){

            return $e->getMessage();
        }
    }


    public function transferToRecipient($bankResolver , $userBank , $bankCode)
    {
        $url = "https://api.paystack.co/transferrecipient";

        $transferToRecipient =  $this->makeApiCallsPost($url ,$bankResolver ,$userBank, $bankCode);

        return $transferToRecipient;
    }


    public function initiateTransfer($bankResolver)
    {
        $url = 'https://api.paystack.co/transfer';

        $headers = [
            "Authorization" => "Bearer " . env('PAYSTACK_SECRET_KEY'),
            "Content-Type" => "application/json"
        ];

        $response = Http::withHeaders($headers)->post($url,[
            'source'     => 'balance',
            'amount'    => $this->amount,
            'recipient' => $bankResolver->data->recipient_code,
            'reason'    => 'Hols trip']);

        $recipient = json_decode($response);


        return  $recipient;
    }



    protected function makeApiCallsGet($url)
    {
        $headers = [
            "Authorization" => "Bearer " . env('PAYSTACK_SECRET_KEY'),
            "Content-Type" => "application/json"
        ];

        $response = Http::withHeaders($headers)->get($url);

        $verify = json_decode($response);

        return  $verify;
    }

    protected function makeApiCallsPost($url, $bankResolver  , $userBank ,$bankCode)
    {
        $headers = [
            "Authorization" => "Bearer " . env('PAYSTACK_SECRET_KEY'),
            "Content-Type" => "application/json"
        ];
        $response = Http::withHeaders($headers)->post($url,[
            'type'           => $bankCode->type,
            'name'           => $bankResolver->account_name,
            'account_number' => $bankResolver->account_number,
            'bank_code'      => $bankCode->bank_code,
            'currency'       => $bankCode->currency,]);

        $recipient = json_decode($response);

        return  $recipient;
    }
}
