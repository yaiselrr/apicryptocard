<?php

namespace App\Http\Controllers\Back;

use App\Dto\TransactionBackDto;
use App\Models\Client;
use App\Repositories\Crypto\CryptoWalletRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\API\UpdateTransactionBackRequest;
use App\Http\Requests\API\UpdateLoadCardRequest;
use App\Repositories\TransactionRepository;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Repositories\AlquimiapayTokenRepository;
use App\Http\Requests\API\UpdateAdjustmentRequest;
use App\Repositories\AccountRepository;
use App\Exceptions\CustomException;


class TransactionBackController extends Controller
{
    /** @var  TransactionRepository */
    private $transactionRepository;
    private $alquimiaRepository;
    private $accountRepository;
    private $cryptowalletRepository;

    public function __construct(AccountRepository $accountRepo, TransactionRepository $transactionRepo, AlquimiapayTokenRepository $alquimiapayToken, CryptoWalletRepository $cryptowalletRepository)
    {
        $this->transactionRepository = $transactionRepo;
        $this->alquimiaRepository = $alquimiapayToken;
        $this->accountRepository = $accountRepo;
        $this->cryptowalletRepository = $cryptowalletRepository;
    }

    public function listTransactionsAccountMotherAll(Request $request)
    {
        $userId = $this->transactionRepository->getUser();

        if ($userId < 0) {
            return response()->json([
                'message' => trans('msgs.msg_valid_user_error'),
                'type' => trans('msgs.type_error'),
            ], 401);
        }

        $transactions = $this->transactionRepository->listTransactionsAccountMotherAll();

        return response()->json(['data' => $transactions], 200);
    }

    public function listTransactionsCardToCardAll(Request $request)
    {
        $userId = $this->transactionRepository->getUser();

        if ($userId < 0) {
            return response()->json([
                'message' => trans('msgs.msg_valid_user_error'),
                'type' => trans('msgs.type_error'),
            ], 401);
        }

        $transactions = $this->transactionRepository->listTransactionsCardToCardAll();

        return response()->json(['data' => $transactions], 200);
    }

    public function listTransactionsCollectionSubAccount(Request $request)
    {
        $userId = $this->transactionRepository->getUser();

        if ($userId < 0) {
            return response()->json([
                'message' => trans('msgs.msg_valid_user_error'),
                'type' => trans('msgs.type_error'),
            ], 401);
        }

        $transactions = $this->transactionRepository->listTransactionsCollectionSubAccount();

        return response()->json(['data' => $transactions], 200);
    }

    public function listTransactionsAll(Request $request)
    {
        $userId = $this->transactionRepository->getUser();

        if ($userId < 0) {
            return response()->json([
                'message' => trans('msgs.msg_valid_user_error'),
                'type' => trans('msgs.type_error'),
            ], 401);
        }

        $transactions = $this->transactionRepository->listTransactionsAll();

        return response()->json(['data' => $transactions], 200);
    }

    public function listTransactionsAllCardLoad(Request $request)
    {
        $userId = $this->transactionRepository->getUser();

        if ($userId < 0) {
            return response()->json([
                'message' => trans('msgs.msg_valid_user_error'),
                'type' => trans('msgs.type_error'),
            ], 401);
        }

        $transactions = $this->transactionRepository->listTransactionsAllCardLoad();

        return response()->json(['data' => $transactions], 200);
    }

