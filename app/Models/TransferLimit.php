<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class TransferLimit
 * @package App\Models
 * @version September 21, 2022, 9:30 am CDT
 *
 * @property number $limit_card_reload
 * @property number $limit_card_tx
 * @property number $limit_first_tx
 * @property boolean $active
 */
class TransferLimit extends Model
{

    public $table = 'transfer_limits';
    


    public $fillable = [
        'limit_card_reload',
        'limit_card_tx',
        'limit_first_tx',
        'active'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'limit_card_reload' => 'decimal:2',
        'limit_card_tx' => 'decimal:2',
        'limit_first_tx' => 'decimal:2'
    ];
}
