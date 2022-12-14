<?php

use App\Models\Bank;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});



Route::get('load-banks', function(){

    $url = "https://api.paystack.co/bank?currency=NGN";
    $headers = [
        "Authorization" => "Bearer " . env('PAYSTACK_SECRET_KEY'),
        "Content-Type" => "application/json"
    ];

    $response = Http::withHeaders($headers)->get($url);

    $verify = json_decode($response);

    if(count($verify->data) !=  Bank::count())
    {
        foreach($verify->data as $bank)
        {
            Bank::firstOrCreate([
                'bank_name' => $bank->name,
                'bank_code' => $bank->code,
                'country'   => $bank->country,
                'currency'  => $bank->currency,
                'type'      => $bank->type,
            ]);
        }
    }

    return 'done';
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
