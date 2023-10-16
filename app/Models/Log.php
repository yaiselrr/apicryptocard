<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Log extends Model
{
    public $table = 'logs';

    protected $fillable = [
        'description',
        'petition',
        'error',
        'parameters',
        'user_id',
        'user_json',
    ];


}
