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
        Schema::create('merchant_orders', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->integer('fiat_id')->unsigned();
            $table->integer('coin_id')->unsigned();
            $table->integer('merchant_id')->unsigned();
            $table->float('price');
            $table->float('available_coin');
            $table->float('lower_limit');
            $table->string('status');
            $table->timestamps();
            $table->foreign('fiat_id')->references('id')->on('currencies');
            $table->foreign('coin_id')->references('id')->on('currencies');
            $table->foreign('merchant_id')->references('id')->on('users');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchant_orders');
    }
};
