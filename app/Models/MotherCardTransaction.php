<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MotherCardTransaction extends Model
{
    use MotherCardTransactionEventTrait;

    public $table = 'mother_card_transactions';

    public $fillable = [
        'id_transaccion',
        'concepto',
        'clave_rastreo',
        'fecha_alta',
        'monto',
        'monto_ref',
        'valor_real',
        'id_medio_pago',
        'alquimia_transaction_data',
        'balance_before_tx',
        'balance_after_tx',
        'transaction_type_id',
        'mother_card_id',
        'currency_id',
        'beneficiary'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id_transaccion' => 'string',
        'concepto' => 'string',
        'clave_rastreo' => 'string',
        'monto' => 'decimal:2',
        'monto_ref' => 'decimal:2',
        'valor_real' => 'decimal:2',
        'id_medio_pago' => 'string',
        'alquimia_transaction_data' => 'string',
        'balance_before_tx' => 'decimal:2',
        'balance_after_tx' => 'decimal:2',
        'transaction_type_id' => 'integer',
        'mother_card_id' => 'integer',
        'currency_id' => 'integer',
        'beneficiary' => 'string'
    ];

    public function mother_card()
    {
        return $this->belongsTo(MotherCard::class, 'mother_card_id');
    }

    public function transactionType()
    {
        return $this->belongsTo(TransactionType::class, 'transaction_type_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class,'currency_id');
    }
}
