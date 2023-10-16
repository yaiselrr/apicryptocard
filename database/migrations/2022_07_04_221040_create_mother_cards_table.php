<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMotherCardsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mother_cards', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('id_account');

            $table->string('api_key');

            $table->string('card_number');

            $table->decimal('balance',9,2);            
            
            $table->unsignedInteger('card_provider_id');
            $table->foreign('card_provider_id')->references('id')->on('cards_providers')->onDelete('cascade');

            $table->unsignedInteger('currency_id');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');

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
        Schema::drop('mother_cards');
    }
}
