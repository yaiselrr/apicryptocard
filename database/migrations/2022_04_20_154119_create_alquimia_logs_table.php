<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAlquimiaLogsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alquimia_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('endpoint');
            $table->text('params');
            $table->text('wso2_token');
            $table->text('alquimia_token');
            $table->text('response');

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
        Schema::drop('alquimia_logs');
    }
}
