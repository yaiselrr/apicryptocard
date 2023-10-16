<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlquimiapayTokensTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alquimiapay_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('type',['WSO2', 'ALQUIMIA'])->default('WSO2');
            $table->text('token');

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
        Schema::drop('alquimiapay_tokens');
    }
}
