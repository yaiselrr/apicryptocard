<?php

namespace App\Repositories;

use App\Dto\FeeConceptDto;
use App\Exceptions\CustomException;
use App\Models\FeeConcept;
use App\Http\Controllers\Helpers\HelperFunctions;
use \Prettus\Repository\Eloquent\BaseRepository as BaseRepository;
use Illuminate\Support\Facades\Session;

class FeeConceptRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name',
        'fee'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return FeeConcept::class;
    }

    public function filtro($filter = [], $orderBy = 'created_at', $direction = 'ASC', $paginate = 10)
    {
        $query = FeeConcept::query();
        $query = HelperFunctions::construirQuerysFiltros($filter, $orderBy, $direction, $query);
        return $query->paginate($paginate);
    }

    public function getUser()
    {
        $user = Session::get('user');

        return $user->id;
    }

    public function listAll()
    {
        $result = [];
        $concepts = FeeConcept::paginate(50);

        foreach ($concepts as $key => $concept) {
            $result[] = new FeeConceptDto($concept,0);
        }

        return $result;
    }

    public function getFeeFromAmountByConcept($amount, $concept_id)
    {
        if ($amount <= 0) {
            throw new CustomException("Amount Error: The value must be greater than one");
        }
        if (!$this->conceptExist($concept_id)) {
            throw new CustomException("Concept Fee Error: The concept does not exist");
        }

        return $this->getConceptFee($concept_id, $amount);
    }

    public function getFeeByConcept($concept_id)
    {
        if (!$this->conceptExistDiferentRefund($concept_id)) {
            throw new CustomException("Concept Fee Error: The concept does not exist");
        }

        return $this->getConceptFee($concept_id);
    }

    public function conceptExist($concept_id)
    {
        $concept = FeeConcept::find($concept_id);

        if ($concept && $concept->name == 'Refund') {
            return true;
        } else {
            return false;
        }
    }

    public function conceptExistDiferentRefund($concept_id)
    {
        $concept = FeeConcept::find($concept_id);

        if ($concept && $concept->name != 'Refund') {
            return true;
        } else {
            return false;
        }
    }

    public function getConceptFee($concept_id, $amount = 0)
    {
        $concept = FeeConcept::find($concept_id);

        return new FeeConceptDto($concept, $amount);
    }
}
