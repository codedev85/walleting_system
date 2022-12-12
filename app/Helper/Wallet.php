<?php

namespace App\Helper;

use App\Events\ReferalNotification;
use App\Events\WalletDebitTransaction;
use App\Events\WalletTopUp;
use App\Events\WalletTransactions;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class Wallet
{
    public static function generateAcountNumber()
    {
        $no = (string) random_int(00000000, 99999999);
        $no = "21$no";

        if ( strlen($no) != 10 ) {
            return self::generateAcountNumber();
        }

        if ( \App\Models\Wallet::where('account_number', $no)->count() > 0 ) {
            return self::generateAcountNumber();
        }

        return $no;
    }



    public static function generateTrnxRef()
    {
        $no = (string) random_int(00000000, 99999999);

        $no = "wal-$no";

        if ( strlen($no) != 12 ) {
            return self::generateTrnxRef();
        }

        if (\App\Models\Transaction::where('ref', $no)->count() > 0 ){
            return self::generateTrnxRef();
        }

        return $no;
    }

    /**
     * Credit Wallet
     *
     * @param $user
     * @param $amount
     * @param $creditedBy
     * @param $info
     * @param $transactionType
     * @param $currency
     * @param bool $isCommission
     * @return array
     */
    public static function credit($user, $amount, $creditedBy , $info,$transactionType=null , $currency=null , $isCommission = false)
    {


        try {


            $wallet = $user->wallet;

            // Check if wallet is active
            if ($wallet->status !== 'ACTIVE') {
                return [
                    'success' => false,
                    'message' => "Wallet is {$wallet->status}"
                ];
            }

            DB::transaction(function () use ($user, $wallet, $amount, $creditedBy, $info, $transactionType, &$response, $isCommission ) {
//                Log::info($transactionType);
                $prev_bal = $wallet->balance;
                $wallet->balance += $amount;
                //calculate i-risk points
                $prev_point = $wallet->points;

                $wallet->points += $amount/env('I_RISK_POINT');

                $wallet->updated_at = now();
                $wallet->save();

                $transaction = new Transaction();
                $transaction->create([
                    'ref'              =>  self::generateTrnxRef(),
                    'wallet_id'        =>  $wallet->id,
                    'amount'           =>  $amount,
                    'user_id'          =>  $user->id,
                    'type'             => 'CREDIT',
                    'prev_balance'     =>  $prev_bal,
                    'new_balance'      =>  $wallet->balance,
                    'status'           =>  'successful',
                    'creditor_id'      =>  $creditedBy,
                    'transaction_type' =>  $transactionType,
                    'info'             =>  $info
                ]);
//                == null ? 'wallet-to-wallet' : "top-up"

                $creditor = \App\Models\User::find($creditedBy);

                // send notification
                if($transactionType == 'top-up'){
                    event(New WalletTopUp($user, $amount ,$creditor));
                }elseif($transactionType ==  "wallet-to-wallet"){
                    event(New WalletTransactions($user, $amount ,$creditor));
                }

                $response = [
                    'success' => true,
                    'message' => 'Wallet crediting was successful.'
                ];
            });

            return $response;
        } catch (\Exception $exception) {
            return [
                'success' => false,
                'message' => $exception->getMessage()
            ];
        }
    }


    /**
     * Debit Wallet
     *
     * @param $user
     * @param $amount
     * @param $beneficiaryId
     * @param $info
     * @param $transactionType
     * @param bool $allow_negative
     * @param bool $isCommission
     * @return array
     */
    public static function debit($user, $amount,$beneficiaryId, $info, $transactionType, $allow_negative = false, $isCommission = false)
    {
        try {

            $wallet = $user->wallet;

            // Check if wallet is active
            if ($wallet->status !== 'ACTIVE') {
                return [
                    'success' => false,
                    'message' => "Wallet is {$wallet->status}"
                ];
            }


            if ($amount > $wallet->balance) {
                if (!$allow_negative) {
                    return [
                        'success' => false,
                        'message' => "Insufficient Fund!"
                    ];
                }
            }


            DB::transaction(function () use ($user, $wallet, $amount,$beneficiaryId, $info,$transactionType, &$response, $isCommission) {

                $prev_bal = $wallet->balance;
                $wallet->balance -= $amount;
                $wallet->updated_at = now();
                $wallet->save();

                $wallet->transactions()->create([
                    'ref'              => self::generateTrnxRef(),
                    'amount'           => $amount,
                    'wallet_id'        => $wallet->id,
                    'type'             => 'DEBIT',
                    'user_id'          => $user->id,
                    'prev_balance'     => $prev_bal,
                    'new_balance'      => $wallet->balance,
                    'status'           => 'successful',
                    'beneficiary_id'    => $beneficiaryId ?? null,
                    'transaction_type' =>  $transactionType,
                    'info'             => $info
                ]);

                // send notification
                $beneficiary = \App\Models\User::find($beneficiaryId);
                event(New WalletDebitTransaction($user, $amount ,$beneficiary));
                $response = [
                    'success' => true,
                    'message' => 'Wallet debit was successful.'
                ];
            });
            return $response;
        } catch (\Exception $exception) {
            return [
                'success' => false,
                'message' => $exception->getMessage()
            ];
        }
    }




}