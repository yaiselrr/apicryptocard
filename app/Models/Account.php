<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{

    public $table = 'accounts';


    public $fillable = [
        'card_number',
        'last8_digits',
        'balance',
        'active',
        'stolen',
        'collection_account',
        'id_webhook',
        'webhook_url',
        'id_account',
        'api_key',
        'activation_code',
        'client_id',
        'mother_card_id',
        'card_provider_id',
        'currency_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'card_number' => 'string',
        'last8_digits' => 'string',
        'balance' => 'float',
        'collection_account' => 'boolean',
        'id_webhook' => 'string',
        'webhook_url' => 'string',
        'id_account' => 'integer',
        'api_key' => 'string',
        'activation_code' => 'string',
        'client_id' => 'integer',
        'mother_card_id' => 'integer',
        'card_provider_id' => 'integer',
        'currency_id' => 'integer'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function motherCard()
    {
        return $this->belongsTo(MotherCard::class);
    }

    public function cardProvider()
    {
        return $this->belongsTo(CardsProvider::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

}
