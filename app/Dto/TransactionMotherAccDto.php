<?php

namespace App\Dto;

use App\Http\Controllers\Helpers\HelperFunctions;
use App\Models\Account;
use App\Models\Currency;
use App\Models\MotherCard;
use App\Models\MotherCardTransaction;
use App\Models\Transaction;

class TransactionMotherAccDto
{
    use JsonSerializeTrait;

    public $id;
    public $date;
    // public $amount;
    public $tx_amount_in_mxn;
    public $tx_amount_in_usd;
    public $balance_before_transaction;
    public $balance_after_transaction;
    public $reference_balance_usd;
    public $id_tx;
    public $mxn;
    public $usd;
    public $concept;
    public $beneficiary_id;
    public $beneficiary_username;
    public $currency;
    public $provider_tracking_id;


    public function __construct(MotherCardTransaction $transaction)
    {
        $mother = MotherCard::find($transaction->mother_card_id);
        $cur = Currency::where('id', $transaction->currency_id)->first();
        $account = Account::find($transaction->mother_card_id);

        $this->id = $transaction->id;
        $this->date = $transaction->fecha_alta;
        // $this->amount = $transaction->monto;
        $this->tx_amount_in_mxn = $transaction->monto;
        $this->balance_before_transaction = $transaction->balance_before_tx;
        $this->balance_after_transaction = $transaction->balance_after_tx;
        $this->reference_balance_usd = HelperFunctions::currencyToMxnChangePrice(2, $transaction->balance_after_tx);
        $this->id_tx = $transaction->id_transaccion;
        $this->mxn = $transaction->valor_real;
        $this->usd = $transaction->monto_ref;
        $this->tx_amount_in_usd = $transaction->monto_ref;
        $this->concept = $transaction->concepto;
        // $this->beneficiary = substr($mother->card_number, -8);

        $claveRastreo = $transaction->clave_rastreo;
        $clientTransaction = Transaction::where('no_referencia_alquimia', $claveRastreo)->first();
        if (!$clientTransaction) {
            $this->beneficiary_id = substr($account->card_number, -8);
            $this->beneficiary_username = "Alquimia Mother Account";
        } else {
            $clientF = json_decode($clientTransaction->client->user_json);
            $this->beneficiary_id = $clientTransaction->client->user_id;
            $this->beneficiary_username = $clientF->name;
        }
        $this->currency = $cur->abbreviation;
        $this->provider_tracking_id = $transaction->clave_rastreo;
    }

}