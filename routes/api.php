<?php

use App\Http\Controllers\Api\User\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1'], function() {
    Route::post('create-account', [AuthController::class, 'createAccount'])->name('create_account');
    Route::post('authenticate',[AuthController::class , 'signin'])->name('signin');

    Route::group(['middleware' => ['auth:sanctum','is_banned']], function () {
        Route::post('verify-otp',[AuthController::class ,'verifyOtp'])->name('verify_otp');
        Route::group(['middleware' => ['must_verify']], function () {
            Route::get('profile',[AuthController::class ,'myProfile'])->name('profile');
        });

    });
});
