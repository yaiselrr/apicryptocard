<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');

            $table->decimal('send_amount', 9, 2)->nullable();

            $table->text('tx_blockchain_ref')->nullable();

            $table->datetime('date')->nullable();

            $table->decimal('amount', 9, 2)->nullable();
            $table->decimal('amount_ref', 9, 2)->nullable();
            $table->decimal('balance_before_transaction', 9, 2)->nullable();
            $table->decimal('balance_after_transaction', 9, 2)->nullable();

            $table->string('id_tx_alquimia')->nullable();
            $table->string('id_tx_vixipay')->nullable();
            $table->string('no_referencia_alquimia')->nullable();
            $table->string('folio_orden_alquimia')->nullable();
            $table->string('concepto')->nullable();

            $table->enum('type', ['INCREMENT', 'DECREMENT'])->default('INCREMENT');
            $table->enum('state', ['PENDING', 'PROCESED', 'CANCELLED', 'ERROR'])->default('PROCESED');

            $table->decimal('fee_amount', 9, 2)->nullable();
            $table->text('alquimia_data')->nullable();

            $table->unsignedInteger('account_id');
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');

            $table->unsignedInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');

            $table->unsignedInteger('currency_id');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('cascade');

            $table->unsignedInteger('fee_id')->nullable();
            $table->foreign('fee_id')->references('id')->on('fees')->onDelete('cascade');

            $table->unsignedInteger('transaction_type_id');
            $table->foreign('transaction_type_id')->references('id')->on('transaction_types')->onDelete('cascade');

            $table->unsignedInteger('card_provider_id')->nullable();
            $table->foreign('card_provider_id')->references('id')->on('cards_providers')->onDelete('cascade');

            $table->unsignedInteger('user_id')->nullable();
            $table->text('user_name')->nullable();
            $table->text('user_json')->nullable();

            $table->unsignedInteger('send_amount_currency_id')->nullable();
            $table->foreign('send_amount_currency_id')->references('id')->on('currencies')->onDelete('cascade');

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
        Schema::drop('transactions');
    }
}
