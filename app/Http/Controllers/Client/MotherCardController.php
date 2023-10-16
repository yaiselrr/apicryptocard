<?php

namespace App\Http\Controllers\Client;

use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\API\UpdateMotherCardRequest;
use App\Repositories\MotherCardRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MotherCardController extends Controller
{
    /** @var  MotherCardRepository */
    private $motherCardRepository;

    public function __construct(MotherCardRepository $motherCardRepo)
    {
        $this->motherCardRepository = $motherCardRepo;
    }


    public function index(Request $request)
    {
        $clientId = $this->motherCardRepository->getClient();

        if ($clientId < 0) {
            return response()->json([
                'message' => trans('msgs.msg_valid_user_error'),
                'type' => trans('msgs.type_error'),
            ], 401);
        }

        $motherCards = $this->motherCardRepository->all();

        return response()->json(['data' => $motherCards], 200);
    }

    public function filter(Request $request)
    {
        $clientId = $this->motherCardRepository->getClient();

        if ($clientId < 0) {
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
            $paginado =  $this->motherCardRepository->filtro($filter, $orderBy, $direction, $paginate);
            return response()->json($paginado, 200);
        } catch (QueryException $e) {
            return response()->json([
                'message' => trans('msgs.msg_error_filter_column_no_exist'),
                'type' => trans('msgs.type_error')
            ], 500);
        }
    }

    public function store(UpdateMotherCardRequest $request)
    {
        try {
            $clientId = $this->motherCardRepository->getClient();

            if ($clientId < 0) {
                return response()->json([
                    'message' => trans('msgs.msg_valid_user_error'),
                    'type' => trans('msgs.type_error'),
                ], 401);
            }

            $input = $request->all();
            //Creando MotherCard en la bd
            $motherCard = $this->motherCardRepository->create($input);

            return response()->json([
                'message' => trans('msgs.msg_el_save_successfully', ['var' => trans_choice('msgs.label_motherCard', 1)]),
                'type' => trans('msgs.type_success'),
                'object' => $motherCard
            ], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.label_account_reload', 1)]),
                'type' => trans('msgs.type_error'),
                'object' => $motherCard
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
            $clientId = $this->motherCardRepository->getClient();

            if ($clientId < 0) {
                return response()->json([
                    'message' => trans('msgs.msg_valid_user_error'),
                    'type' => trans('msgs.type_error'),
                ], 401);
            }

            $motherCard = $this->motherCardRepository->find($id);
            return response()->json(['data' => $motherCard], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.label_account_reload', 1)]),
                'type' => trans('msgs.type_error'),
                'object' => $motherCard
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

    public function update($id, UpdateMotherCardRequest $request)
    {
        try {
            $clientId = $this->motherCardRepository->getClient();

            if ($clientId < 0) {
                return response()->json([
                    'message' => trans('msgs.msg_valid_user_error'),
                    'type' => trans('msgs.type_error'),
                ], 401);
            }

            $motherCard = $this->motherCardRepository->find($id);
            $input = $request->all();

            //Actualizando el motherCard en la bd
            $motherCard = $this->motherCardRepository->update($input, $id);

            return response()->json([
                'message' => trans('msgs.msg_el_update_successfully', ['var' => trans_choice('msgs.label_motherCard', 1)]),
                'type' => trans('msgs.type_success')
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.label_account_reload', 1)]),
                'type' => trans('msgs.type_error'),
                'object' => $motherCard
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
            $clientId = $this->motherCardRepository->getClient();

            if ($clientId < 0) {
                return response()->json([
                    'message' => trans('msgs.msg_valid_user_error'),
                    'type' => trans('msgs.type_error'),
                ], 401);
            }

            //Comprobando que exista el motherCard en la bd
            $motherCard = $this->motherCardRepository->find($id);
            //Eliminando
            $this->motherCardRepository->delete($id);

            return response()->json(['message' => trans('msgs.msg_el_delete_successfully', ['var' => trans_choice('msgs.label_motherCard', 1)]), 'type' => trans('msgs.type_success')], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.label_account_reload', 1)]),
                'type' => trans('msgs.type_error'),
                'object' => $motherCard
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
}
