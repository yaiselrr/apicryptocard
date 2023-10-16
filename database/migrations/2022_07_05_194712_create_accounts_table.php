<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->increments('id');

            $table->string('card_number')->unique();
            $table->string('last8_digits');

            $table->decimal('balance',10,2);

            $table->boolean('active')->default(false);
            $table->boolean('stolen')->default(false);
            $table->boolean('collection_account')->default(false);

            $table->string('id_webhook')->nullable();
            $table->string('webhook_url')->nullable();

            $table->string('activation_code')->nullable();

            $table->integer('id_account')->nullable();            
            $table->string('api_key')->nullable();

            $table->unsignedInteger('client_id')->nullable();
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            
            $table->unsignedInteger('mother_card_id')->nullable();
            $table->foreign('mother_card_id')->references('id')->on('mother_cards')->onDelete('cascade');
            
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
        Schema::drop('accounts');
    }
}
