<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceExchangerCrypto extends Model
{

    public $table = 'price_exchanger_cryptos';
    


    public $fillable = [
        'price',
        'exchanger_provider_id',
        'ccy1',
        'ccy2'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'real',
        'exchanger_provider_id' => 'integer',
        'ccy1' => 'integer',
        'ccy2' => 'integer'
    ];

    /**
     * Validation rules
     *
     * return array
     */
    public static function rules ($id){
        return [
            
        ];
    }

    public function currency1()
    {
        return $this->belongsTo(Currency::class,'ccy1');
    }

    public function currency2()
    {
        return $this->belongsTo(Currency::class,'ccy2');
    }

    public function exchanger_provider()
    {
        return $this->belongsTo(ExchangerProvider::class,'exchanger_provider_id');
    }
}
