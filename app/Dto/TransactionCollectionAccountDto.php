<?php

namespace App\Dto;


use App\Http\Controllers\Helpers\HelperFunctions;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\Currency;
use App\Models\TransactionType;
use Illuminate\Support\Facades\Session;

class TransactionCollectionAccountDto
{
    use JsonSerializeTrait;

    public $id;
    public $timestamp;
    // public $amount_transfer;
    public $tx_amount_in_mxn;
    public $tx_amount_in_usd;
    public $tx_amount_in_eur;
    public $reference_balance_mxn;
    public $reference_balance_usd;
    // public $balance_after_transaction;
    // public $id_tx;
    // public $mxn;
    public $ref_usd;
    public $ref_eurr;
    public $concept;
    public $beneficiary_id;
    public $currency;
    // public $provider_tracking;
    // public $user;
    public $beneficiary_username;


    public function __construct(Transaction $transaction)
    {
        $type = TransactionType::find($transaction->transaction_type_id);
        $ben = Account::find($transaction->account_id);
        $cur = Currency::where('id',$transaction->currency_id)->first();

        if ($transaction->user_json == null) {
            // $this->user = null;
            $this->beneficiary_username = null;
        }else{
            $this->beneficiary_username = json_decode($transaction->user_json)->name;
            // $this->user = json_decode($transaction->user_json)->name;
        } 

        $this->id = $transaction->id;
        $this->timestamp = $transaction->date;
        $this->last4_digits = substr($ben->last8_digits, -4);
        // $this->amount_transfer = $transaction->amount;
        $this->tx_amount_in_mxn = $transaction->amount;
        $this->balance_before_transaction = $transaction->balance_before_transaction;
        $this->reference_balance_mxn = $transaction->balance_after_transaction;
        $this->reference_balance_usd = HelperFunctions::currencyToMxnChangePrice(2, $transaction->balance_after_transaction);
        // $this->mxn = $transaction->amount;
        $this->tx_amount_in_usd = HelperFunctions::currencyToMxnChangePrice(2, $transaction->amount);
        $this->tx_amount_in_eur = HelperFunctions::currencyToMxnChangePrice(3, $transaction->amount);
        $this->concept = ($type->id == 4 || $type->id == 5) ? $transaction->concepto :  $type->name;
        $this->beneficiary_id = $ben->last8_digits;
        $this->currency = $cur->abbreviation;
    }

}