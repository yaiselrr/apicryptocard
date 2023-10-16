<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use TransactionEventTrait;

    public $table = 'transactions';

    public $fillable = [
        'send_amount',
        'tx_blockchain_ref',
        'date',
        'amount',
        'amount_ref',
        'balance_before_transaction',
        'balance_after_transaction',
        'id_tx_alquimia',
        'id_tx_vixipay',
        'no_referencia_alquimia',
        'folio_orden_alquimia',
        'concepto',
        'type',
        'state',
        'account_id',
        'client_id',
        'currency_id',
        'fee_id',
        'transaction_type_id',
        'user_id',
        'user_name',
        'send_amount_currency_id',
        'fee_amount',
        'user_json',
        'alquimia_data',
        'card_provider_id',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'send_amount' => 'decimal:2',
        'tx_blockchain_ref' => 'string',
        'date' => 'datetime',
        'amount' => 'decimal:2',
        'amount_ref' => 'decimal:2',
        'balance_before_transaction' => 'decimal:2',
        'balance_after_transaction' => 'decimal:2',
        'id_tx_alquimia' => 'string',
        'id_tx_vixipay' => 'string',
        'no_referencia_alquimia' => 'string',
        'folio_orden_alquimia' => 'string',
        'type' => 'string',
        'state' => 'string',
        'account_id' => 'integer',
        'client_id' => 'integer',
        'currency_id' => 'integer',
        'fee_id' => 'integer',
        'transaction_type_id' => 'integer',
        'user_id' => 'integer',
        'user_name' => 'string',
        'send_amount_currency_id' => 'integer',
        'fee_amount' => 'decimal:2',
        'alquimia_data' => 'string',
        'user_json' => 'json',
        'card_provider_id' => 'integer',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class,'account_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class,'client_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class,'currency_id');
    }

    public function fee()
    {
        return $this->belongsTo(Fee::class,'fee_id');
    }

    public function transactionType()
    {
        return $this->belongsTo(TransactionType::class,'transaction_type_id');
    }

    public function sendAmountCurrency()
    {
        return $this->belongsTo(Currency::class,'send_amount_currency_id');
    }

    public function provider()
    {
        return $this->belongsTo(CardsProvider::class,'card_provider_id');
    }
}
