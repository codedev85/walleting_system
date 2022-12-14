<?php

use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\PaymentController;
use App\Http\Controllers\Api\Admin\UserManagementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function() {

//    Route::post('create-account', [AuthController::class, 'createAccount'])->name('create_account');
    Route::post('authenticate',[AuthController::class , 'authenticateAdmin'])->name('authenticate_admin');
    //This is a webhook endpoint connected to paystack

    Route::post('verify-payment',[PaymentController::class , 'verifyPaymentWithWebHook'])->name('verify_payment');
    Route::get('verify-payment/{reference}',[PaymentController::class ,'verifyReference'])->name('verify_payment');

    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::post('make-payment',[PaymentController::class , 'makePayment'])->name('make_payment');
        Route::post('on-board-user',[UserManagementController::class ,'onBoardUser'])->name('onboard-user');
        Route::post('bulk-import-users',[UserManagementController::class ,'bulkUserImport'])->name('bulk-import');
        Route::get('bulk-export-users',[UserManagementController::class ,'exportData'])->name('bulk-export');


//        Route::get('verify-payment/{reference}',[PaymentController::class ,'confirmPayment'])->name('verify_payment');
//        Route::post('verify-otp',[AuthController::class ,'verifyOtp'])->name('verify_otp');
//        Route::group(['middleware' => ['must_verify']], function () {
//            Route::get('profile',[AuthController::class ,'myProfile'])->name('profile');
//        });

    });
});
