<?php

namespace App\Repositories;

use App\Dto\AmountFeeRefDto;
use App\Dto\FeeDto;
use App\Models\Fee;
use App\Http\Controllers\Helpers\HelperFunctions;
use \Prettus\Repository\Eloquent\BaseRepository as BaseRepository;
use Illuminate\Support\Facades\Session;
use App\Exceptions\CustomException;
use App\Models\Account;
use Illuminate\Support\Facades\DB;

class FeeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'fee_card_reload',
        'fee_card_tx',
        'fee_first_tx'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Fee::class;
    }

    public function filtro($filter = [], $orderBy = 'created_at', $direction = 'ASC', $paginate = 10)
    {
        $query = Fee::query();
        $query = HelperFunctions::construirQuerysFiltros($filter, $orderBy, $direction, $query);
        return $query->paginate($paginate);
    }

    public function activeFee()
    {
        $fee = Fee::where('active', 1)->first();

        return new FeeDto($fee);
    }

    public function listFeesAll()
    {
        $fees = Fee::all();
        $result = [];

        foreach ($fees as $key => $fee) {
            $result[] = new FeeDto($fee);
        }

        return $result;
    }

    public function getUser()
    {
        $user = Session::get('user');

        return $user->id;
    }

    public function getFeeFromAmount($amount, $type, $currency, $account_id)
    {
        if ($amount < 1) {
            throw new CustomException("Amount Error: The value must be greater than zero");
        }
        if ($type > 1 || $type < 0) {
            throw new CustomException("Fee Error: The value must be between zero and one");
        }
        if ($currency != 'mxn' && $currency != 'usd') {
            throw new CustomException("Currency Error: The value must be msx or usd");
        }
        if (!$this->accountExist($account_id)) {
            throw new CustomException("Account Error: The account does not exist");
        }

        $accountNew = $this->isNewAccount($account_id);
        
        $fee = Fee::where('active', 1)->first();

        if ($currency == 'usd') {
            if ($type == 1) {
                $getFee = $amount * $fee->fee_card_reload / 100;

                $accountNew ? $totalTransfer = $amount - $getFee - $fee->fee_first_tx : $totalTransfer = $amount - $getFee;
                $accountNew ? $getFee = $getFee + $fee->fee_first_tx : $getFee;

                $aproxMxn = HelperFunctions::currencyToCustomChangePrice(2, 1, $totalTransfer);

                return new AmountFeeRefDto($amount, $getFee, $totalTransfer, $aproxUsd = '', $aproxMxn);
            }
            if ($type == 0) {
                $getFee = $amount * $fee->fee_card_tx / 100;

                $accountNew ? $totalTransfer = $amount - $getFee - $fee->fee_first_tx : $totalTransfer = $amount - $getFee;
                $accountNew ? $getFee = $getFee + $fee->fee_first_tx : $getFee;

                $aproxMxn = HelperFunctions::currencyToCustomChangePrice(2, 1, $totalTransfer);

                return new AmountFeeRefDto($amount, $getFee, $totalTransfer, $aproxUsd = '', $aproxMxn);
            }
        } elseif ($currency = 'mxn') {
            if ($type == 1) {
                $getFee = $amount * $fee->fee_card_reload / 100;

                $accountNew ? $totalTransfer = $amount - $getFee - $fee->fee_first_tx : $totalTransfer = $amount - $getFee;
                $accountNew ? $getFee = $getFee + $fee->fee_first_tx : $getFee;

                $aproxUsd = HelperFunctions::currencyToMxnChangePrice(2, $totalTransfer);

                return new AmountFeeRefDto($amount, $getFee, $totalTransfer, $aproxUsd, $aproxMxn = '');
            }
            if ($type == 0) {
                $getFee = $amount * $fee->fee_card_tx / 100;

                $accountNew ? $totalTransfer = $amount - $getFee - $fee->fee_first_tx : $totalTransfer = $amount - $getFee;
                $accountNew ? $getFee = $getFee + $fee->fee_first_tx : $getFee;
                
                $totalTransfer = $amount - $getFee;
                $aproxUsd = HelperFunctions::currencyToMxnChangePrice(2, $totalTransfer);

                return new AmountFeeRefDto($amount, $getFee, $totalTransfer, $aproxUsd, $aproxMxn = '');
            }
        }
    }

    public function isNewAccount($account_id)
    {
        $account = Account::find($account_id);

        $tx = DB::table('transactions')->where('account_id', $account->id)->exists();

        if (!$tx) {
            return true;
        } else {
            return false;
        }
    }

    public function accountExist($account_id)
    {
        $account = Account::find($account_id);

        if (!$account) {
            return false;
        } else {
            return true;
        }
    }
}
