<?php

namespace App\Http\Controllers\Helpers;

use App\Exceptions\CustomException;
use App\Models\Currency;
use App\Models\PriceExchangerCrypto;
use App\Models\PriceExchangerCurrency;

class HelperFunctions
{

    public static function construirQuerysFiltros($filter, $orderBy, $direction, $query)
    {
        foreach ($filter as $campo => $val) {
            if (is_array($val)) {
                list($campo, $condicion, $valor) = $val;
                $query->where($campo, $condicion, $valor);
            } else {
                $query->where($campo, '=', $val);
            }
        }
        $query->orderBy($orderBy, $direction);
        return $query;
    }

    public static function existeFieldInFilter($field, $filter, &$value)
    {
        foreach ($filter as $campo => $val) {
            if (is_array($val)) {
                if ($val[0] == $field) {
                    $value = $val[2];
                    return true;
                }
            } else {
                if ($campo == $field) {
                    $value = $val;
                    return true;
                }
            }
        }

        $value = false;
        return false;
    }

    public static function currencyToMxnChangePrice($currencyId, $amount = 1)
    {
        $currency = Currency::find($currencyId);

        if (!$currency) {
            throw  new CustomException('The Currency don\'t exist.');
        } elseif ($currency->abbreviation == 'MXN') {
            return 1 * $amount;
        }

        if ($amount == 0) {
            return 0;
        }

        $lastPrice = PriceExchangerCurrency::where('currency_id', $currencyId)->orderBy('created_at', 'desc')->first();

        if (!$lastPrice) {
            throw  new CustomException('The Currencies rate calculation failed');
        }

        $result = round($lastPrice->price * $amount, 2);
        //        return $result > 0 ? $result - 0.1 : $result + 0.1;
        return $result;
    }

    public static function currencyToCustomChangePrice($currencyIdOrigin, $currencyIdDestiny, $amount = 1)
    {
        $currencyFrom = Currency::find($currencyIdOrigin);
        $currencyTo = Currency::find($currencyIdDestiny);

        if (!$currencyFrom) {
            throw  new CustomException('The Currency origin don\'t exist.');
        }
        if (!$currencyTo) {
            throw  new CustomException('The Currency destiny don\'t exist.');
        }
        if ($amount == 0) {
            return 0;
        }
        if ($currencyFrom->abbreviation == $currencyTo->abbreviation) {
            return 1 * $amount;
        }
        if ($currencyFrom->abbreviation == 'USD' &&  $currencyTo->abbreviation == 'USDT') {
            return 1 * $amount;
        }
        if ($currencyFrom->abbreviation == 'USDT' &&  $currencyTo->abbreviation == 'USD') {
            return 1 * $amount;
        }
        if ($currencyFrom->abbreviation == 'MXN') {
            $lastPriceTo = PriceExchangerCurrency::where('currency_id', $currencyIdDestiny)->orderBy('created_at', 'desc')->first();

            if (!$lastPriceTo) {
                throw  new CustomException('The Currencies rate calculation failed');
            }

            return round($lastPriceTo->price * $amount, 2);
        }
        if ($currencyTo->abbreviation == 'MXN') {
            $lastPriceTo = PriceExchangerCurrency::where('currency_id', $currencyIdOrigin)->orderBy('created_at', 'desc')->first();

            if (!$lastPriceTo) {
                throw  new CustomException('The Currencies rate calculation failed');
            }
            return round($amount / $lastPriceTo->price, 2);
        }

        $lastPriceFrom = PriceExchangerCurrency::where('currency_id', $currencyIdOrigin)->orderBy('created_at', 'desc')->first();

        if (!$lastPriceFrom) {
            throw  new CustomException('The Currencies rate calculation failed');
        }

        $lastPriceTo = PriceExchangerCurrency::where('currency_id', $currencyIdDestiny)->orderBy('created_at', 'desc')->first();

        if (!$lastPriceTo) {
            throw  new CustomException('The Currencies rate calculation failed');
        }

        $priceToMXN = round($lastPriceFrom->price * $amount, 2);

        return round($priceToMXN / $lastPriceTo, 2);
    }

    public static function cryptoCurrencyToChangePrice($currencyIdOrigin, $currencyIdDestiny, $amount = 1)
    {
        $currencyFrom = Currency::find($currencyIdOrigin);
        $currencyTo = Currency::find($currencyIdDestiny);

        if (!$currencyFrom) {
            throw  new CustomException('The Currency origin don\'t exist.');
        }
        if ($currencyFrom->crypto != 1) {
            throw  new CustomException('The Currency Crypto origin don\'t exist.');
        }
        if ($currencyTo->crypto != 1) {
            throw  new CustomException('The Currency Crypto destiny don\'t exist.');
        }
        if (!$currencyTo) {
            throw  new CustomException('The Currency destiny don\'t exist.');
        }
        if ($amount == 0) {
            return 0;
        }
        if ($currencyFrom->abbreviation == $currencyTo->abbreviation) {
            return 1 * $amount;
        }
        if ($currencyFrom &&  $currencyTo) {
            $convert = PriceExchangerCrypto::where('ccy1', $currencyFrom->id)->where('ccy2', $currencyTo->id)->orderBy('created_at', 'desc')->first();

            if (!$convert) {
                throw  new CustomException('The Currencies rate calculation failed of USDT to ETH');
            }

            $result = $convert->price * $amount;

            return $result;
        }
    }

    public static function getPasswFromCardNumber($cardNumber)
    {
        $passwordKey = ['A', 'h', 'G', '.', '#', '7', 'e', 'T', '*', 'l'];

        $pass = '';
        $countOfRareCharacters = 0;
        for ($i = 0; $i < strlen($cardNumber); $i++) {
            $newChar = $passwordKey[$cardNumber[$i] * 1];
            if ($newChar == '.' || $newChar == '*' || $newChar == '#') {
                if ($countOfRareCharacters == 0) {
                    $countOfRareCharacters++;
                } else {
                    if ($countOfRareCharacters == 1)
                        $newChar = 'y';
                    if ($countOfRareCharacters == 2)
                        $newChar = 'r';
                    if ($countOfRareCharacters >= 3)
                        $newChar = 'k';
                    if ($countOfRareCharacters >= 3)
                        $newChar = 'o';

                    $countOfRareCharacters++;
                }
            }

            $pass .= $newChar;
        }

        return $pass;
    }
}
