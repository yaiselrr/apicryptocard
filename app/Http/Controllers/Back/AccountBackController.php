<?php

namespace App\Http\Controllers\Back;

use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\API\UpdateAccountRequest;
use App\Repositories\AccountRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Repositories\AlquimiapayTokenRepository;
use App\Dto\AccountDto;

class AccountBackController extends Controller
{
    /** @var  AccountRepository */
    private $accountRepository;
    private $alquimiaRepository;

    public function __construct(AccountRepository $accountRepo, AlquimiapayTokenRepository $alquimiapayToken)
    {
        $this->accountRepository = $accountRepo;
        $this->alquimiaRepository = $alquimiapayToken;
    }

    public function listAllAccounts()
    {
        $userId = $this->accountRepository->getUser();

        if ($userId < 0) {
            return response()->json([
                'message' => trans('msgs.msg_valid_user_error'),
                'type' => trans('msgs.type_error'),
            ], 401);
        }

        $accounts = $this->accountRepository->listAllAccounts();

        return response()->json(['data' => $accounts], 200);
    }

    public function listAllClientAccount($client_id)
    {
        $userId = $this->accountRepository->getUser();

        if ($userId < 0) {
            return response()->json([
                'message' => trans('msgs.msg_valid_user_error'),
                'type' => trans('msgs.type_error'),
            ], 401);
        }

        $user = Client::find($client_id);

        if (!$user) {
            return response()->json([
                'message' => "El cliente no existe",
                'type' => trans('msgs.type_error'),
            ], 422);
        } else {
            $accounts = $this->accountRepository->listAllClientAccount($client_id);

            return response()->json(['data' => $accounts], 200);
        }
    }

    public function dataCollectionAccount()
    {
        $userId = $this->accountRepository->getUser();

        if ($userId < 0) {
            return response()->json([
                'message' => trans('msgs.msg_valid_user_error'),
                'type' => trans('msgs.type_error'),
            ], 401);
        }

        $account = $this->accountRepository->dataCollectionAccount();

        return response()->json(['data' => $account], 200);
    }

    public function filter(Request $request)
    {
        $userId = $this->accountRepository->getUser();

        if ($userId < 0) {
            return response()->json([
                'message' => trans('msgs.msg_valid_user_error'),
                'type' => trans('msgs.type_error'),
            ], 401);
        }

        $filter = $request->filter ? json_decode($request->filter) : [];
        $orderBy = $request->orderBy ? $request->orderBy : 'created_at';
        $direction = $request->direction ? $request->direction : 'ASC';
        $paginate = 50;

        if (!empty($request['paginate']) && $request['paginate'] != null)
            $paginate = $request['paginate'];
        try {
            $paginado = $this->accountRepository->filtroAll($filter, $orderBy, $direction, $paginate, $request);

            $result = $this->accountRepository->filterAdmin($paginado->items());

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
            $userId = $this->accountRepository->getUser();

            if ($userId < 0) {
                return response()->json([
                    'message' => trans('msgs.msg_valid_user_error'),
                    'type' => trans('msgs.type_error'),
                ], 401);
            }

            $clientId = $request->client_id;
            $input = $request->all();
            //Validar tarjeta
            $data = $this->alquimiaRepository->asignacionTarjetaCliente($request->card_number, $request->activation_code);

            if ($data != false) {
                $array = $this->accountRepository->makeArray($request->card_number, $clientId);
                $inputEnd = array_merge($input, $array);
                //Creando Account en la bd
                $account = $this->accountRepository->create($inputEnd);
                $account->id_account = $account->id_account = $data->cuenta_ahorro->id_cuenta_ahorro;
                $account->save();
                $movements = [];

                return response()->json([
                    'message' => trans('msgs.msg_el_save_successfully', ['var' => trans_choice('msgs.label_account', 1)]),
                    'type' => trans('msgs.type_success'),
                    'object' => new AccountDto($account, $movements)
                ], 201);
            } else {
                return response()->json([
                    'message' => "La tarjeta ya ha sido asignada a otro usuario",
                    'type' => trans('msgs.type_error'),
                ], 422);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.label_account_reload', 1)]),
                'type' => trans('msgs.type_error'),
                // 'object' => $fee
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

    public function reportStolenCard(Request $request)
    {
        try {
            $userId = $this->accountRepository->getUser();

            if ($userId < 0) {
                return response()->json([
                    'message' => trans('msgs.msg_valid_user_error'),
                    'type' => trans('msgs.type_error'),
                ], 401);
            }

            $clientId = $request->client_id;
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
                'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.label_account_reload', 1)]),
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
                'type' => trans('msgs.type_error'),
                'object' => $e
            ], 500);
        }
    }

    public function listAllAccountByCode($id)
    {
        $client = Client::where('user_id', $id)->first();

        if (!$client) {
            return response()->json([
                'message' => "El cliente no existe",
                'type' => trans('msgs.type_error'),
            ], 422);
        }

        $totalAvailableBalance = 0;
        $totalRefUsd = 0;
        $totalRefEur = 0;
        $accounts = $this->accountRepository->getAllAccountClientId($client->id, $totalAvailableBalance, $totalRefUsd, $totalRefEur);

        return response()->json(['data' => $accounts,
            'totals' => ['total_available_balance' => $totalAvailableBalance, 'total_ref_usd' => $totalRefUsd, 'total_ref_eur' => $totalRefEur]],
            200);
    }


}
