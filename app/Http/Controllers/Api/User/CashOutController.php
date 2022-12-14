<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Interfaces\CashoutInterface;
use App\Models\MyBank;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            'bank_id'=> 'required|integer'
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors());
        }

        $userId = auth()->user()->id;

        $getBank =  MyBank::where(['user_id' => $userId , 'id' => $request->bank_id])->first();

        if(!$getBank){
            return $this->sendError('Oops',  ['Please select a bank assigned to you']);
        }

        $verifyBank =  $this->cashoutRepository->verifyBank($request->amount, $getBank->account_number);

        $isSuccess = $verifyBank['success'];

        if(!$isSuccess) {
            return $this->sendError('Oops an Error Occurred',  $verifyBank);
        }

        $success['data'] = $verifyBank;

        return $this->sendResponse($success, 'Cash withdrawal is successful');
    }
}
