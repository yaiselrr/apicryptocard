<?php

namespace App\Http\Controllers\Client;

use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\API\UpdateAccountRequest;
use App\Repositories\AccountRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Dto\AccountDto;
use App\Http\Controllers\Controller;
use App\Repositories\TransactionRepository;
use App\Repositories\AlquimiapayTokenRepository;

class AccountController extends Controller
{
    /** @var  AccountRepository */
    private $accountRepository;
    private $transactionRepository;
    private $alquimiaRepository;

    public function __construct(TransactionRepository $transactionRepo, AccountRepository $accountRepo, AlquimiapayTokenRepository $alquimiapayToken)
    {
        $this->accountRepository = $accountRepo;
        $this->transactionRepository = $transactionRepo;
        $this->alquimiaRepository = $alquimiapayToken;
    }

    public function index(Request $request)
    {
        $client = $this->accountRepository->getClient();

        if ($client < 0) {
            return response()->json([
                'message' => trans('msgs.msg_valid_user_error'),
                'type' => trans('msgs.type_error'),
            ], 401);
        }

        $accounts = $this->accountRepository->getClientId($client);

        return response()->json(['data' => $accounts], 200);
    }

    public function filter(Request $request)
    {
        $clientId = $this->accountRepository->getClient();

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
            $paginado = $this->accountRepository->filtro($filter, $orderBy, $direction, $paginate, $request);

            $result = $this->accountRepository->filterClientId($clientId, $paginado->items());

            $paginado = $paginado->toArray();
            $paginado['data'] = $result;

            return response()->json($paginado, 200);
        } catch (QueryException $e) {
            return response()->json([
                'message' => trans('msgs.msg_error_filter_column_no_exist'),
                'type' => trans('msgs.type_error')
            ], 500);
        }
    }

    public function store(UpdateAccountRequest $request)
    {
        try {
            $clientId = $this->accountRepository->getClient();

            if ($clientId < 0) {
                return response()->json([
                    'message' => trans('msgs.msg_valid_user_error'),
                    'type' => trans('msgs.type_error'),
                ], 401);
            }

            $input = $request->all();
            //Validar tarjeta

            $data = $this->alquimiaRepository->asignacionTarjetaCliente($request->card_number, $request->activation_code);

            if ($data != false) {
                $array = $this->accountRepository->makeArray($request->card_number, $clientId);
                $inputEnd = array_merge($input, $array);
                //Creando Account en la bd
                $account = $this->accountRepository->create($inputEnd);
                $account->id_account = $data->cuenta_ahorro->id_cuenta_ahorro;
                $account->save();
                $movements = [];

                return response()->json([
                    'message' => trans('msgs.msg_el_save_successfully', ['var' => trans_choice('msgs.label_account', 1)]),
                    'type' => trans('msgs.type_success'),
                    'object' => new AccountDto($account, $movements)
                ], 201);
            } else {
                return response()->json([
                    'message' => trans('msgs.msg_card_exist_error'),
                    'type' => trans('msgs.type_error'),
                ], 422);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.label_account_reload', 1)]),
                'type' => trans('msgs.type_error'),
                // 'object' => $data
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
            $clientId = $this->accountRepository->getClient();

            if ($clientId < 0) {
                return response()->json([
                    'message' => trans('msgs.msg_valid_user_error'),
                    'type' => trans('msgs.type_error'),
                ], 401);
            }

            $account = $this->accountRepository->getAccountClientId($id, $clientId);
            $movements = $this->transactionRepository->listAllMovements($account->id, $clientId);

            if (!$account) {
                return response()->json([
                    'message' => trans('msgs.msg_el_error', ['var' => trans_choice('msgs.label_account', 1)]),
                    'type' => trans('msgs.type_error'),
                ], 422);
            }

            return response()->json([
                'message' => trans('msgs.msg_el_save_successfully', ['var' => trans_choice('msgs.label_account', 1)]),
                'type' => trans('msgs.type_success'),
                'object' => new AccountDto($account, $movements)
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.label_account', 1)]),
                'type' => trans('msgs.type_error'),
                'object' => new AccountDto($account, $movements)
            ], 404);
        } catch (CustomException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'type' => trans('msgs.type_error')
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => trans('msgs.msg_error_contact_the_adminitrator'),
                'type' => trans('msgs.type_error')
            ], 500);
        }
    }

    public function update($id, UpdateAccountRequest $request)
    {
        try {
            $clientId = $this->accountRepository->getClient();

            if ($clientId < 0) {
                return response()->json([
                    'message' => trans('msgs.msg_valid_user_error'),
                    'type' => trans('msgs.type_error'),
                ], 401);
            }

            $account = $this->accountRepository->getAccountClientId($id, $clientId);
            $movements = $this->transactionRepository->listAllMovements($account->id, $clientId);
            $input = $request->all();

            if (!$account) {
                return response()->json([
                    'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.label_account', 1)]),
                    'type' => trans('msgs.type_error')
                ], 422);
            }

            //Actualizando el account en la bd
            $account = $this->accountRepository->update($input, $id);

            return response()->json([
                'message' => trans('msgs.msg_el_save_successfully', ['var' => trans_choice('msgs.label_account', 1)]),
                'type' => trans('msgs.type_success'),
                'object' => new AccountDto($account, $movements)
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.label_account', 1)]),
                'type' => trans('msgs.type_error'),
                'object' => new AccountDto($account, $movements)
            ], 404);
        } catch (CustomException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'type' => trans('msgs.type_error')
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => trans('msgs.msg_error_contact_the_adminitrator'),
                'type' => trans('msgs.type_error')
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $clientId = $this->accountRepository->getClient();

            if ($clientId < 0) {
                return response()->json([
                    'message' => trans('msgs.msg_valid_user_error'),
                    'type' => trans('msgs.type_error'),
                ], 401);
            }

            $account = $this->accountRepository->getAccountClientId($id, $clientId);
            $movements = $this->transactionRepository->listAllMovements($account->id, $clientId);

            if (!$account) {
                return response()->json([
                    'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.label_account', 1)]),
                    'type' => trans('msgs.type_error')
                ], 422);
            }
            //Eliminando
            $this->accountRepository->delete($id);

            return response()->json([
                'message' => trans('msgs.msg_el_save_successfully', ['var' => trans_choice('msgs.label_account', 1)]),
                'type' => trans('msgs.type_success'),
                'object' => new AccountDto($account, $movements)
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.label_account', 1)]),
                'type' => trans('msgs.type_error'),
                'object' => new AccountDto($account, $movements)
            ], 404);
        } catch (CustomException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'type' => trans('msgs.type_error')
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => trans('msgs.msg_error_contact_the_adminitrator'),
                'type' => trans('msgs.type_error')
            ], 500);
        }
    }

    public function reportStolenCard(Request $request)
    {
        try {
            $clientId = $this->accountRepository->getClient();

            if ($clientId < 0) {
                return response()->json([
                    'message' => trans('msgs.msg_valid_user_error'),
                    'type' => trans('msgs.type_error'),
                ], 401);
            }

            //Comprobando que exista el account en la bd
            $account = $this->accountRepository->getAccountClientId($request->account_id, $clientId);
            $movements = $this->transactionRepository->listAllMovements($request->account_id, $clientId);

            if (!$account) {
                return response()->json([
                    'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.label_account', 1)]),
                    'type' => trans('msgs.type_error')
                ], 422);
            }
            //Solicito bloquear tarjeta
            $data = $this->alquimiaRepository->bloquearTarjeta($account->card_number);

            if ($data != false) {
                //Reportar como robada
                $this->accountRepository->stolenCard($account);

                return response()->json([
                    'message' => trans('msgs.msg_el_stolen_successfully', ['var' => trans_choice('msgs.label_account', 1)]),
                    'type' => trans('msgs.type_success'),
                    'object' => new AccountDto($account, $movements)
                ], 200);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.label_account', 1)]),
                'type' => trans('msgs.type_error'),
                'object' => new AccountDto($account, $movements)
            ], 404);
        } catch (CustomException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'type' => trans('msgs.type_error')
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => trans('msgs.msg_error_contact_the_adminitrator'),
                'type' => trans('msgs.type_error')
            ], 500);
        }
    }

    public function getTotalsAccounts(Request $request)
    {
        $client = $this->accountRepository->getClient();

        if ($client < 0) {
            return response()->json([
                'message' => trans('msgs.msg_valid_user_error'),
                'type' => trans('msgs.type_error'),
            ], 401);
        }

        $accounts = $this->accountRepository->getTotalsAccounts($client);

        return response()->json(['data' => $accounts], 200);
    }
}
