<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePriceExchangerCurrenciesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_exchanger_currencies', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('price', 30 ,9);
            $table->unsignedInteger('exchanger_provider_id');
            $table->unsignedInteger('currency_id');

            $table->foreign('exchanger_provider_id')->references('id')->on('exchange_providers')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('currency_id')->references('id')->on('currencies')->onUpdate('cascade')->onDelete('cascade');

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
        Schema::drop('price_exchanger_currencies');
    }
}
