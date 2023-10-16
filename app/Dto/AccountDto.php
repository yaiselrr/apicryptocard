<?php

namespace App\Dto;


use App\Http\Controllers\Helpers\HelperFunctions;
use App\Models\Account;
use App\Models\Client;
use App\Models\Currency;
use App\Dto\TransactionDto;
use App\Models\Transaction;

class AccountDto
{
    use JsonSerializeTrait;

    // public $id;
    public $user_id;
    public $user_name;
    public $card_number;
    // public $balance;
    public $current_balance_in_mxn;
    public $current_balance_in_usd;
    public $current_balance_in_eur;
    public $balanceLoadCard;
    public $mxn;
    // public $usd;
    public $eur;
    public $movements;
    public $stolen;
    public $load;
    public $user;
    public $currency;
    public $txs;
    public $card_activation_date;
    public $total_deposited_in_mxn;
    public $total_deposited_in_usd;
    public $total_deposited_in_eur;
    public $last4_digits;
    public $last8_digits;
    // public $actual_id;

    public function __construct(Account $accountDto, $dataMovimientos)
    {
        $this->movements = [];
        $movementsAux = [];
        $cont = 0;
        $totalDeposited = 0;
        $auxId = Account::where('card_number',$accountDto->card_number)->first();

        foreach ($dataMovimientos as $movimiento) {
            $movementsAux[] = new TransactionDto($movimiento);
            $cont++;
        }

        if ($accountDto->client_id == null) {
            $this->user = $accountDto->client_id;
            $this->user_name = $accountDto->client_id;
        } else {
            $client = Client::where('id', $accountDto->client_id)->first();
            $clientF = json_decode($client->user_json);
            $this->user = $clientF->email;
            $this->user_name = $clientF->name;
        }

        $transactions = Transaction::where('account_id', $accountDto->id)->orWhere('transaction_type_id',1)->orWhere('transaction_type_id',3)->orWhere('transaction_type_id',4)->get();

        foreach ($transactions as $transaction) {
            $totalDeposited+= $transaction->amount;
        }
        
        $cur = Currency::where('id', $accountDto->currency_id)->first();

        $this->movements = array_reverse($movementsAux);
        $this->card_number = $accountDto->card_number;
        // $this->id = $accountDto->id;
        // $this->id = $auxId->id;
        $this->user_id = $auxId->id;
        $this->current_balance_in_mxn = $accountDto->balance;
        $this->mxn = $accountDto->balance;
        $this->current_balance_in_usd = HelperFunctions::currencyToMxnChangePrice(2, $accountDto->balance);
        $this->current_balance_in_eur = HelperFunctions::currencyToMxnChangePrice(3, $accountDto->balance);
        $this->stolen = $accountDto->stolen;
        $this->currency = $cur->abbreviation;
        $this->txs = $cont;
        $this->card_activation_date = $accountDto->created_at;
        $this->total_deposited_in_mxn = $totalDeposited;
        $this->total_deposited_in_usd = HelperFunctions::currencyToMxnChangePrice(2, $totalDeposited);
        $this->total_deposited_in_eur = HelperFunctions::currencyToMxnChangePrice(3, $totalDeposited);
        $this->alias = "Ending ".substr($accountDto->last8_digits, -4);
        $this->balanceLoadCard = $cur->abbreviation." ".$accountDto->balance;
        $this->last4_digits = substr($accountDto->last8_digits, -4);
        $this->last8_digits = $accountDto->last8_digits;
        $this->actual_id = $auxId->id;

    }
}
