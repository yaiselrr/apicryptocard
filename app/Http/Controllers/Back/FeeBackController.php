<?php

namespace App\Http\Controllers\Back;

use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\Api\UpdateFeeRequest;
use App\Repositories\FeeRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FeeBackController extends Controller
{
    /** @var  FeeRepository */
    private $feeRepository;

    public function __construct(FeeRepository $feeRepo)
    {
        $this->feeRepository = $feeRepo;
    }

    public function index(Request $request)
    {
        $userId = $this->feeRepository->getUser();

        if ($userId < 0) {
            return response()->json([
                'message' => trans('msgs.msg_valid_user_error'),
                'type' => trans('msgs.type_error'),
            ], 401);
        }

        $fees = $this->feeRepository->listFeesAll();

        return response()->json(['data' => $fees], 200);
    }

    public function filter(Request $request)
    {
        $userId = $this->feeRepository->getUser();

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
            $paginado =  $this->feeRepository->filtro($filter, $orderBy, $direction, $paginate);
            return response()->json($paginado, 200);
        } catch (QueryException $e) {
            return response()->json([
                'message' => trans('msgs.msg_error_filter_column_no_exist'),
                'type' => trans('msgs.type_error')
            ], 500);
        }
    }

    public function store(UpdateFeeRequest $request)
    {
        try {
            $userId = $this->feeRepository->getUser();

            if ($userId < 0) {
                return response()->json([
                    'message' => trans('msgs.msg_valid_user_error'),
                    'type' => trans('msgs.type_error'),
                ], 401);
            }

            $input = $request->all();
            //Creando Fee en la bd
            $fee = $this->feeRepository->create($input);

            return response()->json([
                'message' => trans('msgs.msg_el_save_successfully', ['var' => trans_choice('msgs.label_fee', 1)]),
                'type' => trans('msgs.type_success'),
                'object' => $fee
            ], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.label_account_reload', 1)]),
                'type' => trans('msgs.type_error'),
                'object' => $fee
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
            $userId = $this->feeRepository->getUser();

            if ($userId < 0) {
                return response()->json([
                    'message' => trans('msgs.msg_valid_user_error'),
                    'type' => trans('msgs.type_error'),
                ], 401);
            }

            $fee = $this->feeRepository->find($id);
            return response()->json(['data' => $fee], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.label_account_reload', 1)]),
                'type' => trans('msgs.type_error'),
                'object' => $fee
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

    public function update($id, UpdateFeeRequest $request)
    {
        try {
            $userId = $this->feeRepository->getUser();

            if ($userId < 0) {
                return response()->json([
                    'message' => trans('msgs.msg_valid_user_error'),
                    'type' => trans('msgs.type_error'),
                ], 401);
            }

            $fee = $this->feeRepository->find($id);
            $input = $request->all();

            //Actualizando el fee en la bd
            $fee = $this->feeRepository->update($input, $id);

            return response()->json([
                'message' => trans('msgs.msg_el_update_successfully', ['var' => trans_choice('msgs.label_fee', 1)]),
                'type' => trans('msgs.type_success')
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.label_account_reload', 1)]),
                'type' => trans('msgs.type_error'),
                'object' => $fee
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
            $userId = $this->feeRepository->getUser();

            if ($userId < 0) {
                return response()->json([
                    'message' => trans('msgs.msg_valid_user_error'),
                    'type' => trans('msgs.type_error'),
                ], 401);
            }

            //Comprobando que exista el fee en la bd
            $fee = $this->feeRepository->find($id);
            //Eliminando
            $this->feeRepository->delete($id);

            return response()->json(['message' => trans('msgs.msg_el_delete_successfully', ['var' => trans_choice('msgs.label_fee', 1)]), 'type' => trans('msgs.type_success')], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.label_account_reload', 1)]),
                'type' => trans('msgs.type_error'),
                'object' => $fee
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

    public function activeFee()
    {
        $userId = $this->feeRepository->getUser();

        if ($userId < 0) {
            return response()->json([
                'message' => trans('msgs.msg_valid_user_error'),
                'type' => trans('msgs.type_error'),
            ], 401);
        }

        $fees = $this->feeRepository->activeFee();

        return response()->json(['data' => $fees], 200);
    }

    public function getFeeFromAmount($amount,$type,$currency,$account_id)
    {
        $userId = $this->feeRepository->getUser();

        if ($userId < 0) {
            return response()->json([
                'message' => trans('msgs.msg_valid_user_error'),
                'type' => trans('msgs.type_error'),
            ], 401);
        }

        $fees = $this->feeRepository->getFeeFromAmount($amount, $type, $currency, $account_id);

        return response()->json(['data' => $fees], 200);
    }
}
