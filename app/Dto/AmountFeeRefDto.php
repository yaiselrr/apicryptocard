<?php

namespace App\Dto;

use App\Models\Fee;

class AmountFeeRefDto
{
    use JsonSerializeTrait;

    public $amount;
    public $fee;
    public $total_transfer;
    public $aproxUsd;
    public $aproxMxn;

    public function __construct($amountDto, $feeDto, $totalTransferDto, $aproxUsdDto, $aproxMxnDto)
    {
        $this->amount = $amountDto;
        $this->fee = $feeDto;
        $this->total_transfer = $totalTransferDto;
        $this->aproxUsd = $aproxUsdDto;
        $this->aproxMxn = $aproxMxnDto;
    }

}