<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class CardsProvider
 * @package App\Models
 * @version July 4, 2022, 10:05 pm EST
 *
 * @property string $name
 */
class CardsProvider extends Model
{

    public $table = 'cards_providers';
    


    public $fillable = [
        'name'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string'
    ];

    public function motherCards(){

        return $this->hasMany(MotherCard::class);
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
