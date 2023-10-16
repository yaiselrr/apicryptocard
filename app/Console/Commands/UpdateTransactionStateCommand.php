<?php

namespace App\Console\Commands;

use App\Models\AlquimiapayToken;
use App\Models\Log;
use App\Models\Transaction;
use App\Repositories\AlquimiapayTokenRepository;
use App\Repositories\TransactionRepository;
use Illuminate\Console\Command;
use App\Models\Account;
// use Illuminate\Support\Facades\Log;

class UpdateTransactionStateCommand extends Command
{
    protected $signature = 'update_transaction_state';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Transaction State';


    private $alquimiaRepository;
    private $transactionRepository;

    public function __construct(
        AlquimiapayTokenRepository $alquimiapayToken,
        TransactionRepository $transaction
    ) {
        $this->alquimiaRepository = $alquimiapayToken;
        $this->transactionRepository = $transaction;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $pendingTransactions = $this->transactionRepository->listAllPendingTransactions();
        // $errorTransactions = $this->transactionRepository->listAllErrorTransactions();

        if (count($pendingTransactions) > 0) {
            foreach ($pendingTransactions as $key => $transaction) {
                $motherAccountBalanceResponse = $this->alquimiaRepository->obtenerBalanceCuentaMadre();
                $account = Account::select('card_number')->whereId($transaction->account_id)->first();

                if ($motherAccountBalanceResponse->status == 0) {
                    Log::create([
                        'description' => $motherAccountBalanceResponse->error,
                        'petition' => 'GET',
                        'error' => $motherAccountBalanceResponse->error,
                        'parameters' => '',
                        'user_id' => $transaction->user_id
                    ]);
                } elseif ($motherAccountBalanceResponse->status == 401) {
                    Log::create([
                        'description' => $motherAccountBalanceResponse->content,
                        'petition' => 'GET',
                        'error' => $motherAccountBalanceResponse->status,
                        'parameters' => '',
                        'user_id' => $transaction->user_id
                    ]);
                } elseif ($motherAccountBalanceResponse->saldo && $motherAccountBalanceResponse->saldo < $transaction->amount) {
                    Log::create([
                        'description' => 'La cuenta madre no tiene el saldo suficiente',
                        'petition' => 'GET',
                        'error' => 422,
                        'parameters' => '',
                        'user_id' => $transaction->user_id
                    ]);
                } elseif ($account == null) {
                    Log::create([
                        'description' => 'La cuenta no existe',
                        'petition' => 'GET',
                        'error' => 422,
                        'parameters' => '',
                        'user_id' => $transaction->user_id
                    ]);
                } else {
                    // $tranfer = $this->alquimiaRepository->transaccionCuentaMadreATarjeta($transaction->amount, $account->card_number);

                    // if ($tranfer != false ) {
                    //     $this->transactionRepository->notificationReload($account->card_number, $transaction->amount, $tranfer['transactionId'], 1, $transaction->client_id);
                    // }else{
                    //     $this->transactionRepository->notificationReload($account->card_number, $transaction->amount, $tranfer['transactionId'], 0, $transaction->client_id);                        
                    //     $transaction->state = "ERROR";
                    //     $transaction->save();
                    // }
                }
            }
        }

        print_r("------------------ UPDATE TRANSACTIONS STATES PENDING END ---------------------");
        // Log::channel('daily')->info("------------------ UPDATE TRANSACTIONS STATES PENDING END ---------------------");
    }
}
