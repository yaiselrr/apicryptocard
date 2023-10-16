<?php

namespace App\Dto;

use App\Models\Fee;

class UserDto
{
    use JsonSerializeTrait;

    public $id;
    public $email;

    public function __construct($id, $userDto)
    {
        $this->id = $id;
        $this->name = json_decode($userDto)->name;
        $this->email = json_decode($userDto)->email;
    }

}