    public function filter(Request $request)
    {
        $userId = $this->transactionRepository->getUser();

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
            $motherAccountBalanceResponse = $this->alquimiaRepository->obtenerBalanceCuentaMadre();
            $paginado =  $this->transactionRepository->filtroAll($filter, $orderBy, $direction, $paginate, $request);
            $result = $this->transactionRepository->filterAdmin($paginado->items(), $motherAccountBalanceResponse->saldo);

            $paginado = $paginado->toArray();
            $paginado['data'] = $result;

            return response()->json($paginado, 200);
        } catch (QueryException $e) {
            return response()->json([
                'message' => trans('msgs.msg_error_filter_column_no_exist'),
                'type' => trans('msgs.type_error')
            ], 500);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.label_account_reload', 1)]),
                'type' => trans('msgs.type_error'),
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

    public function filterCollectionAccount(Request $request)
    {
        $filter = $request->filter ? json_decode($request->filter) : [];
        $orderBy = $request->orderBy ? $request->orderBy : 'id';
        $direction = $request->direction ? $request->direction : 'DESC';
        $paginate = 10;

        if (!empty($request['paginate']) && $request['paginate'] != null)
            $paginate = $request['paginate'];
        try {

            $paginado = $this->transactionRepository->filterCollectionAccount($filter, $orderBy, $direction, $paginate);
            $result = $this->transactionRepository->filterAdminbyCollectionAccount($paginado->items());

            $paginado = $paginado->toArray();
            $paginado['data'] = $result;

            return response()->json($paginado, 200);
        } catch (QueryException $e) {
            return response()->json([
                'message' => trans('msgs.msg_error_filter_column_no_exist'),
                'type' => trans('msgs.type_error')
            ], 500);
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

    public function filterByAccount(Request $request)
    {
        $userId = $this->transactionRepository->getUser();

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
        $card = $this->transactionRepository->isCard($request->card_number);

        if (!$card) {
            return response()->json([
                'message' => "La tarjeta no existe",
                'type' => trans('msgs.type_error'),
            ], 422);
        }

        if (!empty($request['paginate']) && $request['paginate'] != null)
            $paginate = $request['paginate'];
        try {
            $paginado =  $this->transactionRepository->filtroByAccountAll($filter, $orderBy, $direction, $paginate, $request, $card->id);
            $result = $this->transactionRepository->filterAdminbyAccount($paginado->items());

            $paginado = $paginado->toArray();
            $paginado['data'] = $result;

            return response()->json($paginado, 200);
        } catch (QueryException $e) {
            return response()->json([
                'message' => trans('msgs.msg_error_filter_column_no_exist'),
                'type' => trans('msgs.type_error')
            ], 500);
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

    public function listMovementsByCardNumber(Request $request)
    {
        $userId = $this->transactionRepository->getUser();

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
            $paginado =  $this->transactionRepository->filtroBackAll($filter, $orderBy, $direction, $paginate);
            $result = [];

            foreach ($paginado->items() as $item) {
                $result[] = new TransactionBackDto($item);
            }

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

    public function filtroAllCardLoad(Request $request)
    {
        $userId = $this->transactionRepository->getUser();

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
            $paginado =  $this->transactionRepository->filtroAllCardLoad($filter, $orderBy, $direction, $paginate, $request);
            $result = $this->transactionRepository->filterAdminCardLoad($paginado->items());

            $paginado = $paginado->toArray();
            $paginado['data'] = $result;

            return response()->json($paginado, 200);
        } catch (QueryException $e) {
            return response()->json([
                'message' => trans('msgs.msg_error_filter_column_no_exist'),
                'type' => trans('msgs.type_error')
            ], 500);
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

    public function consultarTranferencia(Request $request)
    {
        $userId = $this->transactionRepository->getUser();

        if ($userId < 0) {
            return response()->json([
                'message' => trans('msgs.msg_valid_user_error'),
                'type' => trans('msgs.type_error'),
            ], 401);
        }

        try {
            $data = $this->alquimiaRepository->consultarTranferencia($request->id_transaccion, $request->id_cuenta);

            if ($data != false) {
                return response()->json(['data' => $data], 200);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.label_account_reload_fail', 1)]),
                'type' => trans('msgs.type_error')
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

    public function consultarTranferenciasPendientes(Request $request)
    {
        $userId = $this->transactionRepository->getUser();

        if ($userId < 0) {
            return response()->json([
                'message' => trans('msgs.msg_valid_user_error'),
                'type' => trans('msgs.type_error'),
            ], 401);
        }

        try {
            $data = $this->alquimiaRepository->consultarTranferenciasPendientes($request->id_cuenta);

            if ($data != false) {
                return response()->json(['data' => $data], 200);
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.label_account_reload_fail', 1)]),
                'type' => trans('msgs.type_error')
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

    public function createTransactionTarjetaATarjeta(UpdateTransactionBackRequest $request)
    {
        try {
            $userId = $this->transactionRepository->getUser();

            if ($userId < 0) {
                return response()->json([
                    'message' => trans('msgs.msg_valid_user_error'),
                    'type' => trans('msgs.type_error'),
                ], 401);
            }

            $clientId = $this->transactionRepository->getClientId($request->card_number_destiny);

            if ($clientId < 0) {
                return response()->json([
                    'message' => trans('msgs.msg_valid_user_error'),
                    'type' => trans('msgs.type_error'),
                ], 401);
            }

            //Chekeando 2fa
            $client = Client::find($clientId);
            $this->cryptowalletRepository->check2FAAuthenticator($client->user_id, $request->one_time_password, $request);

            $cardOrigin = $this->transactionRepository->isCard($request->card_number_origin);
            $cardDestiny = $this->transactionRepository->isCard($request->card_number_destiny);

            if (!$cardOrigin || !$cardDestiny) {
                return response()->json([
                    'message' => trans('msgs.msg_exist_card_error'),
                    'type' => trans('msgs.type_error')
                ], 422);
            }

            if ($request->card_number_origin == $request->card_number_destiny) {
                return response()->json([
                    'message' => trans('msgs.msg_account_equal_error'),
                    'type' => trans('msgs.type_error')
                ], 422);
            }

            $balance = $this->transactionRepository->getAccountBalance($request->card_number_origin);

            if ($balance < $request->amount) {
                return response()->json([
                    'message' => trans('msgs.msg_account_origin_error'),
                    'type' => trans('msgs.type_error')
                ], 422);
            }

            $fee = $this->transactionRepository->getFeeTxCard();
            $limit = $this->transactionRepository->getLimitTxCard();
            $feeStore = $this->transactionRepository->calcFee($request->amount, $fee->fee_card_tx);
            $isNew = $this->transactionRepository->isNewAccount($request->card_number_destiny);
            $amount = 0;
            $collection = 0;

            if ($isNew) {
                if ($this->transactionRepository->isValidTransferFirst(
                    $request->amount,
                    $limit->limit_first_tx,
                    $fee->fee_first_tx
                )) {
                    $amount = $this->transactionRepository->getAmountFirst($request->amount, $feeStore, $fee->fee_first_tx);
                    $collection = $this->transactionRepository->collectionBalance($feeStore, $fee->fee_first_tx);
                }
            } elseif ($this->transactionRepository->isValidTransfer(
                $request->amount,
                $limit->limit_card_tx
            )) {
                $amount = $this->transactionRepository->getAmount($request->amount, $feeStore);
                $collection = $feeStore;
            }

            $data = $this->alquimiaRepository->transaccionCuentaClienteACliente($amount, $request->card_number_destiny, $userId, $clientId, $feeStore, $fee->id, $request);

            if ($data != false) {
                $aut = $this->alquimiaRepository->autorizarTransaccionesPendientes($data->id_transaccion);

                if ($aut != false) {
                    $proc = $this->transactionRepository->notificationReload($request->card_number_destiny, $amount, $data->id_transaccion, 1, $clientId);

                    if ($proc != false) {
                        $this->transactionRepository->balanceDiscount($request->card_number_origin, $request->amount, $data->id_transaccion, $clientId, $fee->id, $userId, $data->id_transaccion, $feeStore);
                        $this->transactionRepository->addBalanceToCollectionAccount($collection, $data->id_transaccion, $clientId, $fee->id, $userId, $data->id_transaccion, $feeStore);

                        return response()->json(['data' => $proc], 200);
                    }
                }
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.label_account_reload', 1)]),
                'type' => trans('msgs.type_error'),
                'object' => $data
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

    public function loadCard(UpdateLoadCardRequest $request)
    {
        try {
            $userId = $this->transactionRepository->getUser();

            if ($userId < 0) {
                return response()->json([
                    'message' => trans('msgs.msg_valid_user_error'),
                    'type' => trans('msgs.type_error'),
                ], 401);
            }

            $collection_account = $this->accountRepository->dataCollectionAccount();

            if (!$collection_account || $collection_account->balance < 1) {
                return response()->json([
                    'message' => trans('msgs.balance_collection_account'),
                    'type' => trans('msgs.type_error'),
                ], 422);
            }

            $client_id = $this->transactionRepository->isClient($request->client_id);

            if (!$client_id) {
                return response()->json([
                    'message' => trans('msgs.msg_client_error'),
                    'type' => trans('msgs.type_error')
                ], 422);
            }

            $account_id = $this->transactionRepository->isAccountId($request->account_id);

            if (!$account_id) {
                return response()->json([
                    'message' => trans('msgs.msg_account_error'),
                    'type' => trans('msgs.type_error'),
                ], 422);
            }

            $account_client_id = $this->transactionRepository->accountClientExist($request->account_id, $request->client_id);

            if (!$account_client_id) {
                return response()->json([
                    'message' => trans('msgs.msg_account_error'),
                    'type' => trans('msgs.type_error'),
                ], 422);
            }

            //Chekeando 2fa
            $client = Client::find($request->client_id);
            $this->cryptowalletRepository->check2FAAuthenticator($client->user_id, $request->one_time_password, $request);


            if (!$this->transactionRepository->validAmount($request->amount)) {
                return response()->json([
                    'message' => trans('msgs.msg_amount_error'),
                    'type' => trans('msgs.type_error'),
                ], 422);
            }

            if (!$this->transactionRepository->validAmountVsBalanceCollevtionAccount($request->amount, $collection_account->balance)) {
                return response()->json([
                    'message' => trans('msgs.msg_amount_vs_collection_error'),
                    'type' => trans('msgs.type_error'),
                ], 422);
            }

            $fee = $this->transactionRepository->getFeeReload();
            $feeStore = $this->transactionRepository->calcFee($request->amount, $fee->fee_card_reload);
            $isNew = $this->transactionRepository->isNewAccountId($request->account_id);
            $amount = 0;

            if ($isNew) {
                $amount = $this->transactionRepository->getAmountFirstMotherAccount($request->amount, $feeStore, $fee->fee_first_tx);
            } else {
                $amount = $this->transactionRepository->getAmount($request->amount, $feeStore);
            }

            $data = $this->transactionRepository->loadCard($amount, $request->account_id, $userId, $request->client_id, $feeStore, $fee->id);

            if ($data != false) {
                $proc = $this->transactionRepository->notificationLoadCard($request->account_id, $amount, $request->client_id);
                $this->transactionRepository->balanceDiscountCollectionAccount($collection_account->id, $amount, $request->client_id, $fee->id, $userId, $feeStore);

                if ($proc != false) {
                    return response()->json(['data' => $proc], 200);
                }
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.label_account_reload', 1)]),
                'type' => trans('msgs.type_error'),
                'object' => $data
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

    public function adjustment(UpdateAdjustmentRequest $request)
    {
        try {
            $userId = $this->transactionRepository->getUser();

            if ($userId < 0) {
                return response()->json([
                    'message' => trans('msgs.msg_valid_user_error'),
                    'type' => trans('msgs.type_error'),
                ], 401);
            }

            $client_id = $this->transactionRepository->isClient($request->client_id);

            if (!$client_id) {
                return response()->json([
                    'message' => trans('msgs.msg_client_error'),
                    'type' => trans('msgs.type_error')
                ], 422);
            }

            $account_id = $this->transactionRepository->isAccountId($request->account_id);

            if (!$account_id) {
                return response()->json([
                    'message' => trans('msgs.msg_account_error'),
                    'type' => trans('msgs.type_error'),
                ], 422);
            }

            $account_client_id = $this->transactionRepository->accountClientExist($request->account_id, $request->client_id);

            if (!$account_client_id) {
                return response()->json([
                    'message' => trans('msgs.msg_account_error'),
                    'type' => trans('msgs.type_error'),
                ], 422);
            }

            //Chekeando 2fa
            $client = Client::find($request->client_id);
            $this->cryptowalletRepository->check2FAAuthenticator($client->user_id, $request->one_time_password, $request);

            $fee_concept_id = $this->transactionRepository->isConcept($request->fee_concept_id);

            if (!$fee_concept_id) {
                return response()->json([
                    'message' => trans('msgs.msg_fee_concept_id_error'),
                    'type' => trans('msgs.type_error'),
                ], 422);
            }

            if (!$this->transactionRepository->validDecrementBalanceAccount($request)) {
                return response()->json([
                    'message' => trans('msgs.msg_balance_error'),
                    'type' => trans('msgs.type_error'),
                ], 422);
            }

            $data = $this->transactionRepository->decrementBalanceAccount($request, $userId);

            return response()->json([
                'data' => $data
            ], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.label_account_reload', 1)]),
                'type' => trans('msgs.type_error'),
                'object' => $data
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

    public function users(Request $request)
    {
        $userId = $this->transactionRepository->getUser();

        if ($userId < 0) {
            return response()->json([
                'message' => trans('msgs.msg_valid_user_error'),
                'type' => trans('msgs.type_error'),
            ], 401);
        }

        $users = $this->transactionRepository->users($request);

        return response()->json(['data' => $users], 200);
    }

    public function totalsMovemenentsCollectionAccount(Request $request)
    {
        $userId = $this->transactionRepository->getUser();

        if ($userId < 0) {
            return response()->json([
                'message' => trans('msgs.msg_valid_user_error'),
                'type' => trans('msgs.type_error'),
            ], 401);
        }

        $totals = $this->transactionRepository->totalsMovemenentsCollectionAccount($request);

        return response()->json(['data' => $totals], 200);
    }

    public function totalsMovemenentsMotherAccount(Request $request)
    {
        $userId = $this->transactionRepository->getUser();

        if ($userId < 0) {
            return response()->json([
                'message' => trans('msgs.msg_valid_user_error'),
                'type' => trans('msgs.type_error'),
            ], 401);
        }
        $motherAccountBalanceResponse = $this->alquimiaRepository->obtenerBalanceCuentaMadre();

        $totals = $this->transactionRepository->totalsMovemenentsMotherAccount($motherAccountBalanceResponse->saldo);

        return response()->json(['data' => $totals], 200);
    }
}
