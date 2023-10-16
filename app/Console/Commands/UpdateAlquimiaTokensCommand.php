<?php

namespace App\Console\Commands;

use App\Models\AlquimiaLog;
use App\Models\AlquimiapayToken;
use App\Repositories\AlquimiapayTokenRepository;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateAlquimiaTokensCommand extends Command
{
    protected $signature = 'update_alquimia_tokens_command {force=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Alquimia Tokens';


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
        $force = $this->argument('force');

        $wso2LastToken = $this->alquimiaRepository->getLastToken('wso2', true);
        $alquimiaLastToken = $this->alquimiaRepository->getLastToken('alquimia', true);

        if ($force == 1) {
            $wso2Token = $this->updateWso2Token();
        } else {
            if ($wso2LastToken && $wso2LastToken->token != '0') {
                $lastTokenUnixDate = Carbon::createFromFormat('Y-m-d H:i:s', $wso2LastToken->created_at)->format('U');
                $actualUnixDate = Carbon::now()->format('U');
                if ($actualUnixDate - $lastTokenUnixDate > 420) {
                    $wso2Token = $this->updateWso2Token();
                } else {
                    $wso2Token = $wso2LastToken->token;
                }
            } else {
                $wso2Token = $this->updateWso2Token();
            }
        }

        if ($force == 1) {
            $alquimiaToken = $this->updateAlquimiaToken();
        } else {
            if ($alquimiaLastToken && $alquimiaLastToken->token != '0' && strlen($alquimiaLastToken->token) == 40) {
                $lastTokenUnixDate = Carbon::createFromFormat('Y-m-d H:i:s', $alquimiaLastToken->created_at)->format('U');
                $actualUnixDate = Carbon::now()->format('U');
                if ($actualUnixDate - $lastTokenUnixDate > 72000) {
                    $alquimiaToken = $this->updateAlquimiaToken();
                } else {
                    $alquimiaToken = $alquimiaLastToken->token;
                }
            } else {
                $alquimiaToken = $this->updateAlquimiaToken();
            }
        }

        //Delete TOkens with more than 3 days.
        $this->deleteOldTokens();

        //Delete Logs with more than 15 days.
        $this->deleteOldAlquimiaLogs();

        print_r('WSO2 Token:' . PHP_EOL);
        print_r($wso2Token . PHP_EOL);

        print_r('Alquimia Token:' . PHP_EOL);
        print_r($alquimiaToken . PHP_EOL);


        print_r("------------------ UPDATE TOKENS END ---------------------");
        Log::channel('daily')->info("------------------ UPDATE TOKENS END ---------------------");
    }

    public function updateWso2Token()
    {
        $wso2Token = $this->alquimiaRepository->getWso2Token();

        AlquimiapayToken::create([
            'type' => 'WSO2',
            'token' => $wso2Token
        ]);

        return $wso2Token;
    }

    public function updateAlquimiaToken()
    {
        $alquimiaToken = $this->alquimiaRepository->getAlquimiaToken();

        AlquimiapayToken::create([
            'type' => 'ALQUIMIA',
            'token' => $alquimiaToken
        ]);

        return $alquimiaToken;
    }

    public function deleteOldTokens()
    {
        $actualUnixDate = Carbon::now()->format('U');
        $thriDaysAgo = $actualUnixDate - 259200;
        $thriDaysAgoFormatDate = Carbon::createFromFormat('U', $thriDaysAgo)->format('Y-m-d H:i:s');
        AlquimiapayToken::where('created_at', '<', $thriDaysAgoFormatDate)->delete();
    }

    public function deleteOldAlquimiaLogs()
    {
        $actualUnixDate = Carbon::now()->format('U');
        //1,296,000 son 15 días.
        $daysAgo = $actualUnixDate - 1296000;
        $daysAgoFormatDate = Carbon::createFromFormat('U', $daysAgo)->format('Y-m-d H:i:s');
        AlquimiaLog::where('created_at', '<', $daysAgoFormatDate)->delete();
    }
}
