<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;

class RoleManagement extends BaseController
{
    public function addRole(Request $request){

        $validator = Validator::make($request->all(), [
                            'name' => 'required',
                         ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors());
        }
        $role =  Role::create(['guard_name' => 'admin', 'name' => $request->name]);

        $success['role'] = $role;

        return $this->sendResponse($success, 'Role Created successfully');

    }

    public function assignPermission(Request $request , $role_id){

        $validator = Validator::make($request->all(), [
                          'permission_id' => 'required',
                          ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors());
        }

        $role =  Role::where(['guard_name' => 'admin', 'id' => $role_id])->first();

        if(!$role){
            return $this->sendError('Oops', ['Role not found']);
        }

        $permission = Permission::where(['guard_name' => 'admin', 'id' => $request->permission_id])->first();

        if(!$permission){
            return $this->sendError('Oops', ['Permission not found']);
        }

        $role->givePermissionTo($permission);

        $success['permission'] = $permission;

        return $this->sendResponse($success, 'Permission assigned to role  successfully' ,Response::HTTP_CREATED);
    }



    public function revokePermission(Request $request , $role_id){

        $validator = Validator::make($request->all(), [
            'permission_id' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Error validation', $validator->errors());
        }

        $role =  Role::where(['guard_name' => 'admin', 'id' => $role_id])->first();

        if(!$role){
            return $this->sendError('Oops', ['Role not found']);
        }

        $permission = Permission::where(['guard_name' => 'admin', 'id' => $request->permission_id])->first();

        if(!$permission){
            return $this->sendError('Oops', ['Permission not found']);
        }
        $role->revokePermissionTo($permission);

        $success['permission'] = $permission;

        return $this->sendResponse($success, 'Permission revoked   successfully');
    }

    public function fetchRole(){
        $roles =  Role::where(['guard_name' => 'admin'])->with('permissions')->get();

        $success['roles'] = $roles;
        return $this->sendResponse($success, 'Roles fetched  successfully');
    }

    public function fetchPermissions(){
        $permissions =   Permission::where(['guard_name' => 'admin'])->get();
        $success['permissions'] = $permissions;
        return $this->sendResponse($success, 'Permission fetched  successfully');
    }







}
