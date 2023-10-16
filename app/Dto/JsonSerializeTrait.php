<?php

namespace App\Dto;


trait JsonSerializeTrait
{
    public function jsonSerialize()
    {
        $result = [];
        foreach ($this as $propiedad => $valor) {
            $result[$propiedad] = $valor;
        }

        return $result;
    }
}