<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('ref')->unique();
            $table->uuid('wallet_id');
            $table->unsignedBigInteger('user_id');
            $table->float('amount',15,2);
            $table->float('prev_balance',15,2);
            $table->float('new_balance',15,2);
            $table->enum('type', ['DEBIT', 'CREDIT']);
            $table->enum('status',['successful','canceled','failed']);
            $table->enum('transaction_type', ['wallet-to-wallet', 'top-up', 'fund-withdrawal'])->default('wallet-to-wallet');
            $table->unsignedBigInteger('creditor_id')->nullable();
            $table->unsignedBigInteger('beneficiary_id')->nullable();
            $table->longText('info');
            $table->timestamps();

            $table->foreign('wallet_id')->references('id')->on('wallets')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
