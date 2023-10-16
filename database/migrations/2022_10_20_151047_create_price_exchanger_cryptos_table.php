<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePriceExchangerCryptosTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_exchanger_cryptos', function (Blueprint $table) {
            $table->increments('id');

            $table->decimal('price', 30 ,9);

            $table->unsignedInteger('exchanger_provider_id');
            $table->unsignedInteger('ccy1');
            $table->unsignedInteger('ccy2');

            $table->foreign('exchanger_provider_id')->references('id')->on('exchange_providers')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('ccy1')->references('id')->on('currencies')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('ccy2')->references('id')->on('currencies')->onUpdate('cascade')->onDelete('cascade');

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
        Schema::drop('price_exchanger_cryptos');
    }
}
