<?php

namespace App\Repositories\Xe;

use App\Http\Controllers\Helpers\HelperFunctions;
use App\Models\XeLog;
use Ixudra\Curl\Facades\Curl;

class XeRepository
{
    public $apiId;
    public $apiKey;
    public $xeUrl;

    public function __construct()
    {
        $this->apiId = env('XE_API_ID', 'qroo255719450');
        $this->apiKey = env('XE_API_KEY', '7vid7ik7mc1tqucfiqj5srqvfu');
        $this->apiUrl = env('XE_API_URL', 'https://xecdapi.xe.com/v1');
    }

    public function accountInfo()
    {
        $queryUrl = "/account_info";
        $url = $this->apiUrl . $queryUrl;

        $response = Curl::to($url)
            ->withOption('USERPWD', "$this->apiId:$this->apiKey")
            ->asJson()
            ->withResponseHeaders()
            ->returnResponseObject()
            ->get();

        $responseHeaders = $response->headers;
        if ($response->status == 200) {
            XeLog::create([
                'success' => true,
                'content' => json_encode($response->content),
                'error_message' => '',
                'status' => 200,
                'error_code' => -1,
                'x-ratelimit-limit' => $responseHeaders['x-ratelimit-limit'],
                'x-ratelimit-remaining' => $responseHeaders['x-ratelimit-remaining'],
                'x-ratelimit-reset' => $responseHeaders['x-ratelimit-reset']
            ]);
        } else {
            XeLog::create([
                'success' => false,
                'content' => json_encode($response->content),
                'error_message' => $response->content->message,
                'status' => $response->status,
                'error_code' => $response->content->code,
                'x-ratelimit-limit' => 0,
                'x-ratelimit-remaining' => 0,
                'x-ratelimit-reset' => 0
            ]);
        }

        return $response->content;
    }

    public function currencies($language = 'en', $iso = null, $obsolete = false)
    {
        $queryUrl = "/currencies";
        $url = $this->apiUrl . $queryUrl;
        $params = ['language' => $language];
        if ($iso) {
            $params['iso'] = $iso;
        }

        if ($obsolete) {
            $params['obsolete'] = $obsolete;
        }

        $response = Curl::to($url)
            ->withOption('USERPWD', "$this->apiId:$this->apiKey")
            ->withData($params)
            ->asJson()
            ->withResponseHeaders()
            ->returnResponseObject()
            ->get();

        $responseHeaders = $response->headers;
        if ($response->status == 200) {
            XeLog::create([
                'success' => true,
                'content' => json_encode($response->content),
                'error_message' => '',
                'status' => 200,
                'error_code' => -1,
                'x-ratelimit-limit' => $responseHeaders['x-ratelimit-limit'],
                'x-ratelimit-remaining' => $responseHeaders['x-ratelimit-remaining'],
                'x-ratelimit-reset' => $responseHeaders['x-ratelimit-reset']
            ]);
        } else {
            XeLog::create([
                'success' => false,
                'content' => json_encode($response->content),
                'error_message' => $response->content->message,
                'status' => $response->status,
                'error_code' => $response->content->code,
                'x-ratelimit-limit' => 0,
                'x-ratelimit-remaining' => 0,
                'x-ratelimit-reset' => 0
            ]);
        }

        return $response->content;
    }

    public function convertFrom($from, $to, $amount = 1, $obsolete = false, $inverse = false, $decimal_places = 6)
    {
        $queryUrl = "/convert_from";
        $url = $this->apiUrl . $queryUrl;
        $params = ['from' => $from];
        $params['decimal_places'] = $decimal_places;

        if (is_array($to)) {
            $toStr = '';
            foreach ($to as $index => $currencyToItem) {
                if ($index == 0) {
                    $toStr .= $currencyToItem;
                } else {
                    $toStr .= ",$currencyToItem";
                }
            }
            $params['to'] = $toStr;
        } elseif (is_string($to)) {
            $params['to'] = $to;
        }

        if ($obsolete) {
            $params['obsolete'] = $obsolete;
        }

        if ($inverse) {
            $params['inverse'] = $inverse;
        }

        $response = Curl::to($url)
            ->withOption('USERPWD', "$this->apiId:$this->apiKey")
            ->withData($params)
            ->asJson()
            ->withResponseHeaders()
            ->returnResponseObject()
            ->get();

        $responseHeaders = $response->headers;
        if ($response->status == 200) {
            $xRatelimitLimit = array_key_exists('X-RateLimit-Limit', $responseHeaders) ? $responseHeaders['X-RateLimit-Limit'] : $responseHeaders['x-ratelimit-limit'];
            $xRatelimitRemaining = array_key_exists('X-RateLimit-Remaining', $responseHeaders) ? $responseHeaders['X-RateLimit-Remaining'] : $responseHeaders['x-ratelimit-remaining'];
            $xRatelimitReset = array_key_exists('X-RateLimit-Reset', $responseHeaders) ? $responseHeaders['X-RateLimit-Reset'] : $responseHeaders['x-ratelimit-reset'];

            XeLog::create([
                'success' => true,
                'content' => json_encode($response->content),
                'error_message' => '',
                'status' => 200,
                'error_code' => -1,
                'x-ratelimit-limit' => $xRatelimitLimit,
                'x-ratelimit-remaining' => $xRatelimitRemaining,
                'x-ratelimit-reset' => $xRatelimitReset
            ]);
        } else {
            XeLog::create([
                'success' => false,
                'content' => json_encode($response->content),
                'error_message' => isset($response->content->message) ? $response->content->message : '',
                'status' => $response->status,
                'error_code' => isset($response->content->code) ? $response->content->code : '',
                'x-ratelimit-limit' => 0,
                'x-ratelimit-remaining' => 0,
                'x-ratelimit-reset' => 0
            ]);
        }

        return $response->content;
    }

    public function convertTo($to = null, $from = null, $amount = 1, $obsolete = false, $inverse = false){
        $queryUrl = "/convert_to";

        return '';
    }

    public function historicRate($dateTime, $from = null, $to = null, $amount = 1, $obsolete = false, $inverse = false){
        $queryUrl = "/historic_rate";

        return '';
    }

    public function historicRatePeriod($startDateTime = null, $endDateTime = null, $from = null, $to = null, $amount = 1, $interval = 'DAILY', $obsolete = false, $inverse = false, $page = 1, $perPage = 30){
        $queryUrl = "/historic_rate/period";

        return '';
    }

    public function monthlyAverage($year = null, $month = null, $from = null, $to = null, $amount = 1, $obsolete = false, $inverse = false, array $options = []){
        $queryUrl = "/historic_rate/period";

        return '';
    }
}
