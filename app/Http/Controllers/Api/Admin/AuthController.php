<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends BaseController
{
    //use this method to signin users
    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout');
    }

    public function authenticateAdmin(Request $request)
    {


        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:admins',
            'password' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors());
        }

        if(Auth::guard('admin')->attempt(['email' => $request->email, 'password' => $request->password])){


            $authUser = Auth::guard('admin')->user();
            $success['token'] =  $authUser->createToken('MyAuthApp')->plainTextToken;
            $success['name']  =  $authUser->name;

            return $this->sendResponse($success, 'Admin signed in');
        }
        else{
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised'] ,Response::HTTP_UNAUTHORIZED);
        }
    }

    public function adminProfile()
    {
        $success['profile'] =  auth()->user();

        return $this->sendResponse($success, 'Admin signed in');
    }

    protected function guard()
    {
        return Auth::guard('admin');
    }
}
