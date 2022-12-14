<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\MyBank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MyBankController extends BaseController
{
    public function fetchBanks(){
        DB::beginTransaction();
        $success['bank_list'] = Bank::get();
        DB::commit();
        return $this->sendResponse($success, 'Bank list fetched successfully');
    }

    public function fetchMyBanks(){
        DB::beginTransaction();
        $success['my_bank_list'] = MyBank::where('user_id',auth()->user()->id)->with('bank')->get();
        DB::commit();
        return $this->sendResponse($success, 'Bank list fetched successfully');
    }

    public function addBank(Request $request){

        $validator = Validator::make($request->all(), [
            'bank_id'         => 'required|integer',
            'account_name'    => 'required',
            'account_number'  => 'required|string|min:10|max:10|unique:my_banks,account_number',
        ]);


        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors());
        }


        DB::beginTransaction();
        $createBank = new MyBank();
        $createBank->bank_id = $request->bank_id;
        $createBank->user_id = auth()->user()->id;
        $createBank->account_name = $request->account_name;
        $createBank->account_number = $request->account_number;
        $createBank->save();
        DB::commit();
        $success['bank_list'] = $createBank;
        return $this->sendResponse($success, 'Bank created successfully');
    }
}
