<?php

namespace App\Dto;


use App\Models\TransferLimit;

class LimitDto
{
    use JsonSerializeTrait;

    public $limitTxCardToCard;
    public $limitTxMotherAccountToCard;
    public $limitTXFirst;

    public function __construct(TransferLimit $limitDto)
    {
        $this->limitTxCardToCard = $limitDto->limit_card_tx;
        $this->limitTxMotherAccountToCard = $limitDto->limit_card_reload;
        $this->limitTXFirst = $limitDto->limit_first_tx;
    }

}