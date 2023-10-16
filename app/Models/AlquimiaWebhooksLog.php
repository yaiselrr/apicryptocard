<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class AlquimiaWebhooksLog extends Model
{

    public $table = 'alquimia_webhooks_logs';

    public $fillable = [
        'id_account',
        'alquimia_transaction_data'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id_account' => 'string',
        'alquimia_transaction_data' => 'string'
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
