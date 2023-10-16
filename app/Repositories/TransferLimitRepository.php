<?php

namespace App\Repositories;

use App\Dto\LimitDto;
use App\Models\TransferLimit;
use App\Http\Controllers\Helpers\HelperFunctions;
use \Prettus\Repository\Eloquent\BaseRepository as BaseRepository;
use Illuminate\Support\Facades\Session;

class TransferLimitRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'limit_card_reload',
        'limit_card_tx',
        'limit_first_tx',
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TransferLimit::class;
    }

    public function filtro($filter = [], $orderBy = 'created_at', $direction = 'ASC', $paginate = 10){
        $query = TransferLimit::query();
        $query = HelperFunctions::construirQuerysFiltros($filter, $orderBy, $direction, $query);
        return $query->paginate($paginate);
    }

    public function getUser()
    {
        $user = Session::get('user');

        return $user->id;
    }

    public function activeLimit()
    {
        $limit = TransferLimit::where('active', 1)->first();

        return new LimitDto($limit);
    }

    public function listLimitsAll()
    {
        $limits = TransferLimit::all();
        $result = [];

        foreach ($limits as $key => $limit) {
            $result[] = new LimitDto($limit);
        }

        return $result;
    }
}
