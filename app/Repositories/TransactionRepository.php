<?php

namespace App\Repositories;

use App\Models\Transaction;
use App\Dto\TransactionDto;
use App\Dto\TransactionCollectionAccountDto;
use App\Dto\CardLoadDto;
use App\Dto\TotalsCollectionAccountDto;
use App\Dto\TotalsMotherAccountDto;
use App\Dto\TransactionMotherAccDto;
use App\Dto\UserDto;
use App\Http\Controllers\Helpers\HelperFunctions;
use App\Models\Account;
use \Prettus\Repository\Eloquent\BaseRepository as BaseRepository;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Fee;
use App\Models\MotherCardTransaction;
use Illuminate\Support\Facades\Session;
use App\Models\Client;
use App\Exceptions\CustomException;
use App\Models\FeeConcept;
use App\Models\TransactionType;
use App\Models\TransferLimit;

class TransactionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'send_amount',
        'tx_blockchain_ref',
        'date',
        'amount',
        'amount_ref',
        'balance_before_transaction',
        'balance_after_transaction',
        'id_tx_alquimia',
        'type',
        'state',
        'account_id',
        'client_id',
        'currency_id',
        'fee_id',
        'transaction_type_id',
        'user_name',
        'card_provider_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Transaction::class;
    }

    public function filtro($filter = [], $orderBy = 'created_at', $direction = 'ASC', $paginate = 10)
    {
        $query = Transaction::query();
        $query = HelperFunctions::construirQuerysFiltros($filter, $orderBy, $direction, $query);
        return $query->paginate($paginate);
    }

    public static function filterCollectionAccount($filter, $orderBy = 'created_at', $direction = 'ASC', $paginate = 10)
    {
        $account = Account::where('collection_account', 1)->first();
        $query = Transaction::query();

        $query->where('transactions.account_id', '=', $account->id);
        $query->join('accounts', 'transactions.account_id', '=', 'accounts.id');
        $query->select(
            'transactions.*',
            'accounts.last8_digits as last8_digits'
        );

        $from = Carbon::createFromFormat('Y-m-d', '2020-01-01')->format('Y-m-d H:i:s');
        $to = Carbon::now()->format('Y-m-d H:i:s');
        $today = Carbon::now()->format('Y-m-d H:i:s');
        $start = Carbon::createFromFormat('Y-m-d', '2020-01-01')
        ->format('Y-m-d H:i:s');

        if (!$filter) {
            return $query->orderBy($orderBy, $direction)->paginate($paginate);
        } else {
            foreach ($filter as $campo => $val) {
                if (is_array($val)) {
                    list($campo, $condicion, $valor) = $val;
                    if ($campo == 'id') {
                        $query->where('transactions.' . $campo, $condicion, $valor);
                    }
                    if ($campo == 'user') {
                        $query->where('transactions.user_name', $condicion, $valor);
                    }
                    if ($campo == 'beneficiary') {
                        $query->where('accounts.last8_digits', $condicion, '%' . $valor . '%');
                    }
                    if ($campo == 'last4_digits') {
                        $query->where('accounts.last8_digits', $condicion, '%' . $valor . '%');
                    }
                    if ($campo == 'from') {
                        if ($from < $start) {
                            throw new CustomException("Date Error: The from date must be greater than or equal to the date 2020-01-01");
                        }
                        if ($from > $to) {
                            throw new CustomException("Date Error: The from date must be less than or equal the to date");
                        }
                        $query->whereDate('transactions.created_at', $condicion, $valor);
                    }
                    if ($campo == 'to') {
                        if ($to < $start) {
                            throw new CustomException("Date Error: The to date must be greater than or equal to the date 2020-01-01");
                        }
                        if ($to > $today) {
                            throw new CustomException("Date Error: The to date must be less than or equal to the today date");
                        }
                        $query->whereDate('transactions.created_at', $condicion, $valor);
                    }
                }
            }

            return $query->orderBy($orderBy, $direction)->paginate($paginate);
        }
    }

    public function filtroAll($filter = [], $orderBy = 'created_at', $direction = 'ASC', $paginate = 10, $params)
    {
        $beneficiary = $params->beneficiary;
        $provider = $params->provider;
        $concept = $params->concept;
        $from = $params->from ? $params->from : Carbon::createFromFormat('Y-m-d', '2020-01-01')
            ->format('Y-m-d H:i:s');
        $to = $params->to ? $params->to : Carbon::now()->format('Y-m-d H:i:s');
        $today = Carbon::now()->format('Y-m-d H:i:s');
        $start = Carbon::createFromFormat('Y-m-d', '2020-01-01')
        ->format('Y-m-d H:i:s');

        if ($from < $start) {
            throw new CustomException("Date Error: The from date must be greater than or equal to the date 2020-01-01");
        }
        if ($to < $start) {
            throw new CustomException("Date Error: The to date must be greater than or equal to the date 2020-01-01");
        }
        if ($from > $to) {
            throw new CustomException("Date Error: The from date must be less than or equal the to date");
        }
        if ($to > $today) {
            throw new CustomException("Date Error: The to date must be less than or equal to the today date");
        }
        $query = MotherCardTransaction::query();

        $query->join('mother_cards', 'mother_card_transactions.mother_card_id', '=', 'mother_cards.id')->join('transaction_types', 'transaction_types.id', '=', 'mother_card_transactions.transaction_type_id');
        $query->select(
            'mother_card_transactions.*',
            'mother_cards.card_number as beneficiary'
        );

        if ($beneficiary) {
            $query->where('mother_cards.card_number', 'like', '%' . $params->beneficiary . '%');
        }

        if ($provider) {
            $query->where('mother_card_transactions.clave_rastreo', 'like', '%' . $params->provider . '%');
        }

        if ($concept) {
            $query->where('mother_card_transactions.concepto', 'like', '%' . $params->concept . '%');
        }
        
        if ($from) {
            $query->whereDate('mother_card_transactions.fecha_alta', '>=', $from);
        }

        if ($to) {
            $query->whereDate('mother_card_transactions.fecha_alta', '<=', $to);
        }

        return $query->orderBy('mother_card_transactions.id', 'asc')->paginate(200);
    }

    public function filtroAllCardLoad($filter = [], $orderBy = 'created_at', $direction = 'ASC', $page = 50, $params)
    {
        $tx_id = $params->tx_id;
        $email = $params->email;
        $card_number = $params->card_number;
        $state = $params->state;
        $from = $params->from ? $params->from : Carbon::createFromFormat('Y-m-d', '2020-01-01')
            ->format('Y-m-d H:i:s');
        $to = $params->to ? $params->to : Carbon::now()->format('Y-m-d H:i:s');
        $today = Carbon::now()->format('Y-m-d H:i:s');
        $start = Carbon::createFromFormat('Y-m-d', '2020-01-01')
        ->format('Y-m-d H:i:s');

        if ($from < $start) {
            throw new CustomException("Date Error: The from date must be greater than or equal to the date 2020-01-01");
        }
        if ($to < $start) {
            throw new CustomException("Date Error: The to date must be greater than or equal to the date 2020-01-01");
        }
        if ($from > $to) {
            throw new CustomException("Date Error: The from date must be less than or equal the to date");
        }
        if ($to > $today) {
            throw new CustomException("Date Error: The to date must be less than or equal to the today date");
        }

        $query = Transaction::query();

        $query->join('accounts', 'transactions.account_id', '=', 'accounts.id');
        $query->join('clients', 'transactions.client_id', '=', 'clients.id');
        $query->select(
            'transactions.*',
            'clients.*',
            'accounts.card_number as card_number'
        );

        if ($tx_id) {
            $query->where('transactions.no_referencia_alquimia', 'like', '%' . $params->tx_id . '%');
        }

        if ($state) {
            $query->where('transactions.state', 'like', '%' . $params->state . '%');
        }

        if ($email) {
            $query->where('clients.user_json', 'like', '%' . $params->email . '%');
        }

        if ($card_number) {
            $query->where('accounts.card_number', 'like', '%' . $params->card_number . '%');
        }

        if ($from) {
            $query->whereDate('transactions.date', '>=', $from);
        }

        if ($to) {
            $query->whereDate('transactions.date', '<=', $to);
        }

        return $query->orderBy('transactions.id', 'asc')->paginate(50);
    }

    public function filtroBackAll($filter = [], $orderBy = 'created_at', $direction = 'ASC', $paginate = 10)
    {
        $query = Transaction::query();

        $query = HelperFunctions::construirQuerysFiltros($filter, $orderBy, $direction, $query);

        return $query->paginate($paginate);
    }

    public function filterClientId($id, $paginate)
    {
        $result = [];
        $list = [];
        $totalAmount = 0;
        $usdCont = 0;
        $eurCont = 0;

        foreach ($paginate as $item) {
            if ($item->client_id == $id) {
                $usdEstimated = HelperFunctions::currencyToMxnChangePrice(2, $item->amount);
                $eurEstimated = HelperFunctions::currencyToMxnChangePrice(3, $item->amount);
                $list[] = new TransactionDto($item);
                $usdCont = $usdCont + $usdEstimated;
                $eurCont = $eurCont + $eurEstimated;
                $totalAmount = $totalAmount + $item->amount;
            }
        }

        $result['list'] = $list;
        $result['totalAmount'] = $totalAmount;
        $result['totalUsdEstimated'] = round($usdCont, 2);
        $result['totalEurEstimated'] = round($eurCont, 2);

        return $result;
    }

    public function filterAdmin($paginate, $balance)
    {
        $result = [];
        $list = [];
        $totalAmount = 0;
        $usdCont = 0;
        $balanceUsdEstimated = HelperFunctions::currencyToMxnChangePrice(2, $balance);

        foreach ($paginate as $item) {
            $usdEstimated = $item->monto_ref;
            $list[] = new TransactionMotherAccDto($item);
            $usdCont = $usdCont + $usdEstimated;
            $totalAmount = $totalAmount + $item->valor_real;
        }

        $result['list'] = $list;
        $result['totalIncome'] = 0;
        $result['totalIncomeUsdEstimated'] = 0;
        $result['totalOutGoing'] = $totalAmount;
        $result['totalOutGoingUsdEstimated'] = round($usdCont, 2);
        $result['totalAvailable'] = round($balance, 2);
        $result['totalAvailableUsdEstimated'] = round($balanceUsdEstimated, 2);

        return $result;
    }

    public function filterAdminCardLoad($paginate)
    {
        $result = [];
        $list = [];
        $totalAmount = 0;
        $usdCont = 0;
        $eurCont = 0;

        foreach ($paginate as $item) {
            $usdEstimated = HelperFunctions::currencyToMxnChangePrice(2, $item->amount);
            $eurEstimated = HelperFunctions::currencyToMxnChangePrice(3, $item->amount);
            $list[] = new CardLoadDto($item);
            $usdCont = $usdCont + $usdEstimated;
            $eurCont = $eurCont + $eurEstimated;
            $totalAmount = $totalAmount + $item->amount;
        }

        $result['list'] = $list;
        $result['totalIncome'] = 0;
        $result['totalIncomeUsdEstimated'] = 0;
        $result['totalOutGoing'] = $totalAmount;
        $result['totalOutGoingUsdEstimated'] = round($usdCont, 2);
        $result['totalEurEstimated'] = round($eurCont, 2);

        return $result;
    }

    public function filterAdminbyAccount($paginate)
    {
        $result = [];
        $list = [];
        $totalAmount = 0;
        $usdCont = 0;
        $eurCont = 0;

        foreach ($paginate as $item) {
            $usdEstimated = HelperFunctions::currencyToMxnChangePrice(2, $item->amount);
            $eurEstimated = HelperFunctions::currencyToMxnChangePrice(3, $item->amount);
            $list[] = new TransactionDto($item);
            $usdCont = $usdCont + $usdEstimated;
            $eurCont = $eurCont + $eurEstimated;
            $totalAmount = $totalAmount + $item->amount;
        }

        $result['list'] = $list;
        $result['totalAmount'] = $totalAmount;
        $result['totalUsdEstimated'] = round($usdCont, 2);
        $result['totalEurEstimated'] = round($eurCont, 2);

        return $result;
    }

    public function filterAdminbyCollectionAccount($paginate)
    {
        $result = [];
        $list = [];
        $totalAmount = 0;
        $usdCont = 0;
        $eurCont = 0;

        foreach ($paginate as $item) {
            $usdEstimated = HelperFunctions::currencyToMxnChangePrice(2, $item->amount);
            $eurEstimated = HelperFunctions::currencyToMxnChangePrice(3, $item->amount);
            $list[] = new TransactionCollectionAccountDto($item);
            $usdCont = $usdCont + $usdEstimated;
            $eurCont = $eurCont + $eurEstimated;
            $totalAmount = $totalAmount + $item->amount;
        }

        $result['list'] = $list;
        $result['totalAmount'] = $totalAmount;
        $result['totalUsdEstimated'] = round($usdCont, 2);
        $result['totalEurEstimated'] = round($eurCont, 2);

        return $result;
    }

    public function notificationReload($card_number, $amount, $id_tx_alquimia, $state, $client_id)
    {
        $data = [];
        $account = Account::where('card_number', $card_number)->first();
        $transaction = Transaction::where('account_id', $account->id)->where('state', 'PENDING')->where('id_tx_alquimia', $id_tx_alquimia)->where('amount', $amount)->first();

        if ($state == 1 && $account->client_id == $client_id && $transaction->client_id == $client_id && $transaction) {
            if ($transaction->type == 'INCREMENT') {
                $data['balance before'] = round($account->balance - $amount, 2);
                $data['increment'] = $amount;
                $data['balance after'] = $account->balance;
            } elseif ($transaction->type == 'DECREMENT') {
                $data['balance before'] = round($account->balance + $amount, 2);
                $data['decrement'] = $amount;
                $data['balance after'] = $account->balance;
            }

            Transaction::where('account_id', $account->id)->where('id_tx_alquimia', $id_tx_alquimia)->where('amount', $amount)->where('state', 'PENDING')->update(['state' => 'PROCESED']);

            $data['message'] = 'Su transacción fue procesada satisfactoriamente';
            $data['state'] = 1;
        } elseif ($state == 0 && $account->client_id == $client_id && $transaction->client_id == $client_id) {
            Transaction::where('account_id', $account->id)->where('id_tx_alquimia', $id_tx_alquimia)->where('amount', $amount)->where('state', 'PENDING')->update(['state' => 'CANCELLED']);

            $data['message'] = 'Su recarga fue cancelada';
            $data['state'] = 0;
        }

        return $data;
    }

    public function notificationLoadCard($account_id, $amount, $client_id)
    {
        $data = [];
        $account = Account::where('id', $account_id)->first();
        $transaction = Transaction::where('account_id', $account->id)->where('state', 'PENDING')->where('amount', $amount)->first();

        if ($account->client_id == $client_id && $transaction->client_id == $client_id && $transaction) {
            if ($transaction->type == 'INCREMENT') {
                $data['balance before'] = round($account->balance - $amount, 2);
                $data['increment'] = $amount;
                $data['balance after'] = $account->balance;
            }

            Transaction::where('account_id', $account->id)->where('amount', $amount)->where('state', 'PENDING')->update(['state' => 'PROCESED']);

            $data['message'] = 'Su transacción fue procesada satisfactoriamente';
        } elseif ($account->client_id == $client_id && $transaction->client_id == $client_id) {
            Transaction::where('account_id', $account->id)->where('amount', $amount)->where('state', 'PENDING')->update(['state' => 'CANCELLED']);

            $data['message'] = 'Su recarga fue cancelada';
        }

        return $data;
    }

    public function notificationAdjustment($account_id, $amount, $client_id)
    {
        $data = [];
        $account = Account::where('id', $account_id)->first();
        $transaction = Transaction::where('account_id', $account->id)->where('state', 'PROCESED')->where('amount', $amount)->first();

        if ($account->client_id == $client_id && $transaction->client_id == $client_id && $transaction) {
            $data['balance before'] = round($account->balance + $amount, 2);
            $data['decrement'] = $amount;
            $data['balance after'] = $account->balance;
            $data['message'] = 'El ajuste fue procesado satisfactoriamente';
        }

        return $data;
    }

    public function listAll($clientId)
    {
        $result = [];
        $transactions = Transaction::where('client_id', $clientId)->paginate(50);

        foreach ($transactions as $key => $transaction) {
            $result[] = new TransactionDto($transaction);
        }

        return $result;
    }

    public function listTransactionsAccountMotherAll()
    {
        $result = [];
        $transactionsMotherAcc = MotherCardTransaction::paginate(50);

        foreach ($transactionsMotherAcc as $key => $transaction) {
            $result[] = new TransactionMotherAccDto($transaction);
        }

        return $result;
    }

    public function listTransactionsCardToCardAll()
    {
        $result = [];
        $transactions = Transaction::where('transaction_type_id', 2)->paginate(50);

        foreach ($transactions as $key => $transaction) {
            $result[] = new TransactionDto($transaction);
        }

        return $result;
    }

    public function listTransactionsAll()
    {
        $result = [];
        $transactions = Transaction::paginate(50);

        foreach ($transactions as $key => $transaction) {
            $result[] = new TransactionDto($transaction);
        }

        return $result;
    }

    public function listTransactionsAllCardLoad()
    {
        $result = [];
        $transactions = Transaction::orWhere('transaction_type_id',1)->orWhere('transaction_type_id',7)->paginate(50);

        foreach ($transactions as $key => $transaction) {
            $result[] = new CardLoadDto($transaction);
        }

        return $result;
    }

    public function listTransactionsCollectionSubAccount()
    {
        $result = [];
        $account = Account::where('collection_account', 1)->first();
        $transactions = Transaction::where('account_id', $account->id)->paginate(50);

        foreach ($transactions as $key => $transaction) {
            $result[] = new TransactionCollectionAccountDto($transaction);
        }

        return $result;
    }

    public function listAllMovements($account_id, $clientId)
    {
        $result = [];
        $transactions = Transaction::where('account_id', $account_id)->where('client_id', $clientId)->get();

        foreach ($transactions as $key => $transaction) {
            $result[] = new TransactionDto($transaction);
        }

        return $result;
    }

    public function listAllPendingTransactions()
    {
        $transactions = Transaction::select('amount', 'state')
            ->whereState('PENDING')
            ->get();

        return $transactions;
    }

    public function listAllErrorTransactions()
    {
        $transactions = Transaction::select('amount', 'state')
            ->whereState('ERROR')
            ->get();

        return $transactions;
    }

    public function loadCard($amount, $account_id, $user_id, $client_id, $fee_store, $fee_id)
    {
        $account = Account::where('id', $account_id)->first();
        $type = TransactionType::find(3);
        $mytime = Carbon::now();
        $time = time();

        $newTransaction = Transaction::create([
            'date' => $mytime->toDateTimeString(),
            'amount' => $amount,
            'id_tx_alquimia' => '',
            'state' => 'PENDING',
            'account_id' => $account_id,
            'client_id' => $client_id,
            'currency_id' => $account->currency_id,
            'fee_id' => $fee_id,
            'transaction_type_id' => 7,
            'user_id' => $user_id,
            'user_name' => $this->getUserName(),
            'no_referencia_alquimia' => $time,
            'folio_orden_alquimia' => '',
            'concepto' => $type->name,
            'send_amount_currency_id' => 4,
            'fee_amount' => $fee_store,
            'user_json' => $this->getUserJson(),
            'card_provider_id' => $account->card_provider_id
        ]);

        if ($newTransaction) {
            return $newTransaction;
        } else {
            return false;
        }
    }

    public function isCard($card_number)
    {
        $account = Account::select('id')->whereCardNumber($card_number)->first();

        if ($account) {
            return $account;
        } else {
            return false;
        }
    }

    public function filtroByAccountAll($filter = [], $orderBy = 'created_at', $direction = 'ASC', $paginate = 10, $params, $id)
    {
        $reference = $params->reference;
        $provider = $params->provider;
        $concept = $params->concept;
        $from = $params->from ? $params->from : Carbon::createFromFormat('Y-m-d', '2020-01-01')
            ->format('Y-m-d H:i:s');
        $to = $params->to ? $params->to : Carbon::now()->format('Y-m-d H:i:s');
        $today = Carbon::now()->format('Y-m-d H:i:s');
        $start = Carbon::createFromFormat('Y-m-d', '2020-01-01')
        ->format('Y-m-d H:i:s');

        if ($from < $start) {
            throw new CustomException("Date Error: The from date must be greater than or equal to the date 2020-01-01");
        }
        if ($to < $start) {
            throw new CustomException("Date Error: The to date must be greater than or equal to the date 2020-01-01");
        }
        if ($from > $to) {
            throw new CustomException("Date Error: The from date must be less than or equal the to date");
        }
        if ($to > $today) {
            throw new CustomException("Date Error: The to date must be less than or equal to the today date");
        }
        $query = Transaction::query();

        $query->join('transaction_types', 'transactions.transaction_type_id', '=', 'transaction_types.id')
            ->join('accounts', 'transactions.account_id', '=', 'accounts.id');
        $query->select(
            'transactions.*',
            'transaction_types.name as concept',
            'accounts.last8_digits as reference'
        );

        $query->where('transactions.account_id', $id);

        if ($reference) {
            $query->where('accounts.last8_digits', 'like', '%' . $params->reference . '%');
        }

        if ($provider) {
            $query->where('transactions.folio_orden_alquimia', 'like', '%' . $params->provider . '%');
        }

        if ($concept) {
            $query->where('transaction_types.name', 'like', '%' . $params->concept . '%');
        }

        if ($from) {
            $query->whereDate('transactions.date', '>=', $from);
        }

        if ($to) {
            $query->whereDate('transactions.date', '<=', $to);
        }

        return $query->orderBy('transactions.id', 'asc')->paginate($paginate);
    }

    public function isAccount($card_number)
    {
        $account = Account::where('card_number', $card_number)->first();

        if ($account) {
            return $account;
        } else {
            return false;
        }
    }

    public function listAllMovementsCardNumber($account_id, $paginate)
    {
        $result = [];
        $transactions = Transaction::where('account_id', $account_id)->paginate($paginate);

        foreach ($transactions as $key => $transaction) {
            $result[] = new TransactionDto($transaction);
        }

        return $result;
    }

    public function getFeeReload()
    {
        $feeId = Fee::select('id', 'fee_card_reload', 'fee_first_tx')->whereActive(true)->first();

        return $feeId;
    }

    public function getFeeTxCard()
    {
        $feeId = Fee::select('id', 'fee_card_tx', 'fee_first_tx')->whereActive(true)->first();

        return $feeId;
    }

    public function getLimitReload()
    {
        $limitId = TransferLimit::select('id', 'limit_card_reload', 'limit_first_tx')->whereActive(true)->first();

        return $limitId;
    }

    public function getLimitTxCard()
    {
        $limitId = TransferLimit::select('id', 'limit_card_tx', 'limit_first_tx')->whereActive(true)->first();

        return $limitId;
    }

    public function calcFee($amount, $fee)
    {
        $totalFee = $amount * $fee / 100;

        return $totalFee;
    }

    public function getClient()
    {
        $user = Session::get('user');
        $client = Client::where('user_id', $user->id)->first();

        return $client->id;
    }

    public function getUser()
    {
        $user = Session::get('user');

        return $user->id;
    }

    public function getUserName()
    {
        $user = Session::get('user');

        return $user->name;
    }

    public function getUserJson()
    {
        $user = Session::get('user');

        return json_encode($user);
    }

    public function getClientId($card_number)
    {
        $account = Account::where('card_number', $card_number)->first();

        if ($account) {
            return $account->client_id;
        } else {
            return false;
        }
    }

    public function getAccountBalance($card_number)
    {
        $balance = Account::select('balance')->whereCardNumber($card_number)->first();

        if ($balance) {
            return $balance->balance;
        } else {
            return 0;
        }
    }

    public function balanceDiscount($card_number, $amount, $idTx, $clientId, $feeId, $userId, $folio, $fee)
    {
        $account = Account::where('card_number', $card_number)->first();
        $type = TransactionType::find(2);
        $mytime = Carbon::now();
        $time = time();

        Transaction::create([
            'date' => $mytime->toDateTimeString(),
            'amount' => $amount,
            'id_tx_alquimia' => $idTx,
            'state' => 'PROCESED',
            'type' => 'DECREMENT',
            'account_id' => $account->id,
            'client_id' => $clientId,
            'currency_id' => $account->currency_id,
            'fee_id' => $feeId,
            'transaction_type_id' => 2,
            'concepto' => $type->name,
            'user_id' => $userId,
            'user_name' => $this->getUserName(),
            'no_referencia_alquimia' => $time,
            'folio_orden_alquimia' => $folio,
            'send_amount_currency_id' => 1,
            'fee_amount' => $fee,
            'user_json' => $this->getUserJson(),
            'card_provider_id' => $account->card_provider_id
        ]);
    }

    public function balanceDiscountCollectionAccount($account_id, $amount, $clientId, $feeId, $userId, $fee)
    {
        $account = Account::find($account_id);
        $type = TransactionType::find(2);
        $mytime = Carbon::now();
        $time = time();

        $transaction = Transaction::create([
            'date' => $mytime->toDateTimeString(),
            'amount' => $amount,
            'id_tx_alquimia' => '',
            'state' => 'PROCESED',
            'type' => 'DECREMENT',
            'account_id' => $account->id,
            'client_id' => $clientId,
            'currency_id' => $account->currency_id,
            'fee_id' => $feeId,
            'transaction_type_id' => 2,
            'concepto' => $type->name,
            'user_id' => $userId,
            'user_name' => $this->getUserName(),
            'no_referencia_alquimia' => $time,
            'folio_orden_alquimia' => '',
            'send_amount_currency_id' => 1,
            'fee_amount' => $fee,
            'user_json' => $this->getUserJson(),
            'card_provider_id' => $account->card_provider_id,
        ]);

        if ($transaction) {
            return true;
        } else {
            return false;
        }
    }

    public function isNewAccount($card_number)
    {
        $account = Account::where('card_number', $card_number)->first();

        if (!$account) {
            throw new CustomException("Account Error: The account does not exist");
        }

        $tx = DB::table('transactions')->where('account_id', $account->id)->exists();

        if ($tx) {
            return false;
        } else {
            return true;
        }
    }

    public function isNewAccountId($account_id)
    {
        $account = Account::find($account_id);

        if (!$account) {
            throw new CustomException("Account Error: The account does not exist");
        }

        $tx = DB::table('transactions')->where('account_id', $account->id)->exists();

        if ($tx) {
            return false;
        } else {
            return true;
        }
    }

    public function addBalanceToCollectionAccount($amount, $idTx, $clientId, $feeId, $userId, $folio, $fee)
    {
        $account = Account::where('collection_account', true)->first();
        $type = TransactionType::find(3);

        if (!$account) {
            throw new CustomException("Account Error: The account does not exist");
        }

        $mytime = Carbon::now();
        $time = time();

        Transaction::create([
            'date' => $mytime->toDateTimeString(),
            'amount' => $amount,
            'id_tx_alquimia' => $idTx,
            'state' => 'PROCESED',
            'account_id' => $account->id,
            'client_id' => $clientId,
            'currency_id' => $account->currency_id,
            'fee_id' => $feeId,
            'transaction_type_id' => 3,
            'user_id' => $userId,
            'user_name' => $this->getUserName(),
            'no_referencia_alquimia' => $time,
            'folio_orden_alquimia' => $folio,
            'concepto' => $type->name,
            'send_amount_currency_id' => 1,
            'fee_amount' => $fee,
            'user_json' => $this->getUserJson(),
            'card_provider_id' => $account->card_provider_id
        ]);

        return true;
    }

    public function isValidTransferFirst($amount, $limit, $fee)
    {
        $amountUsd = HelperFunctions::currencyToMxnChangePrice(2, $amount);
        $amountMxn = HelperFunctions::currencyToCustomChangePrice(2, 1, $limit);

        if ($amountUsd >= $limit && $limit <= $fee) {
            return true;
        }

        throw new CustomException("Transfer Error: The amount is not enough for the first transfer the minimum value is " . $amountMxn . " MXN.");
    }

    public function isValidTransfer($amount, $limit)
    {
        $amountUsd = HelperFunctions::currencyToMxnChangePrice(2, $amount);

        if ($amountUsd >= $limit) {
            return true;
        }

        throw new CustomException("Transfer Error: The amount is not enough for the transfer the minimum value " . $limit);
    }

    public function getAmountFirst($amount, $fee1, $fee2)
    {
        $feeEnd = HelperFunctions::currencyToCustomChangePrice(2, 1, $fee2);

        if ($amount - $fee1 - $feeEnd < 0) {
            throw new CustomException("Transfer Error: The amount is not enough for the transfer the minimum value " . $feeEnd + $fee1);
        } else {
            return $amount - $fee1 - $feeEnd;
        }
    }

    public function collectionBalance($fee1, $fee2)
    {
        $feeEnd = HelperFunctions::currencyToCustomChangePrice(2, 1, $fee2);

        return $fee1 + $feeEnd;
    }

    public function getAmountFirstMotherAccount($amount, $fee1, $fee2)
    {
        $feeEnd = HelperFunctions::currencyToCustomChangePrice(2, 1, $fee2);

        if ($amount - $fee1 - $feeEnd < 0) {
            throw new CustomException("Transfer Error: The amount is not enough for the transfer the minimum value " . $feeEnd + $fee1);
        } else {
            return $amount - $fee1 - $feeEnd;
        }
    }

    public function getAmount($amount, $fee1)
    {
        if ($amount - $fee1 < 0) {
            throw new CustomException("Transfer Error: The amount is not enough for the transfer");
        } else {
            return $amount - $fee1;
        }
    }

    public function isClient($client_id)
    {
        $client = Client::where('id', $client_id)->first();

        if (!$client) {
            throw new CustomException("User Error: The client does not exist");
        }

        return true;
    }

    public function isAccountId($account_id)
    {
        $account = Account::where('id', $account_id)->first();

        if (!$account) {
            throw new CustomException("Account Error: The account does not exist");
        }

        return true;
    }

    public function accountClientExist($account_id, $client_id)
    {
        $account = Account::where('id', $account_id)->where('client_id', $client_id)->first();

        if (!$account) {
            throw new CustomException("Account Error: The account does not exist");
        }

        return true;
    }

    public function getClientAccounts($client_id)
    {
        $account = Account::where('client_id', $client_id)->paginate(20);

        if (!$account) {
            throw new CustomException("User Error: The user with account does not exist");
        }

        return true;
    }

    public function isConcept($concept_id)
    {
        $cocnept = FeeConcept::find($concept_id);

        if (!$cocnept) {
            throw new CustomException("Fee Concept Error: The fee concept does not exist");
        }

        return true;
    }

    public function updateFields($request)
    {
        $cocnept = FeeConcept::find($request->fee_concept_id);
        $input = [];

        if ($cocnept->name == "Refund") {
            if (!$request->amount || $request->amount == "") {
                throw new CustomException("Amount Error: The amount has not been specified");
            }

            $input['approx_usd'] = HelperFunctions::currencyToMxnChangePrice(2, $request->amount) + $cocnept->fee;
        } else {
            $input['approx_usd'] = HelperFunctions::currencyToCustomChangePrice(2, 1, $cocnept->fee);
        }

        $input['fee_mxn'] = HelperFunctions::currencyToCustomChangePrice(2, 1, $cocnept->fee);

        return $input;
    }

    public function validDecrementBalanceAccount($request)
    {
        $cocnept = FeeConcept::find($request->fee_concept_id);
        $account = Account::find($request->account_id);

        if (!$account) {
            throw new CustomException("Account Error: The account does not exist");
        }

        if (!$cocnept) {
            throw new CustomException("Fee Concept Error: The fee concept does not exist");
        }

        if ($cocnept->name == "Refund") {
            if (!$request->amount || $request->amount == "") {
                throw new CustomException("Amount Error: The amount has not been specified");
            }

            if ($account->balance - $request->amount - HelperFunctions::currencyToCustomChangePrice(2, 1, $cocnept->fee) < 0) {
                throw new CustomException("Balance Error: The account does not have a sufficient balance to execute the operation");
            }

            return true;
        } elseif ($cocnept->name != "Refund" && $cocnept->name != "") {
            if ($account->balance - HelperFunctions::currencyToCustomChangePrice(2, 1, $cocnept->fee) < 0) {
                throw new CustomException("Balance Error: The account does not have a sufficient balance to execute the operation");
            }

            return true;
        }
    }

    public function decrementBalanceAccount($request, $userId)
    {
        $cocnept = FeeConcept::find($request->fee_concept_id);
        $type = TransactionType::find(6);
        $account = Account::find($request->account_id);
        $amount = $request->amount + HelperFunctions::currencyToCustomChangePrice(2, 1, $cocnept->fee);
        $fee = HelperFunctions::currencyToCustomChangePrice(2, 1, $cocnept->fee);

        if (!$account) {
            throw new CustomException("Account Error: The account does not exist");
        }

        if (!$cocnept) {
            throw new CustomException("Fee Concept Error: The fee concept does not exist");
        }

        if ($cocnept->name == "Refund") {
            $mytime = Carbon::now();
            $time = time();

            Transaction::create([
                'date' => $mytime->toDateTimeString(),
                'amount' => $amount,
                'id_tx_alquimia' => null,
                'state' => 'PROCESED',
                'type' => 'DECREMENT',
                'account_id' => $request->account_id,
                'client_id' => $request->client_id,
                'currency_id' => $account->currency_id,
                'fee_id' => null,
                'transaction_type_id' => 6,
                'user_id' => $userId,
                'user_name' => $this->getUserName(),
                'no_referencia_alquimia' => $time,
                'folio_orden_alquimia' => null,
                'concepto' => $type->name,
                'send_amount_currency_id' => 1,
                'fee_amount' => $fee,
                'user_json' => $this->getUserJson(),
                'card_provider_id' => $account->card_provider_id
            ]);

            $this->addBalanceToCollectionAccountAdjustment($amount, $request->client_id, $userId, $fee);
            $data = $this->notificationAdjustment($request->account_id, $amount, $request->client_id);

            return $data;
        } elseif ($cocnept->name != "Refund" && $cocnept->name != "") {
            $mytime = Carbon::now();
            $time = time();

            Transaction::create([
                'date' => $mytime->toDateTimeString(),
                'amount' => $fee,
                'id_tx_alquimia' => null,
                'state' => 'PROCESED',
                'account_id' => $request->account_id,
                'client_id' => $request->client_id,
                'currency_id' => $account->currency_id,
                'fee_id' => null,
                'transaction_type_id' => 6,
                'user_id' => $userId,
                'user_name' => $this->getUserName(),
                'no_referencia_alquimia' => $time,
                'folio_orden_alquimia' => null,
                'concepto' => $cocnept->name,
                'send_amount_currency_id' => 1,
                'fee_amount' => $fee,
                'user_json' => $this->getUserJson(),
                'card_provider_id' => $account->card_provider_id
            ]);

            $this->addBalanceToCollectionAccountAdjustment($fee, $request->client_id, $userId, $fee);
            $data = $this->notificationAdjustment($request->account_id, $fee, $request->client_id);

            return $data;
        }
    }

    public function addBalanceToCollectionAccountAdjustment($amount, $clientId, $userId, $fee)
    {
        $account = Account::where('collection_account', true)->first();
        $cocnept = TransactionType::find(3);

        if (!$account) {
            throw new CustomException("Account Error: The account does not exist");
        }

        $mytime = Carbon::now();
        $time = time();

        $transaction = Transaction::create([
            'date' => $mytime->toDateTimeString(),
            'amount' => $amount,
            'id_tx_alquimia' => null,
            'state' => 'PROCESED',
            'account_id' => $account->id,
            'client_id' => $clientId,
            'currency_id' => $account->currency_id,
            'fee_id' => null,
            'transaction_type_id' => 3,
            'user_id' => $userId,
            'user_name' => $this->getUserName(),
            'no_referencia_alquimia' => $time,
            'folio_orden_alquimia' => null,
            'send_amount_currency_id' => 1,
            'fee_amount' => $fee,
            'concepto' => $cocnept->name,
            'user_json' => $this->getUserJson(),
            'card_provider_id' => $account->card_provider_id
        ]);

        if ($transaction) {
            return true;
        } else {
            return false;
        }
    }

    public function users()
    {
        $clients = Client::all();
        $result = [];

        foreach ($clients as $key => $client) {
            $result[] = new UserDto($client->id, $client->user_json);
        }

        return $result;
    }

    public function obtenerBalanceCollectionAccount()
    {
        $account = Account::where('collection_account', 1)->first();

        if (!$account) {
            throw new CustomException("Collection Account Error: The account does not exist");
        }

        return $account;
    }

    public function validAmount($amount)
    {
        if ($amount < 1) {
            throw new CustomException("Amount Error: The value must be greater than zero");
        }

        return true;
    }

    public function validAmountVsBalanceCollevtionAccount($amount, $balance)
    {
        if ($amount > $balance) {
            throw new CustomException("Amount Error: the value has to be equal or less than the collection account balance");
        }

        return true;
    }

    public function totalsMovemenentsCollectionAccount()
    {
        $totalIncome = 0;
        $totalOutgoing = 0;
        $totalAvailable = 0;
        $collection_account = Account::where('collection_account', 1)->first();
        $collectionsIncomes = Transaction::where('account_id', $collection_account->id)->where('type', 'INCREMENT')->get();
        $collectionsOutgoings = Transaction::where('account_id', $collection_account->id)->where('type', 'DECREMENT')->get();

        foreach ($collectionsIncomes as $key => $collectionsIncome) {
            $totalIncome += $collectionsIncome->amount;
        }
        foreach ($collectionsOutgoings as $key => $collectionsOutgoing) {
            $totalOutgoing += $collectionsOutgoing->amount;
        }

        $totalAvailable = $collection_account->balance;

        return new TotalsCollectionAccountDto(round($totalIncome, 2), round($totalOutgoing, 2), round($totalAvailable, 2));
    }

    public function totalsMovemenentsMotherAccount($saldo)
    {
        $totalIncome = 0;
        $totalOutgoing = 0;
        $totalAvailable = 0;
        $collectionsIncomes = MotherCardTransaction::where('transaction_type_id', 4)->get();
        $collectionsOutgoings = MotherCardTransaction::where('transaction_type_id', 5)->get();

        foreach ($collectionsIncomes as $key => $collectionsIncome) {
            $totalIncome += $collectionsIncome->valor_real;
        }
        foreach ($collectionsOutgoings as $key => $collectionsOutgoing) {
            $totalOutgoing += $collectionsOutgoing->valor_real;
        }

        $totalOutgoingEnd = -1 * $totalOutgoing;

        $totalAvailable = $saldo;

        return new TotalsMotherAccountDto(round($totalIncome, 2), round($totalOutgoing, 2), round($totalAvailable, 2));
    }
    
}
