<?php

namespace App\Dto;

use App\Models\Fee;

class FeeDto
{
    use JsonSerializeTrait;

    public $feeTxCardToCard;
    public $feeTxMotherAccountToCard;
    public $feeTXFirst;

    public function __construct(Fee $feeDto)
    {
        $this->feeTxCardToCard = $feeDto->fee_card_tx;
        $this->feeTxMotherAccountToCard = $feeDto->fee_card_reload;
        $this->feeTXFirst = $feeDto->fee_first_tx;
    }

}