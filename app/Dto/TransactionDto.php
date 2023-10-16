<?php

namespace App\Dto;


use App\Http\Controllers\Helpers\HelperFunctions;
use App\Models\Account;
use App\Models\Client;
use App\Models\Transaction;
use App\Models\Currency;
use App\Models\TransactionType;
use Illuminate\Support\Facades\Session;

class TransactionDto
{
    use JsonSerializeTrait;

    public $id;
    public $date;
    public $amount;
    public $balance_before_transaction;
    public $balance_after_transaction;
    public $id_tx;
    public $mxn;
    public $usd;
    public $eur;
    public $concept;
    public $beneficiary;
    public $currency;
    public $provider_tracking;
    public $user;
    public $client;


    public function __construct(Transaction $transaction)
    {
        $type = TransactionType::find($transaction->transaction_type_id);
        $ben = Account::find($transaction->account_id);
        $cur = Currency::where('id', $transaction->currency_id)->first();
        $client = Client::where('id', $transaction->client_id)->first();
        $clientF = json_decode($client->user_json);
        $user = Session::get('user');
        
        $this->id = $transaction->id;
        $this->date = $transaction->date;
        $this->amount = $transaction->amount;
        $this->balance_before_transaction = $transaction->balance_before_transaction;
        $this->balance_after_transaction = $transaction->balance_after_transaction;
        $this->id_tx = $transaction->id_tx_alquimia;
        $this->mxn = $transaction->amount;
        $this->usd = HelperFunctions::currencyToMxnChangePrice(2, $transaction->amount);
        $this->eur = HelperFunctions::currencyToMxnChangePrice(3, $transaction->amount);
        $this->concept = ($type->id == 4 || $type->id == 5) ? $transaction->concepto :  $type->name;
        $this->beneficiary = $ben->last8_digits;
        $this->currency = $cur->abbreviation;
        $this->provider_tracking = $transaction->folio_orden_alquimia;
        $this->client = $clientF->name;
        
        if ($transaction->user_json == null) {
            $this->user = null;
        }else{
            $this->user = json_decode($transaction->user_json)->name;
        }
        
    }
}
