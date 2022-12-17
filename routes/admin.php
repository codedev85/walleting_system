<?php

use App\Http\Controllers\Api\Admin\AdminManagement;
use App\Http\Controllers\Api\Admin\AuthController;
use App\Http\Controllers\Api\Admin\PaymentController;
use App\Http\Controllers\Api\Admin\RoleManagement;
use App\Http\Controllers\Api\Admin\UserManagementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1'], function() {


    Route::post('authenticate',[AuthController::class , 'authenticateAdmin'])->name('authenticate_admin');

    //This is a webhook endpoint , it should be inputed into  paystack webhook url section
    //read ReadMe.md for more information;

    Route::post('verify-payment',[PaymentController::class , 'verifyPaymentWithWebHook'])->name('verify_webhook_payment');



    Route::group(['middleware' => ['auth:sanctum','permissions']], function () {

        Route::post('create-admin', [AdminManagement::class, 'createAccount'])->name('admin_create_account');

        Route::post('make-payment',[PaymentController::class , 'makePayment'])->name('make_payment');
        Route::get('verify-payment/{reference}',[PaymentController::class ,'verifyReference'])->name('verify_reference_payment');

        Route::post('on-board-user',[UserManagementController::class ,'onBoardUser'])->name('onboard-user');
        Route::post('bulk-import-users',[UserManagementController::class ,'bulkUserImport'])->name('bulk-import');
        Route::get('bulk-export-users',[UserManagementController::class ,'exportData'])->name('bulk-export');
        Route::get('suspend-user/{user_id}',[UserManagementController::class ,'suspendUser'])->name('suspend-user');
        Route::get('activate-user/{user_id}',[UserManagementController::class ,'activateUser'])->name('activate-user');

        //role management
        Route::post('create-role',[RoleManagement::class ,'addRole'])->name('create_role');
        Route::get('fetch-roles',[RoleManagement::class ,'fetchRole'])->name('fetch_role');
        Route::get('fetch-permission',[RoleManagement::class ,'fetchPermissions'])->name('fetch_permissions');
        Route::post('assign-permission-to-role/{role_id}',[RoleManagement::class ,'assignPermission'])->name('assign_permission_role');
        Route::post('revoke-permission-to-role/{role_id}',[RoleManagement::class ,'revokePermission'])->name('revoke_permission_role');


    });
});
