<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Client extends Model
{

    public $table = 'clients';

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public $fillable = [
        'zip_code',
        'user_id',
        'user_json',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'zip_code' => 'string',
        'user_id' => 'integer',
        'user_json' => 'json',
    ];

    /**
     * Validation rules
     *
     * return array
     */
    public static function rules($id)
    {
        return [
            // 'zip_code' => 'required',
            'user_id' => 'required'
        ];
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
