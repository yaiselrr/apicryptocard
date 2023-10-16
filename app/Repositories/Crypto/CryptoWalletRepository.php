<?php

namespace App\Repositories\Crypto;

use App\Dto\UserDto;
use App\Exceptions\CustomException;
use App\Http\Controllers\Helpers\HelperFunctions;
use Illuminate\Http\Request;
use Ixudra\Curl\Facades\Curl;

class CryptoWalletRepository
{

    public function cardReload(Request $request)
    {
        $host = env('API_VIXIPAY_URL');
        $queryUrl = "/api/v1/client/crypto_transactions";
        $url = $host . $queryUrl;
        $walletId = $request->wallet_id;
        $amount = HelperFunctions::currencyToCustomChangePrice(1, 2, $request->amount);
        $transaction_type = 2;
        $verificationCode = $request->verification_code;
        $accesToken = $request->header('Authorization');

        $headers = [
            "Authorization: $accesToken",
            "Content-Type: application/json"
        ];

        $data = [
            'wallet_id' => $walletId,
            'verification_code' => $verificationCode,
            'amount' => $amount,
            'transaction_type' => $transaction_type
        ];

        $response = Curl::to($url)
            ->withData($data)
            ->asJson()
            ->withHeaders($headers)
            ->returnResponseObject()
            ->post();

        if ($response->status == 200) {
            return $response->content->data;
        }

        if ($response->status != 200) {
            throw new CustomException("The Wallet Tx could not be processed in Vixi API");
        }

        return false;
    }

    public function users(Request $request)
    {
        $host = env('API_VIXIPAY_URL');
        $queryUrl = "/api/v1/users";
        $url = $host . $queryUrl;
        $accesToken = $request->header('Authorization');

        $headers = [
            "Authorization: $accesToken",
            "Content-Type: application/json"
        ];

        $response = Curl::to($url)
            ->withHeaders($headers)
            ->returnResponseObject()
            ->get();

        if ($response->status == 200) {
            $data = json_decode($response->content);

            return $this->processingUser($data->data);
        }

        if ($response->status != 200) {
            throw new CustomException("The Users could not be processed in Vixi API");
        }

        return false;
    }

    public function processingUser($arrayUser)
    {
        $result = [];

        foreach ($arrayUser as $key => $user) {
            if ($user->rol->name == "Client") {
                $result[] = new UserDto($user->id, $user);
            }
        }

        return $result;
    }

    public function check2FAAuthenticator($user_id, $one_time_password, $request)
    {
        $host = env('API_VIXIPAY_URL');
        $queryUrl = "/api/v1/users/check_one_time_password";
        $url = $host . $queryUrl;


        $accesToken = $request->header('Authorization');

        $headers = [
            "Authorization: $accesToken",
            "Content-Type: application/json"
        ];

        $data = [
            'user_id' => $user_id,
            'one_time_password' => $one_time_password,
        ];

        $response = Curl::to($url)
            ->withData($data)
            ->asJson()
            ->withHeaders($headers)
            ->returnResponseObject()
            ->post();

        if ($response->status == 200) {
            return true;
        }

        if ($response->status != 200) {
            throw new CustomException("Two Factor Authenticator is wrong. Please try again.");
        }

        return false;
    }
}
