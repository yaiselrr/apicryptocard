<?php

namespace App\Dto;


use App\Http\Controllers\Helpers\HelperFunctions;

class TotalsMotherAccountDto
{
    use JsonSerializeTrait;

    public $totalIncome;
    public $totalOutgoing;
    public $totalAvailable;
    public $ref_usd_income;
    public $ref_usd_outgoing;
    public $ref_usd_available;


    public function __construct($totalIncomeDto, $totalOutgoingDto, $totalAvailableDto)
    {
        $this->totalIncome = $totalIncomeDto;
        $this->totalOutgoing = $totalOutgoingDto * -1;
        $this->totalAvailable = $totalAvailableDto;
        $this->ref_usd_income = HelperFunctions::currencyToMxnChangePrice(2, $totalIncomeDto);
        $this->ref_usd_outgoing = HelperFunctions::currencyToMxnChangePrice(2, $totalOutgoingDto) * -1;
        $this->ref_usd_available = HelperFunctions::currencyToMxnChangePrice(2, $totalAvailableDto);
    }

}