<?php

namespace App\Providers;

use App\Interfaces\CashoutInterface;
use App\Interfaces\Payment;
use App\Repositories\CashoutRepository;
use App\Repositories\PaymentRepository;
use Illuminate\Support\ServiceProvider;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Payment::class, PaymentRepository::class);
        $this->app->bind(CashoutInterface::class, CashoutRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
