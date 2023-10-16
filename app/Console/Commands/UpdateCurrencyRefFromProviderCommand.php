<?php

namespace App\Console\Commands;

use App\Models\Currency;
use App\Models\PriceExchangerCrypto;
use App\Models\PriceExchangerCurrency;
use App\Repositories\Xe\XeRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Repositories\Bitfinex\BitfinexRepository;

class UpdateCurrencyRefFromProviderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update_prices_from_provider';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the currencies prices respect to de referencial currency';


    private $xeRepository;
    private $bitfinexRepository;

    public function __construct(XeRepository $xeRepositoryObj, BitfinexRepository $bitfinexRepo)
    {
        $this->xeRepository = $xeRepositoryObj;
        $this->bitfinexRepository = $bitfinexRepo;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //        $referencialCurrency = Currency::where('abbreviation', 'MXN')->first();
        //        $otherCurrencies = Currency::where('abbreviation', '!=', 'MXN')->get()->pluck('abbreviation')->toArray();
        //
        //        $response = $this->xeRepository->convertFrom($referencialCurrency->abbreviation, $otherCurrencies);
        //
        //        if ($response && isset($response->to)) {
        //            foreach ($response->to as $currenciesRate) {
        //                $currency = Currency::where('abbreviation', $currenciesRate->quotecurrency)->first();
        //                PriceExchangerCurrency::create([
        //                    'price' => $currenciesRate->mid,
        //                    'exchanger_provider_id' => 1,
        //                    'currency_id' => $currency->id
        //                ]);
        //            }
        //        }
        $cryptos = Currency::where('crypto', 1)->get();
        $crypto2s = Currency::where('crypto', 1)->get();
        $ccy1 = null;
        $ccy2 = null;

        $referencialCurrency = Currency::where('abbreviation', 'MXN')->first();
        $otherCurrencies = Currency::where('abbreviation', '!=', 'MXN')->get()->pluck('abbreviation')->toArray();

        $response = $this->xeRepository->convertFrom($referencialCurrency->abbreviation, $otherCurrencies);

        if ($response && isset($response->to)) {
            foreach ($response->to as $currenciesRate) {
                $currency = Currency::where('abbreviation', $currenciesRate->quotecurrency)->first();
                PriceExchangerCurrency::create([
                    'price' => $currenciesRate->mid,
                    'exchanger_provider_id' => 1,
                    'currency_id' => $currency->id
                ]);
            }
        }

        foreach ($cryptos as $key => $crypto) {
            ($crypto->abbreviation == "USDT" || $crypto->abbreviation == "USDC") ? $ccy1 = "USD" : $ccy1 = $crypto->abbreviation;
            foreach ($crypto2s as $key => $value) {
                ($value->abbreviation == "USDT" || $value->abbreviation == "USDC") ? $ccy2 = "USD" : $ccy2 = $value->abbreviation;
                if ($value->abbreviation != $crypto->abbreviation) {
                    $response = $this->bitfinexRepository->convertBetweenCrypto($ccy1, $ccy2);

                    if ($response != null) {
                        PriceExchangerCrypto::create([
                            'price' => $response,
                            'exchanger_provider_id' => 2,
                            'ccy1' => $crypto->id,
                            'ccy2' => $value->id
                        ]);
                    }
                }
            }
        }

        Log::channel('daily')->info("------------------ UPDATE CURRENCIES RATES CRYPTOS AND NO CYPTOS END ---------------------");
    }
}
