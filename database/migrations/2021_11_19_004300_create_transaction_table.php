<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->unsignedBigInteger('payee_id');
            $table->unsignedBigInteger('payer_id');
            $table->unsignedBigInteger('amount');
            $table->timestamps();

            $table->foreign('payee_id')
                ->references('id')
                ->on('users');

            $table->foreign('payer_id')
                ->references('id')
                ->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction');
    }
}
