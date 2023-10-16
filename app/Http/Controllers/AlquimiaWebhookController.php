<?php

namespace App\Http\Controllers;

use App\Models\AlquimiaWebhooksLog;
use App\Repositories\TransactionRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class AlquimiaWebhookController extends Controller
{
    /** @var  TransactionRepository */
    private $transactionRepository;

    public function __construct(TransactionRepository $transactionRepo)
    {
        $this->transactionRepository = $transactionRepo;
    }

    public function webhook($account_id, Request $request)
    {
        AlquimiaWebhooksLog::create(
            [
                'id_account' => $account_id,
                'alquimia_transaction_data' => json_encode($request->all())
            ]
        );

        Artisan::call("load_retroactive_alquimia_transactions all $account_id");
        return response()->json(['data' => 'OK'], 200);
    }
}
