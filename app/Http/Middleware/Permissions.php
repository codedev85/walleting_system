<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\UnauthorizedException;

class Permissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $permission = null, $guard = null)
    {

        $authGuard = app('auth')->guard('admin');
        $user = auth()->user();
        $roles = $user->roles;

//        $permissions = [];
        foreach($roles as $role){
            $permissions =  $role->permissions->pluck('name');
//            array_push($permissions , $role->permissions->pluck('name') ) ;
        }

        if (auth()->guest()) {
            return response()->json(['success' => false , 'error' => 'unauthorized']);
        }



        foreach ($permissions as $perm) {
            if ($perm  ===  $request->route()->getName()) {
                return $next($request);
            }
        }
      return response()->json(['success' => false , 'error' => 'you dont have permission to access this route']);
    }
}
