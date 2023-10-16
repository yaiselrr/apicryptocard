<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class AlquimiaLog extends Model
{

    public $table = 'alquimia_logs';

    public $fillable = [
        'endpoint',
        'params',
        'wso2_token',
        'alquimia_token',
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
        'wso2_token' => 'string',
        'alquimia_token' => 'string',
        'response' => 'string'
    ];

    /**
     * Validation rules
     *
     * return array
     */
    public static function rules($id)
    {
        return [

        ];
    }

}
