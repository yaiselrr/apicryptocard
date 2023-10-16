<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMotherCardTransactionsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mother_card_transactions', function (Blueprint $table) {
            $table->increments('id');

            $table->string('id_transaccion');
            $table->string('concepto');
            $table->string('clave_rastreo');
            $table->datetime('fecha_alta');
            $table->decimal('monto',12,2)->nullable();
            $table->decimal('monto_ref',12,2)->nullable();
            $table->decimal('valor_real',12,2)->nullable();
            $table->integer('id_medio_pago');
            $table->text('alquimia_transaction_data');
            $table->decimal('balance_before_tx',12,2);
            $table->decimal('balance_after_tx',12,2);
            
            $table->unsignedInteger('transaction_type_id');
            $table->foreign('transaction_type_id')->references('id')->on('transaction_types')->onDelete('cascade');
            
            $table->unsignedInteger('mother_card_id');
            $table->foreign('mother_card_id')->references('id')->on('mother_cards')->onDelete('cascade');
            
            $table->unsignedInteger('currency_id');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');
            
            $table->string('beneficiary');

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
        Schema::drop('mother_card_transactions');
    }
}
