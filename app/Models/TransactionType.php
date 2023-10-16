<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TransactionType
 * @package App\Models
 * @version July 5, 2022, 11:14 pm EST
 *
 * @property string $name
 * @property string $type
 */
class TransactionType extends Model
{

    public $table = 'transaction_types';
    


    public $fillable = [
        'name',
        'type'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'type' => 'string'
    ];

    /**
     * Validation rules
     *
     * return array
     */
    public static function rules ($id){
        return [
            'name' => 'required'
        ];
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function mother_card_transactions()
    {
        return $this->hasMany(MotherCardTransaction::class, 'transaction_type_id');
    }
}
