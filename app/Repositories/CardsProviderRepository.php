<?php

namespace App\Repositories;

use App\Dto\AccountDto;
use App\Dto\CardLoadDto;
use App\Dto\TransactionDto;
use App\Exceptions\CustomException;
use App\Models\CardsProvider;
use App\Models\MotherCard;
use App\Http\Controllers\Helpers\HelperFunctions;
use App\Models\Account;
use App\Models\Transaction;
use Carbon\Carbon;
use \Prettus\Repository\Eloquent\BaseRepository as BaseRepository;
use Illuminate\Support\Facades\Session;

class CardsProviderRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CardsProvider::class;
    }

    public function filtro($filter = [], $orderBy = 'created_at', $direction = 'ASC', $paginate = 10){
        $query = CardsProvider::query();
        $query = HelperFunctions::construirQuerysFiltros($filter, $orderBy, $direction, $query);
        return $query->paginate($paginate);
    }

    public function cuentasMadres($card_provider_id){
        $this->getProvider($card_provider_id);

        $motherCards = MotherCard::where('card_provider_id',$card_provider_id)->get();

        return $motherCards;
    }

    public function getAccountsByProvider($card_provider_id){
        $this->getProvider($card_provider_id);

        $accounts = Account::where('card_provider_id',$card_provider_id)->get();

        foreach ($accounts as $key => $account) {
            if (!$account->collection_account) {
                $movements = $this->listAllMovements($account->id);
                $result[] = new AccountDto($account, $movements);
            }
        }

        return $result;
    }

    public function getTransactionsCollectionAccountsByProvider($card_provider_id){
        $this->getProvider($card_provider_id);
        
        $accounts = Account::where('card_provider_id',$card_provider_id)->where('collection_account',1)->first();

        return $this->listAllMovementsAccountCollection($accounts->id);
    }

    public function getTransactionsCardLoadByProvider($card_provider_id){
        $this->getProvider($card_provider_id);

        $transactions = Transaction::where('card_provider_id', $card_provider_id)->where('transaction_type_id', 1)->paginate(50);
        $result = [];

        foreach ($transactions as $key => $transaction) {
            $result[] = new TransactionDto($transaction);
        }

        return $transactions;
    }

    public function listAllMovements($account_id)
    {
        $transactions = Transaction::where('account_id', $account_id)->paginate(50);
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

    public function getProvider($card_provider_id)
    {
        $provider = CardsProvider::find($card_provider_id);

        if (!$provider) {
            throw new CustomException("Card Provider Error: The card provider does not exist");
        }

        return true;
    }

    public function globalBalance()
    {
        $providers = CardsProvider::all();

        $cont_mother_accounts = 0;
        $cont_collection_accounts = 0;

        foreach ($providers as $key => $providers) {
            $motherCards = MotherCard::where('card_provider_id',$providers->id)->get();
            $collectionAccounts = Account::where('card_provider_id',$providers->id)->where('collection_account',1)->get();

            foreach ($motherCards as $key => $motherCard) {
                $cont_mother_accounts = $cont_mother_accounts + $motherCard->balance;
            }
            foreach ($collectionAccounts as $key => $collectionAccount) {
                $cont_collection_accounts = $cont_collection_accounts + $collectionAccount->balance;
            }
        }   

        return HelperFunctions::currencyToMxnChangePrice(2,$cont_mother_accounts + $cont_collection_accounts);
    }

    public function filtroAllCardLoad($filter = [], $orderBy = 'created_at', $direction = 'ASC', $page = 50, $params, $card_provider_id)
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

        $query->where('transactions.card_provider_id', $card_provider_id);

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

    public function getUser()
    {
        $user = Session::get('user');

        return $user->id;
    }
}
