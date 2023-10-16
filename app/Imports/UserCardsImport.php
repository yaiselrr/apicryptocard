<?php

namespace App\Imports;


use App\Http\Controllers\Helpers\HelperFunctions;
use App\Models\Client;
use App\Models\User;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Row;

class UserCardsImport implements OnEachRow
{
    /**
     * @param Collection $collection
     */
    public function onRow(Row $row)
    {
        $data = $row->toArray();
        $index = $row->getIndex();
        if ($index > 1) {
            $cardNumber = $data[0];
            $cardUser = substr($cardNumber,8);
            $newIndex = 300 + $index;
            $u = User::create([
                'name' => $cardUser,
                'username' => $cardUser,
                'email' => $newIndex . 'not_assigned@gmail.com',
                'password' => HelperFunctions::getPasswFromCardNumber($cardUser . ''),
                'last_name' => ''
            ]);

            $u->assignRole('Client');

            Client::create([
                'zip_code' => '53000',
                'card_number' => $cardNumber,
                'user_id' => $u->id
            ]);
        }
    }
}
