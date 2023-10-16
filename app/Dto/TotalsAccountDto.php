<?php

namespace App\Dto;


use App\Http\Controllers\Helpers\HelperFunctions;

class TotalsAccountDto
{
    use JsonSerializeTrait;

    public $id;
    public $total;
    public $totalUsd;

    public function __construct($totalDto)
    {
        $this->total = $totalDto;
        $this->totalUsd = HelperFunctions::currencyToMxnChangePrice(2, $totalDto);
        $this->eur = HelperFunctions::currencyToMxnChangePrice(3, $totalDto);

    }
}
