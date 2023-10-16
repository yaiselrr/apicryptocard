<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class PriceExchangerCurrency extends Model
{

    public $table = 'price_exchanger_currencies';

    public $fillable = [
        'price',
        'exchanger_provider_id',
        'currency_id'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'real',
        'exchanger_provider_id' => 'integer',
        'currency_id' => 'integer'
    ];

    /**
     * Validation rules
     *
     * return array
     */
    public static function rules($id)
    {
        return [
            'price' => 'required',
            'exchanger_provider_id' => 'required',
            'currency_id' => 'required'
        ];
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function exchanger_provider()
    {
        return $this->belongsTo(ExchangerProvider::class);
    }

}
