<?php

namespace App\Dto;


use App\Http\Controllers\Helpers\HelperFunctions;
use App\Models\Account;

class AccountBackDto
{
    use JsonSerializeTrait;
    
    public $id;
    public $card_number;
    public $balance;
    public $mxn;
    public $usd;
    public $eur;
    public $last8_digits;



    public function __construct(Account $account)
    {
        $this->id = $account->id;
        $this->card_number = $account->card_number;
        $this->balance = $account->balance;
        $this->mxn = $account->balance;
        $this->usd = HelperFunctions::currencyToMxnChangePrice(2, $account->balance);
        $this->eur = HelperFunctions::currencyToMxnChangePrice(3, $account->balance);
        $this->alias = "Ending ".substr($account->last8_digits, -4);
        $this->last8_digits = $account->last8_digits;
    }

}