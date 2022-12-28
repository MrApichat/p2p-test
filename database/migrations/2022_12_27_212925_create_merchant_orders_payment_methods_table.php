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
        Schema::create('merchant_orders_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->integer('payment_method_id')->unsigned();
            $table->integer('merchant_order_id')->unsigned();
            $table->timestamps();
            $table->foreign('merchant_order_id')->references('id')->on('merchant_orders');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchant_orders_payment_methods');
    }
};
