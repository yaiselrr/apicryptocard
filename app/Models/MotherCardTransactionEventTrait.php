<?php

namespace App\Models;


use App\Http\Controllers\Helpers\HelperFunctions;

trait MotherCardTransactionEventTrait
{

    protected static function booted()
    {
        static::creating(function (MotherCardTransaction $transaction) {
            $transaction->setPropertiesCreating($transaction);
        });

        static::created(function (MotherCardTransaction $transaction) {
            $transaction->setPropertiesCreated($transaction);
        });
    }

    public function setPropertiesCreating(&$transaction)
    {
        $this->setBalanceProperties($transaction);
        $refAmount = HelperFunctions::currencyToCustomChangePrice($transaction->currency_id, 2, $transaction->monto);
        $transaction->monto_ref = $refAmount;
    }

    public function setPropertiesCreated($transaction)
    {
        $this->setMotherCardProperties($transaction);
    }

    public function setBalanceProperties(&$transaction)
    {


//        $transactionType = TransactionType::find($transaction->transaction_type_id);

        $motherCard = $transaction->mother_card_id ? MotherCard::find($transaction->mother_card_id) : null;
        $transaction->balance_before_tx = $motherCard->balance;
        $transaction->balance_after_tx = $motherCard->balance + $transaction->valor_real;

//        if ($transactionType->type == 'INCREMENT') {
//            $transaction->balance_after_tx = $motherCard->balance + $transaction->monto;
//        } elseif ($transactionType->type == 'DECREMENT') {
//            $transaction->balance_after_tx = $motherCard->balance - $transaction->monto;
//        }
    }

    public function setMotherCardProperties(&$transaction)
    {
        $MotherCard = MotherCard::find($transaction->mother_card_id);
        if ($MotherCard) {
            $MotherCard->balance = $transaction->balance_after_tx;
            $MotherCard->save();
        }
    }

}