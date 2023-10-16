<?php

namespace App\Repositories;


use App\Models\TemporalFile;
use \Prettus\Repository\Eloquent\BaseRepository as BaseRepository;

class TemporalFileRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TemporalFile::class;
    }


}
