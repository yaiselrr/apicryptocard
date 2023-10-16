<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransferLimitsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transfer_limits', function (Blueprint $table) {
            $table->increments('id');

            $table->decimal('limit_card_reload',9,2);
            $table->decimal('limit_card_tx',9,2);
            $table->decimal('limit_first_tx',9,2);

            $table->boolean('active')->default(true);

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
        Schema::drop('transfer_limits');
    }
}
