<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use App\Mail\AdminCreds;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;

class AdminManagement extends BaseController
{
    public function createAccount(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'role_id' => 'required|integer'
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors() ,Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        DB::beginTransaction();
        $password = Str::random(6);;
        $admin = new Admin();
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->password = Hash::make($password);
        $admin->email_verified_at = now();
        $admin->save();
        $role = Role::where('id', $request->role_id)->first();
        $admin->assignRole($role);
        DB::commit();
        $success['admin']   =  $admin;
        Mail::to($request->email)->send(new AdminCreds($admin , $password));
        return $this->sendResponse($success, 'Admin created successfully' , Response::HTTP_CREATED);
    }
}
