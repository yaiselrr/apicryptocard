<?php

namespace App\Repositories;

use App\Dto\AccountBackDto;
use App\Dto\AccountDto;
use App\Dto\CollectionAccountDto;
use App\Dto\TotalsAccountDto;
use App\Dto\TransactionDto;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Client;
use App\Http\Controllers\Helpers\HelperFunctions;
use \Prettus\Repository\Eloquent\BaseRepository as BaseRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AccountRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'card_number',
        'last8_digigts',
        'balance',
        'active',
        'stolen',
        'id_account',
        'client_id',
        'mother_card_id',
        'card_provider_id',
        'currency_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Account::class;
    }

    public function filtro($filter = [], $orderBy = 'created_at', $direction = 'ASC', $paginate = 10, $params)
    {
        
        $query = Account::query();

        if ($params->card_number) {
            $query->where('name', 'LIKE', '%' . $params->name . '%');
        }
        if ($params->stolen) {
            $query->where('active', '=', $params->active);
        }

        $query->orderBy($orderBy, $direction);
        return $query->paginate($paginate);
    }

    public function filtroAll($filter = [], $orderBy = 'created_at', $direction = 'ASC', $paginate = 50, $params)
    {
        $card_number = $params->card_number;
        $user = $params->user;

        $query = Account::query();
        $query->join('clients', 'accounts.client_id', '=', 'clients.id');
        $query->select(
            'accounts.*'
        );

        if ($card_number) {
            $query->where('accounts.card_number', 'like', '%' . $params->card_number . '%');
        }

        if ($user) {
            $query->where('clients.user_json', 'like', '%' . $params->user . '%');
        }

        return $query->orderBy('accounts.id', 'asc')->paginate($paginate);
    }

    public function listAllMovements($account_id, $clientId)
    {
        $transactions = Transaction::where('account_id', $account_id)->where('client_id', $clientId)->paginate(50);
        $result = [];

        foreach ($transactions as $key => $transaction) {
            $result[] = new TransactionDto($transaction);
        }

        return $transactions;
    }

    public function listAllMovementsAccountCollection($account_id)
    {
        $transactions = Transaction::where('account_id', $account_id)->paginate(50);
        $result = [];

        foreach ($transactions as $key => $transaction) {
            $result[] = new TransactionDto($transaction);
        }

        return $transactions;
    }

    public function listAllAccounts($paginate = 10)
    {
        $accounts = Account::paginate(50);

        foreach ($accounts as $key => $account) {
            if (!$account->collection_account) {
                $movements = $this->listAllMovements($account->id, $account->client_id);
                $result[] = new AccountDto($account, $movements);
            }
        }

        return $result;
    }

    public function listAllClientAccount($client_id)
    {
        $accounts = Account::where('client_id', $client_id)->paginate(50);

        foreach ($accounts as $key => $account) {
            $movements = $this->listAllMovements($account->id, $account->client_id);
            $result[] = new AccountDto($account, $movements);
        }

        return $result;
    }

    public function dataCollectionAccount()
    {
        $account = Account::where('collection_account', 1)->first();

        return new CollectionAccountDto($account);
    }

    public function getClientId($id)
    {
        $accounts = Account::where('client_id', $id)->get();
        $totalBalance = Account::where('client_id', $id)->get()->sum('balance');
        $result = [];
        $list = [];
        $usdCont = 0;

        foreach ($accounts as $key => $account) {
            if (!$account->collection_account) {
                $usdEstimated = HelperFunctions::currencyToMxnChangePrice(2, $account->balance);
                $movements = $this->listAllMovements($account->id, $id);
                $list[] = new AccountDto($account, $movements);
                $usdCont = $usdCont + $usdEstimated;
            }
        }
        $result['list'] = $list;
        $result['totalBalance'] = $totalBalance;
        $result['totalUsdEstimated'] = $usdCont;

        return $result;
    }

    public function filterClientId($id, $paginate)
    {
        $result = [];
        $list = [];
        $totalBalance = 0;
        $usdCont = 0;
        $eurCont = 0;

        foreach ($paginate as $item) {
            if ($item->client_id == $id) {
                $movements = $this->listAllMovements($item->id, $id);
                $usdEstimated = HelperFunctions::currencyToMxnChangePrice(2, $item->balance);
                $eurEstimated = HelperFunctions::currencyToMxnChangePrice(3, $item->balance);
                $list[] = new AccountDto($item, $movements);
                $usdCont = $usdCont + $usdEstimated;
                $eurCont = $eurCont + $eurEstimated;
                $totalBalance = $totalBalance + $item->balance;
            }
        }
        $result['list'] = $list;

        $result['totalBalance'] = $totalBalance;
        $result['totalUsdEstimated'] = round($usdCont, 2);
        $result['totalEurEstimated'] = round($eurCont, 2);

        return $result;
    }

    public function filterAdmin($paginate)
    {
        $result = [];
        $list = [];
        $totalBalance = 0;
        $usdCont = 0;
        $eurCont = 0;

        foreach ($paginate as $item) {
            $movements = [];
            $usdEstimated = HelperFunctions::currencyToMxnChangePrice(2, $item->balance);
            $eurEstimated = HelperFunctions::currencyToMxnChangePrice(3, $item->balance);
            $list[] = new AccountDto($item, $movements);
            $usdCont = $usdCont + $usdEstimated;
            $eurCont = $eurCont + $eurEstimated;
            $totalBalance = $totalBalance + $item->balance;
        }
        $result['list'] = $list;

        $result['totalBalance'] = $totalBalance;
        $result['totalUsdEstimated'] = round($usdCont, 2);
        $result['totalEurEstimated'] = round($eurCont, 2);

        return $result;
    }

    public function getAccountClientId($id, $clientId)
    {
        $account = Account::where('client_id', $clientId)->find($id);

        return $account;
    }

    public function stolenCard($account)
    {
        if ($account->id_account != null && $account->active == true  && $account->stolen == false) {
            $account->active = false;
            $account->stolen = true;
            $account->save();

            return true;
        }

        return false;
    }

    public function mailRegister()
    {
        $user = Auth::user();

        return $user->email;
    }

    public function toValidateCard($card_number, $code)
    {
        $cardAccount = Account::select('card_number')->whereCardNumber($card_number)->first();

        if ($cardAccount == null) {
            return false;
        }

        return true;
    }

    public function makeArray($card_number, $clientId)
    {
        $input = [];
        $input['last8_digits'] = substr($card_number, -8);
        $input['balance'] = 0.00;
        $input['active'] = 1;
        $input['stolen'] = 0;
        $input['id_account'] = null;
        $input['client_id'] = $clientId;
        $input['mother_card_id'] = 1;
        $input['card_provider_id'] = 1;
        $input['currency_id'] = 1;

        return $input;
    }

    public function validUser($code)
    {
        $user = User::where('unique_code', $code)->first();

        if (!$user) {
            return false;
        } else {
            return $user;
        }
    }

    public function validClient($user_id)
    {
        $client = Client::where('user_id', $user_id)->first();

        if (!$client) {
            return false;
        } else {
            return $client;
        }
    }

    public function getAllAccountClientId($clientId, &$totalAvailableBalance, &$totalRefUsd, &$totalRefEur)
    {
        $accounts = Account::where('client_id', $clientId)->get();

        $result = [];
        $totalAvailableBalance = 0;
        $totalRefUsd = 0;
        $totalRefEur = 0;
        foreach ($accounts as $account) {
            $accountDto = new AccountBackDto($account);
            $totalAvailableBalance += $accountDto->balance;
            $totalRefUsd += $accountDto->usd;
            $totalRefEur += $accountDto->eur;

            $result[] = $accountDto;
        }

        return $result;
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

    public function getTotalsAccounts($client)
    {
        $accounts = Account::where('client_id', $client)->get();
        $result =0;

        foreach ($accounts as $key => $account) {
            $result = $result + $account->balance;
        }

        return new TotalsAccountDto($result);
    }
}
