<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlquimiaWebhooksLogsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alquimia_webhooks_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('id_account');
            $table->text('alquimia_transaction_data');


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
        Schema::drop('alquimia_webhooks_logs');
    }
}
