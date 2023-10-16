<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlquimiapayToken extends Model
{

    public $table = 'alquimiapay_tokens';

    public $fillable = [
        'type',
        'token'
    ];

    protected $casts = [
        'type' => 'string',
        'token' => 'string'
    ];

    /**
     * Validation rules
     *
     * return array
     */
    public static function rules($id)
    {
        return [
            'type' => 'required',
            'token' => 'required'
        ];
    }

}
