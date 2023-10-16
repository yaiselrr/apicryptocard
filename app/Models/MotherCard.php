<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class MotherCard extends Model
{

    public $table = 'mother_cards';


    public $fillable = [
        'id_account',
        'api_key',
        'card_number',
        'balance',
        'card_provider_id',
        'currency_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id_account' => 'integer',
        'api_key' => 'string',
        'card_number' => 'string',
        'balance' => 'float',
        'card_provider_id' => 'integer',
        'currency_id' => 'integer'
    ];

    public function cardProvider()
    {
        return $this->belongsTo(CardsProvider::class, 'card_provider_id');
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function mother_card_transactions()
    {
        return $this->hasMany(MotherCardTransaction::class, 'mother_card_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class,'currency_id');
    }
}
