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
        Schema::create('tarnsaction_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('transaction_id');
            $table->enum('transaction_type',['credit','debit']);
            $table->string('initiated_by');//user email that initiates the transaction
            $table->longText('info')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tarnsaction_logs');
    }
};
