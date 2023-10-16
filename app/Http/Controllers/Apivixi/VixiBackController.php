<?php

namespace App\Http\Controllers\Apivixi;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class VixiBackController extends Controller
{
    public function __construct()
    {

    }

    public function client_register(Request $request)
    {

        try {
//            $userId = $request->user_id;
//            $userJson = $request->user_json;
//
//            $client = Client::where('user_id', $userId);
//            if(!$client) {
//                Client::create([
//                    'zip_code' => 53000,
//                    'user_id' => $userId,
//                    'user_json' => $userJson
//                ]);
//            }

            return response()->json([
                'message' => trans('msgs.type_success'),
                'type' => trans('msgs.type_success')
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => trans('msgs.msg_error_contact_the_adminitrator'),
                'type' => trans('msgs.type_error'),
                'object' => $e
            ], 500);
        }
    }
}
