<?php

namespace App\Console\Commands;

use App\Models\AlquimiapayToken;
use App\Repositories\AlquimiapayTokenRepository;
use App\Repositories\GoogleSheetRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateAlquimiaAccountsIdCommand extends Command
{
    protected $signature = 'update_alquimia_accounts_id {stop_at_firts_associated_account=yes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Alquimia Accounts Id';


    private $alquimiaRepository;
    private $googleSheetRepository;

    public function __construct(AlquimiapayTokenRepository $alquimiapayTokenRepository,
                                GoogleSheetRepository $sheetRepository)
    {
        $this->alquimiaRepository = $alquimiapayTokenRepository;
        $this->googleSheetRepository = $sheetRepository;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $stopAtFirtsAssociatedAccount = $this->argument('stop_at_firts_associated_account');
        $alquimiaAccounts = $this->alquimiaRepository->updateIdCuentas(28095, $stopAtFirtsAssociatedAccount == 'yes' ? true : false);

        $accountBalances = [];

        foreach ($alquimiaAccounts as $alquimiaAccount) {
            $balance = new \stdClass();
            $balance->number = substr($alquimiaAccount->no_cuenta, -8);
            $balance->balance = $alquimiaAccount->saldo_ahorro;
            $accountBalances[] = $balance;
        }

        if ($accountBalances) {
            $this->googleSheetRepository->updateAccountsBalance($accountBalances);
        }

        $motherAccountBalanceResponse = $this->alquimiaRepository->obtenerBalanceCuentaMadre();
        if ($motherAccountBalanceResponse) {
            $motherBalance = new \stdClass();
            $motherBalance->balance = $motherAccountBalanceResponse->saldo;
            $this->googleSheetRepository->updateAccountsMotherBalance($motherBalance);
        }

        print_r("------------------ UPDATE IDS DE CUENTAS END ---------------------");
        Log::channel('daily')->info("------------------ UPDATE IDS DE CUENTAS END ---------------------");
    }

}
