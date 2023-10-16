<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *      schema="TemporalFile",
 *      title="TemporalFile",
 *      required={"url", "modelo"},
 *      @OA\Property(
 *          property="id",
 *          description="id",
 *          type="integer",
 *          format="int32"
 *      ),
 *      @OA\Property(
 *          property="url",
 *          description="url",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="model",
 *          description="model",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="created_at",
 *          description="created_at",
 *          type="string",
 *          format="date-time"
 *      ),
 *      @OA\Property(
 *          property="updated_at",
 *          description="updated_at",
 *          type="string",
 *          format="date-time"
 *      )
 * )
 */
class TemporalFile extends Model
{

    public $table = 'temporal_files';
    public $appends = ['original_url'];


    public $fillable = [
        'url',
        'model'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'url' => 'string',
        'model' => 'string'
    ];

    /**
     * Validation rules
     *
     * return array
     */
    public static function rules($id)
    {
        return [
            'url' => 'required|unique',
            'model' => 'required'
        ];
    }

    public function getUrlAttribute($value)
    {
        return \Storage::disk('public')->url($value);
    }

    public function getOriginalUrlAttribute()
    {
        return $this->attributes['url'];
    }
}
