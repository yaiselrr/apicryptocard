<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class ExchangerProvider extends Model
{

    protected $table = 'exchange_providers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description'
    ];


    protected $casts = [
        'name' => 'string',
        'description' => 'string',
    ];


    public function price_exchanger_currecies()
    {
        return $this->hasMany(PriceExchangerCurrency::class);
    }

    public function price_exchanger_cryptocurrecies()
    {
        return $this->hasMany(PriceExchangerCrypto::class);
    }

}
