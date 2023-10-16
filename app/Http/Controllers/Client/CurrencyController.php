<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Helpers\HelperFunctions;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CurrencyController extends Controller
{

    public function convertMxnToUsd(Request $request)
    {
        $validacion = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'amount' => 'required'
        ]);

        if ($validacion->fails()) {
            return response()->json(['errors' => $validacion->errors()],422);
        }

        $usdAmount = HelperFunctions::currencyToCustomChangePrice(1, 2, $request->amount);
        return response()->json(['data' => ['usd_amount' => $usdAmount]], 200);
    }

    public function convertUsdToMxn(Request $request)
    {
        $validacion = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'amount' => 'required'
        ]);

        if ($validacion->fails()) {
            return response()->json(['errors' => $validacion->errors()],422);
        }

        $mxnAmount = HelperFunctions::currencyToCustomChangePrice(2, 1, $request->amount);
        return response()->json(['data' => ['mxn_amount' => $mxnAmount]], 200);
    }
}
