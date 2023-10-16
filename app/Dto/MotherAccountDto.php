<?php

namespace App\Dto;

use App\Models\Fee;

class CollectionAccountDto
{
    use JsonSerializeTrait;

    public $alias;
    public $balance;

    public function __construct($motherAccountDto, $aliasDto)
    {
        
        $this->alias = "Ending ".substr($aliasDto, -4);
        $this->balance = $motherAccountDto->saldo;
    }

}