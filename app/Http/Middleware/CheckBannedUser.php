<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckBannedUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
//     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        if(Auth::check() && auth()->user()->isBanned === "false"){
            return $next($request);
        }else{
//            auth()->user()->tokens()->delete();
            $response = [
                'success' => false,
                'message' => "Your access to this platform has been suspend , contact support",
            ];

            return response()->json($response, 404);
        }


    }
}
