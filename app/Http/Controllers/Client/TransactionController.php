<?php

namespace App\Http\Controllers\Client;

use App\Exceptions\CustomException;
use App\Http\Requests\API\UpdateTransactionMotherCardRequest;
use App\Repositories\Crypto\CryptoWalletRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Http\Requests\API\UpdateTransactionRequest;
use App\Repositories\TransactionRepository;
use App\Repositories\TransferLimitRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Requests\API\UpdateNotificationTransactionRequest;
use App\Models\Account;
use App\Http\Controllers\Controller;
use App\Repositories\AlquimiapayTokenRepository;

class TransactionController extends Controller
{
    /** @var  TransactionRepository */
    private $transactionRepository;
    private $alquimiaRepository;
    private $cryptoWalletRepository;
    private $transferLimitRepository;

    public function __construct(
        TransactionRepository $transactionRepo,
        AlquimiapayTokenRepository $alquimiapayToken,
        CryptoWalletRepository $cryptoWalletRepo,
        TransferLimitRepository $transferLimitRepo
    ) {
        $this->transactionRepository = $transactionRepo;
        $this->alquimiaRepository = $alquimiapayToken;
        $this->cryptoWalletRepository = $cryptoWalletRepo;
        $this->transferLimitRepository = $transferLimitRepo;
    }

    public function index(Request $request)
    {
        $clientId = $this->transactionRepository->getClient();

        if ($clientId < 0) {
            return response()->json([
                'message' => trans('msgs.msg_valid_user_error'),
                'type' => trans('msgs.type_error'),
            ], 401);
        }

        $transactions = $this->transactionRepository->listAll($clientId);

        return response()->json(['data' => $transactions], 200);
    }

    public function filter(Request $request)
    {
        $clientId = $this->transactionRepository->getClient();

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
            $paginado = $this->transactionRepository->filtro($filter, $orderBy, $direction, $paginate);
            $result = $this->transactionRepository->filterClientId($clientId, $paginado->items());

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

    public function createTransactionTarjetaATarjeta(UpdateTransactionRequest $request)
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
                $proc = $this->transactionRepository->notificationReload($request->card_number_destiny, $amount, $data->id_transaccion, 1, $clientId);


                if ($proc != false) {
                    $this->transactionRepository->balanceDiscount($request->card_number_origin, $request->amount, $data->id_transaccion, $clientId, $fee->id, $userId, $data->folio_orden, $feeStore);
                    $this->transactionRepository->addBalanceToCollectionAccount($collection, $data->id_transaccion, $clientId, $fee->id, $userId, $data->folio_orden, $feeStore);

                    return response()->json(['data' => $proc], 200);
                }
            }
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.label_account_reload', 1)]),
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
                'type' => trans('msgs.type_error'),
                'object' => $e->getMessage()
            ], 500);
        }
    }

    public function createTransactionCuentaMadreATarjeta(UpdateTransactionMotherCardRequest $request)
    {
        try {
            $userId = $this->transactionRepository->getUser();

            if ($userId < 0) {
                return response()->json([
                    'message' => trans('msgs.msg_valid_user_error'),
                    'type' => trans('msgs.type_error'),
                ], 401);
            }

            $clientId = $this->transactionRepository->getClient();

            if ($clientId < 0) {
                return response()->json([
                    'message' => trans('msgs.msg_valid_user_error'),
                    'type' => trans('msgs.type_error'),
                ], 401);
            }

            $card = $this->transactionRepository->isCard($request->card_number);

            if (!$card) {
                return response()->json([
                    'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.label_account', 1)]),
                    'type' => trans('msgs.type_error')
                ], 422);
            }

            $fee = $this->transactionRepository->getFeeReload();
            $limit = $this->transactionRepository->getLimitReload();
            $feeStore = $this->transactionRepository->calcFee($request->amount, $fee->fee_card_reload);
            $isNew = $this->transactionRepository->isNewAccount($request->card_number);
            $amount = 0;

            if ($isNew) {

                if ($this->transactionRepository->isValidTransferFirst(
                    $request->amount,
                    $limit->limit_first_tx,
                    $fee->fee_first_tx
                )) {
                    $amount = $this->transactionRepository->getAmountFirstMotherAccount($request->amount, $feeStore, $fee->fee_first_tx);
                }
            } elseif ($this->transactionRepository->isValidTransfer(
                $request->amount,
                $limit->limit_card_reload
            )) {
                $amount = $this->transactionRepository->getAmount($request->amount, $feeStore);
            }

            $this->cryptoWalletRepository->cardReload($request);

            $data = $this->alquimiaRepository->transaccionCuentaMadreATarjeta($amount, $request->card_number, $userId, $clientId, $feeStore, $fee->id);

            if ($data != false) {
                $aut = $this->alquimiaRepository->autorizarTransaccionesPendientes($data->id_transaccion);

                if ($aut != false) {
                    $proc = $this->transactionRepository->notificationReload($request->card_number, $amount, $data->id_transaccion, 1, $clientId);

                    if ($proc != false) {
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
                'type' => trans('msgs.type_error')
            ], 500);
        }
    }

    public function notificationReload(UpdateNotificationTransactionRequest $request)
    {
        try {
            $clientId = $this->transactionRepository->getClient();

            if ($clientId < 0) {
                return response()->json([
                    'message' => trans('msgs.msg_valid_user_error'),
                    'type' => trans('msgs.type_error'),
                ], 401);
            }

            $data = $this->transactionRepository->notificationReload($request->card_number, $request->amount, $request->id_tx_vixipay, $request->state, $clientId);

            return response()->json(['data' => $data], 200);
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

    public function movements($id)
    {
        $clientId = $this->transactionRepository->getClient();

        if ($clientId < 0) {
            return response()->json([
                'message' => trans('msgs.msg_valid_user_error'),
                'type' => trans('msgs.type_error'),
            ], 401);
        }

        $account = Account::find($id);

        if (!$account) {
            return response()->json([
                'message' => trans('msgs.msg_el_no_found', ['var' => trans_choice('msgs.label_account', 1)]),
                'type' => trans('msgs.type_error')
            ], 422);
        }

        $movements = $this->transactionRepository->listAllMovements($id, $clientId);

        return response()->json(['data' => $movements], 200);
    }
}
