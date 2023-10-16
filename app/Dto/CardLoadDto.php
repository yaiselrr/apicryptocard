<?php

namespace App\Dto;


use App\Models\Account;
use App\Models\Client;
use App\Models\Transaction;
use App\Models\Currency;
use App\Models\Fee;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class CardLoadDto
{
    use JsonSerializeTrait;
    
    public $id;
    public $tx_id;
    public $timestamp;
    public $email;
    public $card_number;
    public $origin_currency;
    public $amt_sent;
    public $fee_billed_to_client;
    public $amt_to_be_load;
    public $amt_ref_usd;
    public $load_currency;
    public $amt_load;
    public $status;

    public function __construct(Transaction $transactionDto)
    {
        if ($transactionDto->send_amount_currency_id == null) {
            $this->origin_currency = null;
        }else{
            $cur = Currency::where('id',$transactionDto->send_amount_currency_id)->first();
            $this->origin_currency = $cur->abbreviation;
        }

        $curLoad = Currency::where('id',1)->first();
        $account = Account::find($transactionDto->account_id);
        $user = Session::get('user');
        $transactionAux = Transaction::where('id_tx_alquimia',$transactionDto->id_tx_alquimia)->first();
        
        if ($transactionDto->fee_id == null) {
            $this->fee_billed_to_client = null;
        }else{
            $getFee = Fee::find($transactionDto->fee_id);
            $this->fee_billed_to_client = $getFee->fee_card_reload;
        }
        if ($transactionDto->user_json == null) {
            $this->email = null;
        }else{
            $this->email = json_decode($transactionDto->user_json)->email;
        }        

        // $this->id = $transactionDto->id;
        $this->id = $transactionAux->id;
        $this->tx_id = $transactionDto->no_referencia_alquimia;
        $this->timestamp = $transactionDto->date;
        $this->card_number = $account->card_number;
        $this->amt_sent = $transactionDto->send_amount;        
        $this->amt_to_be_load = $transactionDto->send_amount;
        $this->amt_ref_usd = $transactionDto->amount_ref;
        $this->load_currency = $curLoad->abbreviation;
        $this->amt_load = $transactionDto->amount;
        $this->status = $transactionDto->state;
    }

}