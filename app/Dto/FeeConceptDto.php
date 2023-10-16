<?php

namespace App\Dto;

use App\Http\Controllers\Helpers\HelperFunctions;
use App\Models\Fee;
use App\Models\FeeConcept;

class FeeConceptDto
{
    use JsonSerializeTrait;

    public $id;
    public $concept;
    public $feeUsd;
    public $feeMxn;
    public $totalTransfer;

    public function __construct(FeeConcept $feeConceptDto, $amount)
    {
        $this->id = $feeConceptDto->id;
        $this->concept = $feeConceptDto->name;
        $this->feeUsd = $feeConceptDto->fee;
        $this->feeMxn = HelperFunctions::currencyToCustomChangePrice(2, 1, $feeConceptDto->fee);
        $this->totalTransfer = abs($feeConceptDto->fee - HelperFunctions::currencyToCustomChangePrice(1, 2, $amount));
    }

}