<?php

namespace App\Repositories;

use App\Models\MotherCard;
use App\Http\Controllers\Helpers\HelperFunctions;
use \Prettus\Repository\Eloquent\BaseRepository as BaseRepository;
use Illuminate\Support\Facades\Session;
use App\Models\Client;

class MotherCardRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id_account',
        'card_number',
        'balance',
        'card_provider_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return MotherCard::class;
    }

    public function filtro($filter = [], $orderBy = 'created_at', $direction = 'ASC', $paginate = 10){
        $query = MotherCard::query();
        $query = HelperFunctions::construirQuerysFiltros($filter, $orderBy, $direction, $query);
        return $query->paginate($paginate);
    }

    public function getClient()
    {
        $user = Session::get('user');
        $client = Client::where('user_id', $user->id)->first();

        return $client->id;
    }
}
