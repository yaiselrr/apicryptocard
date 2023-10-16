<?php

namespace App\Console\Commands;

use App\Http\Controllers\Helpers\HelperFunctions;
use App\Models\Account;
use App\Models\MotherCard;
use App\Models\MotherCardTransaction;
use App\Models\Transaction;
use App\Repositories\AlquimiapayTokenRepository;
use App\Repositories\GoogleSheetRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class LoadRetroactiveAlquimiaTransactionsCommand extends Command
{
    protected $signature = 'load_retroactive_alquimia_transactions {type=all} {account_id=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Load Retroactive Alquimia Transactions';


    private $alquimiaRepository;

    public function __construct(AlquimiapayTokenRepository $alquimiapayTokenRepository)
    {
        $this->alquimiaRepository = $alquimiapayTokenRepository;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $type = $this->argument('type');
        $accountId = $this->argument('account_id');


        try {
            if ($type == 'mother' || $type == 'all') {
                $query = MotherCard::query();
                if ($accountId != 0) {
                    $query->where('id_account', $accountId);
                }

                $mAccounts = $query->get();
                if (!$mAccounts->isEmpty()) {
                    $this->loadRetroactiveAlquimiaTxMotherCards($mAccounts);
                }
            }

            if ($type == 'accounts' || $type == 'all') {
                $query = Account::query();
                if ($accountId != 0) {
                    $query->where('id_account', $accountId);
                }

                $accounts = $query->get();
                if (!$accounts->isEmpty()) {
                    $this->loadRetroactiveAlquimiaTxMotherAccounts($accounts);
                }
            }
        } catch (\Exception $e) {
            //            print_r($e->getMessage() . "\n");
            //            print_r($e->getFile() . "\n");
            //            print_r($e->getLine() . "\n");

            Log::channel('daily')->info("------------------ *** LOAD RETROACTIVE ALQUIMIA TRANSACCTIONS ERROR *** ---------------------" . "\n");
            Log::channel('daily')->info($e->getMessage() . "\n");
            Log::channel('daily')->info($e->getFile() . "\n");
            Log::channel('daily')->info($e->getLine() . "\n");
        }

        //        print_r("------------------ LOAD RETROACTIVE ALQUIMIA TRANSACCTIONS ---------------------");
        Log::channel('daily')->info("------------------ LOAD RETROACTIVE ALQUIMIA TRANSACCTIONS ---------------------");
    }

    public function loadRetroactiveAlquimiaTxMotherCards($accounts)
    {
        foreach ($accounts as $account) {
            $alquimiaTxs = $this->alquimiaRepository->obtenerTransacciones($account->id_account);

            foreach ($alquimiaTxs as $alTx) {
                $idTransaction = $alTx->id_transaccion;

                $tx = MotherCardTransaction::where('id_transaccion', $idTransaction)->first();

                dump("entrando mother");

                if (!$tx) {
                    $data = [
                        'id_transaccion' => $idTransaction,
                        'concepto' => $alTx->concepto,
                        'clave_rastreo' => $alTx->clave_rastreo,
                        'fecha_alta' => $alTx->fecha_alta,
                        'monto' => $alTx->monto,
                        'valor_real' => $alTx->valor_real,
                        'id_medio_pago' => $alTx->id_medio_pago,
                        'alquimia_transaction_data' => "",
                        'transaction_type_id' => $alTx->valor_real < 0 ? 5 : 4,
                        'mother_card_id' => $account->id,
                        'currency_id' => $account->currency_id,
                        'beneficiary' => $account->card_number
                    ];

                    // print_r($data);die;
                    MotherCardTransaction::create($data);
                }
            }
        }
    }

    public function loadRetroactiveAlquimiaTxMotherAccounts($accounts)
    {
        foreach ($accounts as $account) {
            if ($account->id_account) {
                $alquimiaTxs = $this->alquimiaRepository->obtenerTransacciones($account->id_account);
                foreach ($alquimiaTxs as $alTx) {
                    $idTransaction = $alTx->id_transaccion;
                    dump("entrando accounts");
                    $tx = Transaction::where('id_tx_alquimia', $idTransaction)->first();

                    if (!$tx) {
                        Transaction::create([
                            'send_amount' => null,
                            'tx_blockchain_ref' => null,
                            'date' => $alTx->fecha_alta,
                            'amount' => $alTx->monto,
                            'id_tx_alquimia' => $idTransaction,
                            'id_tx_vixipay' => null,
                            'no_referencia_alquimia' => $alTx->clave_rastreo,
                            'folio_orden_alquimia' => $alTx->clave_rastreo,
                            'concepto' => $alTx->concepto,
                            'type' => $alTx->valor_real < 0 ? 'DECREMENT' : 'INCREMENT',
                            'state' => 'PROCESED',
                            'account_id' => $account->id,
                            'client_id' => $account->client_id,
                            'currency_id' => $account->currency_id,
                            'fee_id' => null,
                            'transaction_type_id' => $alTx->valor_real < 0 ? 5 : 4,
                            'user_id' => null,
                            'send_amount_currency_id' => null,
                            'fee_amount' => null,
                            'alquimia_data' => json_encode(get_object_vars($alTx)),
                            'card_provider_id' => $account->card_provider_id,
                        ]);
                    }

                    // $transAux = Transaction::find($tx->id);

                    // if (str_contains($transAux->concepto, "CardLoad")) {

                    //     $transAux->transaction_type_id = 1;
                    //     $transAux->save();
                    // }
                }
            }
        }
    }
}
