<?php

namespace App\Dto;


use App\Http\Controllers\Helpers\HelperFunctions;
use App\Models\Account;
use App\Models\Client;
use App\Models\Currency;
use App\Dto\TransactionDto;
use App\Models\Transaction;

class CollectionAccountDto
{
    use JsonSerializeTrait;

    public $id;
    public $card_number;
    public $balance;
    public $balanceLoadCard;
    public $mxn;
    public $usd;
    public $eur;
    public $movements;
    public $stolen;
    public $load;
    public $user;
    public $currency;
    public $txs;
    public $activation_date;
    public $total_deposited;

    public function __construct(Account $accountDto)
    {
        $this->movements = [];
        $cont = 0;
        $totalDeposited = 0;

        if ($accountDto->client_id == null) {
            $this->user = $accountDto->client_id;
        } else {
            $client = Client::where('id', $accountDto->client_id)->first();
            $clientF = json_decode($client->user_json);
            $this->user = $clientF->name;
        }

        $transactions = Transaction::where('account_id', $accountDto->id)->where('transaction_type_id',1)->get();

        foreach ($transactions as $transaction) {
            $totalDeposited+= $transaction->amount;
        }
        
        $cur = Currency::where('id', $accountDto->currency_id)->first();
        
        $this->card_number = $accountDto->card_number;
        $this->id = $accountDto->id;
        $this->balance = $accountDto->balance;
        $this->mxn = $accountDto->balance;
        $this->usd = HelperFunctions::currencyToMxnChangePrice(2, $accountDto->balance);
        $this->eur = HelperFunctions::currencyToMxnChangePrice(3, $accountDto->balance);
        $this->stolen = $accountDto->stolen;
        $this->currency = $cur->abbreviation;
        $this->txs = $cont;
        $this->activation_date = $accountDto->created_at;
        $this->total_deposited = $totalDeposited;
        $this->alias = "Ending ".substr($accountDto->last8_digits, -4);
        $this->balanceLoadCard = $cur->abbreviation." ".$accountDto->balance;

    }
}
