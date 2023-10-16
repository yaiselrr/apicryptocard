<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class VixipayServiceLog
 * @package App\Models
 * @version July 12, 2022, 9:31 pm EST
 *
 * @property string $endpoint
 * @property string $params
 * @property string $response
 */
class VixipayServiceLog extends Model
{

    public $table = 'vixipay_service_logs';
    


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
            // 'endpoint' => 'required'
        ];
    }
}
