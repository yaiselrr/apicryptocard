<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class XeLog extends Model
{

    public $table = 'xe_logs';

    public $fillable = [
        'success',
        'content',
        'error_message',
        'status',
        'error_code',
        'x-ratelimit-limit',
        'x-ratelimit-remaining',
        'x-ratelimit-reset',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'success' => 'boolean',
        'content' => 'string',
        'error_message' => 'string',
        'status' => 'integer',
        'error_code' => 'integer',
        'x-ratelimit-limit' => 'integer',
        'x-ratelimit-remaining' => 'integer',
        'x-ratelimit-reset' => 'integer'
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
