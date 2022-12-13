<?php

use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function() {

//    Route::post('create-account', [AuthController::class, 'createAccount'])->name('create_account');
    Route::post('authenticate',[AuthController::class , 'authenticateAdmin'])->name('authenticate_admin');
    //This is a webhook endpoint connected to paystack n
    Route::post('verify-payment',[PaymentController::class , 'verifyPaymentWithWebHook'])->name('verify_payment');
    Route::get('verify-payment/{reference}',[PaymentController::class ,'verifyReference'])->name('verify_payment');

    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::post('make-payment',[PaymentController::class , 'makePayment'])->name('make_payment');


//        Route::get('verify-payment/{reference}',[PaymentController::class ,'confirmPayment'])->name('verify_payment');
//        Route::post('verify-otp',[AuthController::class ,'verifyOtp'])->name('verify_otp');
//        Route::group(['middleware' => ['must_verify']], function () {
//            Route::get('profile',[AuthController::class ,'myProfile'])->name('profile');
//        });

    });
});
