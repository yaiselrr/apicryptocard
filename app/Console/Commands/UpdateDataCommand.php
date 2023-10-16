<?php

namespace App\Console\Commands;

use App\Dto\AccountDto;
use App\Http\Controllers\Helpers\HelperFunctions;
use App\Models\Client;
use App\Repositories\AlquimiapayTokenRepository;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use App\Exports\ClientCardrsExport;
use Maatwebsite\Excel\Facades\Excel;

class UpdateDataCommand extends Command
{
    private $alquimiaRepository;

    public function __construct(AlquimiapayTokenRepository $alquimiapayTokenRepository)
    {
        $this->alquimiaRepository = $alquimiapayTokenRepository;
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_data_command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Data Command';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $total = 0;
            $this->alquimiaRepository->obtenerCuentasHijas(28095, 1, 10, '-fecha_alta', $total);
            print_r($total);
            die;
            $this->alquimiaRepository->asignacionTarjetaCliente('2222222222222222', '222222');
        } catch (\Exception $e) {
            print_r($e->getMessage());
            die;
        }
        $requestTime = time();
        $apiKey = 'asdlkjpoiqwemnbvzxcvlkjhgfasdfgh';
        $apiSecret = 'pjoeiksjiekrijdikwpeirkmncjkskdjxmciuznansurikf019d2fkdf83pfrwke';
        $tokenHash = hash_hmac('sha256', "$apiKey:$requestTime", $apiSecret);
        print_r('API KEY length:' . "\n");
        print_r(strlen($apiKey) . "\n");
        print_r('API SECRET length:' . "\n");
        print_r(strlen($apiSecret) . "\n");
        print_r('Request Time:' . "\n");
        print_r($requestTime . "\n");
        print_r('Token Hash:' . "\n");
        print_r($tokenHash . "\n");
        die;

        //EXPORT USER PASSWORDS
        $this->exportExcelPasswords(204);
        print_r("fin");
        die;

        $dataBalance = $this->alquimiaRepository->obtenerBalance('4116080158691597');
        $dataMovimientos = $this->alquimiaRepository->obtenerTransacciones(20100);
        $data = new AccountDto($dataBalance, $dataMovimientos);
        print_r($data);
        die;


        print_r($this->alquimiaRepository->obtenerBalance('4116080158691597'));
        print_r($this->alquimiaRepository->obtenerTransacciones(20100));
        die;


        print_r(HelperFunctions::getPasswFromCardNumber('78969734'));
        die;

        print_r("Update Users Username. Start *** \n");

        $usersToChange = [
            ['username' => '36944753', 'new_email' => 'david.v.consulting@gmail.com'],
            ['username' => '78657537', 'new_email' => 'inf502@protonmail.com'],
            ['username' => '84293327', 'new_email' => 'itzhak.s148@gmail.com'],
            ['username' => '00565196', 'new_email' => 'office129@mail.com'],
            ['username' => '13403393', 'new_email' => 'Josh@vixigroup.com'],
            ['username' => '50627136', 'new_email' => 'artempetrov77@icloud.com'],
            ['username' => '00776603', 'new_email' => 'irlopo77@gmail.com'],
        ];

        $affectedRows = 0;
        foreach ($usersToChange as $user) {
            $affectedRows += User::where('username', $user['username'])->update([
                'email' => $user['new_email']
            ]);
        }

        print_r("Updated Users $affectedRows \n");
        print_r("Update Users Username. End *** \n");

        die;

        Artisan::call('update_alquimia_tokens_command');

        print_r($this->alquimiaRepository->obtenerBalance('4116080142117501'));
        print_r($this->alquimiaRepository->obtenerTransacciones(16206));
        die;

        print_r(HelperFunctions::getPasswFromCardNumber('42117501'));
        die;


        $users = User::where('id', '>', 3)->get();
        foreach ($users as $user) {
            $user->password = HelperFunctions::getPasswFromCardNumber($user->username);
            $user->save();
        }

        print_r("cambiadas las contraseñas");


        die;


        Schema::table('clients', function (Blueprint $table) {
            $table->string('id_account')->nullable();
        });
        print_r('Añadido el campo id_account a la tabla clientes');
        die;

        print_r('ssssss');
        $balance = $this->alquimiaRepository->obtenerBalance('4116080104310631');
        print_r($balance);
        die;
        $cuentas = $this->alquimiaRepository->obtenerCuentasAhorroCliente();
        print_r($cuentas);
        die;
        print_r(PHP_EOL);
        print_r(PHP_EOL);
        print_r(PHP_EOL);
        print_r($this->alquimiaRepository->obtenerTransacciones($cuenta->id_cuenta_ahorro));
        print_r(PHP_EOL);
        print_r("Data Updated");

        $change1USD = HelperFunctions::currencyToMxnChangePrice(2, 1);
        $change5USD = HelperFunctions::currencyToMxnChangePrice(2, 5);
        $change80USD = HelperFunctions::currencyToMxnChangePrice(2, 80);
        $change24_50USD = HelperFunctions::currencyToMxnChangePrice(2, 24.50);

        $change1EUR = HelperFunctions::currencyToMxnChangePrice(3, 1);
        $change5EUR = HelperFunctions::currencyToMxnChangePrice(3, 5);
        $change80EUR = HelperFunctions::currencyToMxnChangePrice(3, 80);
        $change24_50EUR = HelperFunctions::currencyToMxnChangePrice(3, 24.50);


        print_r("cambios a USD: \n");
        print_r("1 USD: $change1USD \n");
        print_r("5 USD: $change5USD \n");
        print_r("80 USD: $change80USD \n");
        print_r("24.50 USD: $change24_50USD \n");

        print_r("\n\n\n\n cambios a EUR: \n");
        print_r("1 EUR: $change1EUR \n");
        print_r("5 EUR: $change5EUR \n");
        print_r("80 EUR: $change80EUR \n");
        print_r("24.50 EUR: $change24_50EUR \n");
        die;

    }

    public function exportExcelPasswords($start = 3)
    {
        $usersDb = User::with('client')->where('id', '>', $start)->get();

        foreach ($usersDb as $user) {
            $clients[] = [
                'card_number' => $user->client->card_number,
                'username' => $user->username,
                'password' => HelperFunctions::getPasswFromCardNumber($user->username)
            ];
        }

        Excel::store(new ClientCardrsExport($clients), 'cards1.xlsx');
    }
}
