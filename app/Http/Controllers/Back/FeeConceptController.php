<?php

namespace App\Http\Controllers\Back;

use App\Dto\FeeConceptDto;
use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Repositories\FeeConceptRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Requests\API\UpdateFeeConceptRequest;

/**
 * Class FeeConceptController
 * @package App\Http\Controllers\API
 */

class FeeConceptController extends Controller
{
    /** @var  FeeConceptRepository */
    private $feeConceptRepository;

    public function __construct(FeeConceptRepository $feeConceptRepo)
    {
        $this->feeConceptRepository = $feeConceptRepo;
    }

    public function index(Request $request)
    {
        $userId = $this->feeConceptRepository->getUser();

        if ($userId < 0) {
            return response()->json([
                'message' => trans('msgs.msg_valid_user_error'),
                'type' => trans('msgs.type_error'),
            ], 401);
        }

        $feeConcepts = $this->feeConceptRepository->listAll();

        return response()->json(['data' => $feeConcepts], 200);
    }

    public function filter(Request $request)
    {
        $userId = $this->feeConceptRepository->getUser();

        if ($userId < 0) {
            return response()->json([
                'message' => trans('msgs.msg_valid_user_error'),
                'type' => trans('msgs.type_error'),
            ], 401);
        }

        $filter = $request->filter ? json_decode($request->filter) : [];
        $orderBy = $request->orderBy ? $request->orderBy : 'created_at';
        $direction = $request->direction ? $request->direction : 'ASC';
        $paginate = 10;
        if (!empty($request['paginate']) && $request['paginate'] != null)
            $paginate = $request['paginate'];
        try {
            $paginado =  $this->feeConceptRepository->filtro($filter, $orderBy, $direction, $paginate);
            return response()->json($paginado, 200);
        } catch (QueryException $e) {
            return response()->json([
                'message' => trans('msgs.msg_error_filter_column_no_exist'),
                'type' => trans('msgs.type_error')
            ], 500);
        }
    }

    public function store(UpdateFeeConceptRequest $request)
    {
        try {
            $userId = $this->feeConceptRepository->getUser();

            if ($userId < 0) {
                return response()->json([
                    'message' => trans('msgs.msg_valid_user_error'),
                    'type' => trans('msgs.type_error'),
                ], 401);
            }

            $input = $request->all();
            //Creando FeeConcept en la bd
            $feeConcept = $this->feeConceptRepository->create($input);

            return response()->json([
                'message' => trans('msgs.msg_el_save_successfully', ['var' => trans_choice('msgs.label_feeConcept', 1)]),
                'type' => trans('msgs.type_success'),
                'object' => $feeConcept
            ], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.label_account_reload', 1)]),
                'type' => trans('msgs.type_error'),
                'object' => $feeConcept
            ], 404);
        } catch (CustomException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'type' => trans('msgs.type_error')
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => trans('msgs.msg_error_contact_the_adminitrator'),
                'type' => trans('msgs.type_error'),
                'object' => $e
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $userId = $this->feeConceptRepository->getUser();

            if ($userId < 0) {
                return response()->json([
                    'message' => trans('msgs.msg_valid_user_error'),
                    'type' => trans('msgs.type_error'),
                ], 401);
            }

            $feeConcept = $this->feeConceptRepository->find($id);
            return response()->json(['data' => new FeeConceptDto($feeConcept,0)], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.label_account_reload', 1)]),
                'type' => trans('msgs.type_error'),
                'object' => $feeConcept
            ], 404);
        } catch (CustomException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'type' => trans('msgs.type_error')
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => trans('msgs.msg_error_contact_the_adminitrator'),
                'type' => trans('msgs.type_error'),
                'object' => $e
            ], 500);
        }
    }

    public function update($id, UpdateFeeConceptRequest $request)
    {
        try {
            $userId = $this->feeConceptRepository->getUser();

            if ($userId < 0) {
                return response()->json([
                    'message' => trans('msgs.msg_valid_user_error'),
                    'type' => trans('msgs.type_error'),
                ], 401);
            }

            $feeConcept = $this->feeConceptRepository->find($id);
            $input = $request->all();

            //Actualizando el feeConcept en la bd
            $feeConcept = $this->feeConceptRepository->update($input, $id);

            return response()->json([
                'message' => trans('msgs.msg_el_update_successfully', ['var' => trans_choice('msgs.label_feeConcept', 1)]),
                'type' => trans('msgs.type_success')
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.label_account_reload', 1)]),
                'type' => trans('msgs.type_error'),
                'object' => $feeConcept
            ], 404);
        } catch (CustomException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'type' => trans('msgs.type_error')
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => trans('msgs.msg_error_contact_the_adminitrator'),
                'type' => trans('msgs.type_error'),
                'object' => $e
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $userId = $this->feeConceptRepository->getUser();

            if ($userId < 0) {
                return response()->json([
                    'message' => trans('msgs.msg_valid_user_error'),
                    'type' => trans('msgs.type_error'),
                ], 401);
            }

            //Comprobando que exista el feeConcept en la bd
            $feeConcept = $this->feeConceptRepository->find($id);
            //Eliminando
            $this->feeConceptRepository->delete($id);

            return response()->json(['message' => trans('msgs.msg_el_delete_successfully', ['var' => trans_choice('msgs.label_feeConcept', 1)]), 'type' => trans('msgs.type_success')], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.label_account_reload', 1)]),
                'type' => trans('msgs.type_error'),
                'object' => $feeConcept
            ], 404);
        } catch (CustomException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'type' => trans('msgs.type_error')
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => trans('msgs.msg_error_contact_the_adminitrator'),
                'type' => trans('msgs.type_error'),
                'object' => $e
            ], 500);
        }
    }

    public function getFeeFromAmountByConcept($amount, $concept_id)
    {
        $userId = $this->feeConceptRepository->getUser();

        if ($userId < 0) {
            return response()->json([
                'message' => trans('msgs.msg_valid_user_error'),
                'type' => trans('msgs.type_error'),
            ], 401);
        }

        $feeConcepts = $this->feeConceptRepository->getFeeFromAmountByConcept($amount, $concept_id);

        return response()->json(['data' => $feeConcepts], 200);
    }

    public function getFeeByConcept($concept_id)
    {
        $userId = $this->feeConceptRepository->getUser();

        if ($userId < 0) {
            return response()->json([
                'message' => trans('msgs.msg_valid_user_error'),
                'type' => trans('msgs.type_error'),
            ], 401);
        }

        $feeConcepts = $this->feeConceptRepository->getFeeByConcept($concept_id);

        return response()->json(['data' => $feeConcepts], 200);
    }
}
