<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{

    public $table = 'currencies';

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public $fillable = [
        'name',
        'abbreviation',
        'crypto',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'abbreviation' => 'string',
        'crypto' => 'boolean'
        
    ];

    /**
     * Validation rules
     *
     * return array
     */
    public static function rules($id)
    {
        return [
            'name' => 'required',
            'abbreviation' => 'required',
            'crypto' => 'required'
        ];
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function mother_card_transactions()
    {
        return $this->hasMany(MotherCardTransaction::class);
    }

    public function mother_cards()
    {
        return $this->hasMany(MotherCard::class);
    }

    public function price_exchanger_cryptocurrecies()
    {
        return $this->hasMany(PriceExchangerCrypto::class);
    }
}
