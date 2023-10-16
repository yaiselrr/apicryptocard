<?php

namespace App\Http\Controllers\Back;

use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\Api\UpdateTransferLimitRequest;
use App\Repositories\TransferLimitRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class TransferLimitBackController extends Controller
{
    /** @var  TransferLimitRepository */
    private $transferLimitRepository;

    public function __construct(TransferLimitRepository $transferLimitRepo)
    {
        $this->transferLimitRepository = $transferLimitRepo;
    }

    public function index(Request $request)
    {
        $userId = $this->transferLimitRepository->getUser();

        if ($userId < 0) {
            return response()->json([
                'message' => trans('msgs.msg_valid_user_error'),
                'type' => trans('msgs.type_error'),
            ], 401);
        }

        $transferLimits = $this->transferLimitRepository->listLimitsAll();

        return response()->json(['data' => $transferLimits], 200);
    }

    public function filter(Request $request)
    {
        $userId = $this->transferLimitRepository->getUser();

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
            $paginado =  $this->transferLimitRepository->filtro($filter, $orderBy, $direction, $paginate);
            return response()->json($paginado, 200);
        } catch (QueryException $e) {
            return response()->json([
                'message' => trans('msgs.msg_error_filter_column_no_exist'),
                'type' => trans('msgs.type_error')
            ], 500);
        }
    }

    public function store(UpdateTransferLimitRequest $request)
    {
        try {
            $userId = $this->transferLimitRepository->getUser();

            if ($userId < 0) {
                return response()->json([
                    'message' => trans('msgs.msg_valid_user_error'),
                    'type' => trans('msgs.type_error'),
                ], 401);
            }

            $input = $request->all();
            //Creando TransferLimit en la bd
            $transferLimit = $this->transferLimitRepository->create($input);

            return response()->json([
                'message' => trans('msgs.msg_el_save_successfully', ['var' => trans_choice('msgs.label_transferLimit', 1)]),
                'type' => trans('msgs.type_success'),
                'object' => $transferLimit
            ], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.label_account_reload', 1)]),
                'type' => trans('msgs.type_error'),
                'object' => $transferLimit
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
            $userId = $this->transferLimitRepository->getUser();

            if ($userId < 0) {
                return response()->json([
                    'message' => trans('msgs.msg_valid_user_error'),
                    'type' => trans('msgs.type_error'),
                ], 401);
            }

            $transferLimit = $this->transferLimitRepository->find($id);
            return response()->json(['data' => $transferLimit], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.label_account_reload', 1)]),
                'type' => trans('msgs.type_error'),
                'object' => $transferLimit
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

    public function update($id, UpdateTransferLimitRequest $request)
    {
        try {
            $userId = $this->transferLimitRepository->getUser();

            if ($userId < 0) {
                return response()->json([
                    'message' => trans('msgs.msg_valid_user_error'),
                    'type' => trans('msgs.type_error'),
                ], 401);
            }

            $transferLimit = $this->transferLimitRepository->find($id);
            $input = $request->all();

            //Actualizando el transferLimit en la bd
            $transferLimit = $this->transferLimitRepository->update($input, $id);

            return response()->json([
                'message' => trans('msgs.msg_el_update_successfully', ['var' => trans_choice('msgs.label_transferLimit', 1)]),
                'type' => trans('msgs.type_success')
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.label_account_reload', 1)]),
                'type' => trans('msgs.type_error'),
                'object' => $transferLimit
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
            $userId = $this->transferLimitRepository->getUser();

            if ($userId < 0) {
                return response()->json([
                    'message' => trans('msgs.msg_valid_user_error'),
                    'type' => trans('msgs.type_error'),
                ], 401);
            }

            //Comprobando que exista el transferLimit en la bd
            $transferLimit = $this->transferLimitRepository->find($id);
            //Eliminando
            $this->transferLimitRepository->delete($id);

            return response()->json(['message' => trans('msgs.msg_el_delete_successfully', ['var' => trans_choice('msgs.label_transferLimit', 1)]), 'type' => trans('msgs.type_success')], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.label_account_reload', 1)]),
                'type' => trans('msgs.type_error'),
                'object' => $transferLimit
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

    public function enabledLimit()
    {
        $userId = $this->transferLimitRepository->getUser();

        if ($userId < 0) {
            return response()->json([
                'message' => trans('msgs.msg_valid_user_error'),
                'type' => trans('msgs.type_error'),
            ], 401);
        }

        $limits = $this->transferLimitRepository->activeLimit();

        return response()->json(['data' => $limits], 200);
    }
}
