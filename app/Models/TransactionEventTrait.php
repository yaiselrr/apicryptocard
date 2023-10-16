<?php

namespace App\Models;


use App\Http\Controllers\Helpers\HelperFunctions;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

trait TransactionEventTrait
{

    protected static function booted()
    {
        static::creating(function (Transaction $transaction) {
            $transaction->setPropertiesCreating($transaction);
        });

        static::created(function (Transaction $transaction) {
            $transaction->setPropertiesCreated($transaction);
        });

        static::updated(function (Transaction $transaction) {
            $transaction->setPropertiesUpdated($transaction);
        });
    }

    public function setPropertiesCreating(&$transaction)
    {
        $this->setBalanceProperties($transaction);
        $transaction->amount_ref = HelperFunctions::currencyToMxnChangePrice($transaction->currency_id, $transaction->amount);
        $transaction->send_amount = HelperFunctions::currencyToMxnChangePrice($transaction->currency_id, $transaction->amount);
        $user = Session::get('user');
        $transaction->user_id = $user ?  $user->id : null;
    }

    public function setPropertiesCreated($transaction)
    {
        if (!$transaction->future) {
            $this->setAccountProperties($transaction);
        }
    }

    public function setPropertiesUpdated($transaction)
    {
        //nada por ahora
    }

    public function setBalanceProperties(&$transaction)
    {
        $transactionType = TransactionType::find($transaction->transaction_type_id);
        $account = $transaction->account_id ? Account::find($transaction->account_id) : null;
        $transaction->balance_before_transaction = $account->balance;
        
        if ($transactionType->type == 'INCREMENT') {
            $transaction->balance_after_transaction = $account->balance + $transaction->amount;
        } elseif ($transactionType->type == 'DECREMENT') {
            $transaction->balance_after_transaction = $account->balance - $transaction->amount;
        }
    }

    public function setAccountProperties(&$transaction)
    {
        $account = Account::find($transaction->account_id);
        if ($account) {
            $account->balance = $transaction->balance_after_transaction;
            $account->save();
        }
    }

}