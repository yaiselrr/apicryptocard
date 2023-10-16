<?php

namespace App\Repositories\Bitfinex;

use App\Exceptions\CustomException;
use App\Models\BitfinexLog;
use Ixudra\Curl\Facades\Curl;

class BitfinexRepository
{
    public function convertBetweenCrypto($ccy1, $ccy2)
    {
        $host = env('BITFINEX_API_URL', 'https://api-pub.bitfinex.com/v2');
        $queryUrl = "/calc/fx";
        $url = $host . $queryUrl;

        $headers = [
            "Accept: application/json",
            "Content-Type: application/json"
        ];

        $data = [
            'ccy1' => $ccy1,
            'ccy2' => $ccy2
        ];

        $newBitfinexLog = BitfinexLog::create([
            'endpoint' => $url,
            'params' => json_encode($data),
            'response' => ''
        ]);

        $response = Curl::to($url)
            ->withData($data)
            ->asJson()
            ->withHeaders($headers)
            ->returnResponseObject()
            ->post();

        if ($response) {
            $newBitfinexLog->response = substr(json_encode($response), 0, 10240);
            $newBitfinexLog->save();
        }

        if ($response->status == 200) {
            return $response->content[0];
        }

        if ($response->status != 200) {
            throw new CustomException("The convert failed");
        }
    }
}
