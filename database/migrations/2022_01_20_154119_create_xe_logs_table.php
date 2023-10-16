<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateXeLogsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xe_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('success');
            $table->text('content');
            $table->string('error_message');
            $table->integer('status');
            $table->integer('error_code');
            $table->integer('x-ratelimit-limit');
            $table->integer('x-ratelimit-remaining');
            $table->integer('x-ratelimit-reset');

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
        Schema::drop('xe_logs');
    }
}
