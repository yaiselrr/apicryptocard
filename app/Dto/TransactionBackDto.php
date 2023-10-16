<?php

namespace App\Dto;


use App\Http\Controllers\Helpers\HelperFunctions;
use App\Models\Transaction;
use App\Models\Currency;
use App\Models\TransactionType;

class TransactionBackDto
{
    use JsonSerializeTrait;

    public $id;
    public $date;
    public $amount;
    public $usd;
    public $id_tx;
    public $eur;
    public $concept;
    public $currency;

    public function __construct(Transaction $transactionDto)
    {
        $type = TransactionType::find($transactionDto->transaction_type_id);
        $cur = Currency::where('id',$transactionDto->currency_id)->first();

        $this->id = $transactionDto->id;
        $this->date = $transactionDto->date;
        $this->amount = $transactionDto->amount;
        $this->usd = HelperFunctions::currencyToMxnChangePrice(2, $transactionDto->amount);
        $this->eur = HelperFunctions::currencyToMxnChangePrice(3, $transactionDto->amount);
        $this->id_tx = $transactionDto->id_tx_alquimia;
        $this->concept = $type->name;
        $this->currency = $cur->abbreviation;
        $this->no_referencia_alquimia = $cur->no_referencia_alquimia;
    }

}