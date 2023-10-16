<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Fee
 * @package App\Models
 * @version July 5, 2022, 7:34 pm EST
 *
 * @property number $fee_card_reload
 * @property number $fee_card_tx
 * @property number $fee_first_tx
 * @property boolean $active
 */
class Fee extends Model
{

    public $table = 'fees';
    


    public $fillable = [
        'fee_card_reload',
        'fee_card_tx',
        'fee_first_tx',
        'active'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'fee_card_reload' => 'float',
        'fee_card_tx' => 'float',
        'fee_first_tx' => 'float',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
