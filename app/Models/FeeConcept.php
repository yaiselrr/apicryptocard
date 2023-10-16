<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeConcept extends Model
{

    public $table = 'fee_concepts';
    


    public $fillable = [
        'name',
        'fee'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'fee' => 'decimal:2'
    ];
}
