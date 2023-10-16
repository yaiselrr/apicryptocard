<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class BitfinexLog
 * @package App\Models
 * @version October 20, 2022, 2:53 pm CDT
 *
 * @property string $endpoint
 * @property string $params
 * @property string $response
 */
class BitfinexLog extends Model
{

    public $table = 'bitfinex_logs';
    


    public $fillable = [
        'endpoint',
        'params',
        'response'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'endpoint' => 'string',
        'params' => 'string',
        'response' => 'string'
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
}
