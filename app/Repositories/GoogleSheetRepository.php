<?php

namespace App\Repositories;

use Ixudra\Curl\Facades\Curl;

class GoogleSheetRepository
{
    public function updateAccountsBalance($accountBalances)
    {
        $host = env('GOOGLE_SHEET_APP');
        $queryUrl = "general/update_spreadsheet";
        $url = $host . $queryUrl;

        $response = Curl::to($url)
            ->withData( $accountBalances )
            ->asJson( true )
            ->post();

        return $response;
    }

    public function updateAccountsMotherBalance($newBalance)
    {
        $host = env('GOOGLE_SHEET_APP');
        $queryUrl = "general/update_spreadsheet_mother";
        $url = $host . $queryUrl;

        $response = Curl::to($url)
            ->withData( $newBalance )
            ->asJson( true )
            ->post();

        return $response;
    }

}
