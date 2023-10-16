<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\App;
use App\Models\Card;
use App\Models\Client;
use App\Models\Currency;
use App\Models\ExchangerProvider;
use App\Models\CardsProvider;
use App\Models\Fee;
use App\Models\FeeConcept;
use App\Models\MotherCard;
use App\Models\PriceExchangerCurrency;
use App\Models\SumSubReviewRejectType;
use App\Models\TransactionClass;
use App\Models\TransactionRequest;
use App\Models\TransactionType;
use App\Models\TransferLimit;
use App\Models\User;
use App\Models\UserApp;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        if ($this->command->confirm('Do you wish to fresh migration before seeding, it will clear all old data ?')) {
            // Call the php artisan migrate:refresh
            $this->command->call('migrate:fresh');
            $this->command->warn("Data cleared, starting from blank database.");
        }

        $clients = [
            [
                'name' => 'Jon',
                'email' => 'jon@777crew.com',
                'last_name' => 'Grimberg',
                'username' => 'jon',
                'phone' => '5322222222',
            ], [
                'name' => 'd002',
                'email' => 'd002bet0@gmail.com',
                'last_name' => 'bet0',
                'username' => 'd002bet0',
                'phone' => '5322222222',
            ], [
                'name' => 'd001',
                'email' => 'd001bet0@gmail.com',
                'last_name' => 'bet0',
                'username' => 'd001bet0',
                'phone' => '5322222222',
            ], [
                'name' => 'mary',
                'email' => 'mary@777crew.com',
                'last_name' => 'bet0',
                'username' => 'mary',
                'phone' => '5322222222',
            ], [
                'name' => 'laraveldev3',
                'email' => 'laraveldev3@gmail.com',
                'last_name' => '777crew',
                'phone' => '+52-998-1844329'
            ], [
                'name' => 'angulard4',
                'email' => 'angulard4@gmail.com',
                'last_name' => '777crew',
                'phone' => '+52-998-1844329'
            ], [
                'name' => 'charly',
                'email' => 'charly@gmail.com',
                'last_name' => '777crew',
                'phone' => '+52-998-1844329'
            ], [
                'name' => 'Analista',
                'email' => 'analistaqa777@gmail.com',
                'last_name' => '777crew',
                'phone' => '+52-998-1844329'
            ], [
                'name' => 'Analista',
                'email' => 'analistaqa777@gmail.com',
                'last_name' => 'Code',
                'phone' => '+52-998-1844329'
            ]
        ];

        foreach ($clients as $key => $user) {
            Client::create([
                'zip_code' => 53000,
                'user_id' => $key + 8,
                'user_json' => json_encode($user)
            ]);
        }

        $currencies = [
            ['name' => 'Mexican Peso', 'abbreviation' => 'MXN', 'crypto' => false],
            ['name' => 'United States Dollar', 'abbreviation' => 'USD', 'crypto' => false],
            ['name' => 'Euro', 'abbreviation' => 'EUR', 'crypto' => false],
            ['name' => 'Tether', 'abbreviation' => 'USDT', 'crypto' => true],
            ['name' => 'USD Coin', 'abbreviation' => 'USDC', 'crypto' => true],
            ['name' => 'Ethereum', 'abbreviation' => 'ETH', 'crypto' => true],
            ['name' => 'Bitcoin', 'abbreviation' => 'BTC', 'crypto' => true]
        ];

        foreach ($currencies as $currency) {
            Currency::create([
                'name' => $currency['name'],
                'abbreviation' => $currency['abbreviation'],
                'crypto' => $currency['crypto'],
            ]);
        }

        $exchangerProviders = [
            ['name' => 'Ex', 'description' => 'Currency Rates Provider'],
            ['name' => 'Bitfinex', 'description' => 'Currency Rates Provider']
        ];

        foreach ($exchangerProviders as $ep) {
            ExchangerProvider::create([
                'name' => $ep['name'],
                'description' => $ep['description']
            ]);
        }

        $cardProviders = [
            ['name' => 'Alquimia', 'mother_account' => [
                'id_account' => 28095,
                'api_key' => '28b13c49202dd553238dd50e2581b009',
                'card_number' => '1000000130200675',
                'balance' => 0.00,
                'currency_id' => 1,
            ]]
        ];

        foreach ($cardProviders as $cp) {
            $cardP = CardsProvider::create([
                'name' => $cp['name']
            ]);

            $motherCardData = $cp['mother_account'];

            MotherCard::create([
                'id_account' => $motherCardData['id_account'],
                'api_key' => $motherCardData['api_key'],
                'card_number' => $motherCardData['card_number'],
                'balance' => $motherCardData['balance'],
                'currency_id' => $motherCardData['currency_id'],
                'card_provider_id' => $cardP->id,
            ]);
        }

        $accounts = [
            ['card_number' => '4116080106965887', 'last8_digits' => '06965887', 'balance' => 0.00, 'client_id' => null, 'mother_card_id' => 1, 'card_provider_id' => 1, 'currency_id' => 1],
            ['card_number' => '4116080150476492', 'last8_digits' => '50476492', 'balance' => 0.00, 'client_id' => 7, 'mother_card_id' => 1, 'card_provider_id' => 1, 'currency_id' => 1],
            ['card_number' => '4116080138120014', 'last8_digits' => '38120014', 'balance' => 0.00, 'client_id' => 7, 'mother_card_id' => 1, 'card_provider_id' => 1, 'currency_id' => 1],
            ['card_number' => '4116080136958035', 'last8_digits' => '36958035', 'balance' => 0.00, 'client_id' => 5, 'mother_card_id' => 1, 'card_provider_id' => 1, 'currency_id' => 1],
            ['card_number' => '4116080172616265', 'last8_digits' => '72616265', 'balance' => 0.00, 'client_id' => 5, 'mother_card_id' => 1, 'card_provider_id' => 1, 'currency_id' => 1],
            ['card_number' => '4116080126055446', 'last8_digits' => '26055446', 'balance' => 0.00, 'client_id' => 1, 'mother_card_id' => 1, 'card_provider_id' => 1, 'currency_id' => 1],
            ['card_number' => '4116080104924068', 'last8_digits' => '04924068', 'balance' => 0.00, 'client_id' => 1, 'mother_card_id' => 1, 'card_provider_id' => 1, 'currency_id' => 1],
            ['card_number' => '4116080180070513', 'last8_digits' => '80070513', 'balance' => 0.00, 'client_id' => 4, 'mother_card_id' => 1, 'card_provider_id' => 1, 'currency_id' => 1],
            ['card_number' => '4116080150832827', 'last8_digits' => '50832827', 'balance' => 0.00, 'client_id' => 4, 'mother_card_id' => 1, 'card_provider_id' => 1, 'currency_id' => 1],
            ['card_number' => '4116080132455366', 'last8_digits' => '32455366', 'balance' => 0.00, 'client_id' => 8, 'mother_card_id' => 1, 'card_provider_id' => 1, 'currency_id' => 1],
            ['card_number' => '4116080117599295', 'last8_digits' => '17599295', 'balance' => 0.00, 'client_id' => 8, 'mother_card_id' => 1, 'card_provider_id' => 1, 'currency_id' => 1],
            ['card_number' => '4116080115092798', 'last8_digits' => '15092798', 'balance' => 0.00, 'client_id' => 2, 'mother_card_id' => 1, 'card_provider_id' => 1, 'currency_id' => 1],
            ['card_number' => '4116080162467521', 'last8_digits' => '62467521', 'balance' => 0.00, 'client_id' => 2, 'mother_card_id' => 1, 'card_provider_id' => 1, 'currency_id' => 1],
            ['card_number' => '4116080143652159', 'last8_digits' => '43652159', 'balance' => 0.00, 'client_id' => 3, 'mother_card_id' => 1, 'card_provider_id' => 1, 'currency_id' => 1],
            ['card_number' => '4116080187255729', 'last8_digits' => '87255729', 'balance' => 0.00, 'client_id' => 3, 'mother_card_id' => 1, 'card_provider_id' => 1, 'currency_id' => 1],
            ['card_number' => '4116080106965857', 'last8_digits' => '06965857', 'balance' => 0.00, 'client_id' => 6, 'mother_card_id' => 1, 'card_provider_id' => 1, 'currency_id' => 1],
            ['card_number' => '4116080125899083', 'last8_digits' => '25899083', 'balance' => 0.00, 'client_id' => 6, 'mother_card_id' => 1, 'card_provider_id' => 1, 'currency_id' => 1]
        ];

        foreach ($accounts as $tc) {
            if ($tc['card_number'] == '4116080106965887') {
                Account::create([
                    'card_number' => $tc['card_number'],
                    'last8_digits' => $tc['last8_digits'],
                    'balance' => $tc['balance'],
                    'active' => true,
                    'collection_account' => true,
                    'client_id' => $tc['client_id'],
                    'mother_card_id' => $tc['mother_card_id'],
                    'card_provider_id' => $tc['card_provider_id'],
                    'currency_id' => $tc['currency_id']
                ]);
            } else {
                Account::create([
                    'card_number' => $tc['card_number'],
                    'last8_digits' => $tc['last8_digits'],
                    'balance' => $tc['balance'],
                    'client_id' => $tc['client_id'],
                    'mother_card_id' => $tc['mother_card_id'],
                    'card_provider_id' => $tc['card_provider_id'],
                    'currency_id' => $tc['currency_id']
                ]);
            }
        }

        $transaction_types = [
            ['name' => 'Depósito', 'type' => 'INCREMENT'],
            ['name' => 'Transferencia Origen', 'type' => 'DECREMENT'],
            ['name' => 'Transferencia Destino', 'type' => 'INCREMENT'],
            ['name' => 'Alquimia Increment Tx', 'type' => 'INCREMENT'],
            ['name' => 'Alquimia Decrement Tx', 'type' => 'DECREMENT'],
            ['name' => 'Ajuste', 'type' => 'DECREMENT'],
            ['name' => 'Depósito desde Cuenta', 'type' => 'INCREMENT'],
        ];

        foreach ($transaction_types as $tt) {
            TransactionType::create([
                'name' => $tt['name'],
                'type' => $tt['type']
            ]);
        }

        Fee::create([
            'fee_card_reload' => 5.00,
            'fee_card_tx' => 3.00,
            'fee_first_tx' =>0.01,
        ]);

        TransferLimit::create([
            'limit_card_reload' => 1.00,
            'limit_card_tx' => 0.00,
            'limit_first_tx' => 0.01,
        ]);

        $fee_concepts = [
            ['name' => 'Refund', 'fee' => 0.00],
            ['name' => 'Order New Card at pickup location', 'fee' => 14.90],
            ['name' => 'Order New Card fast shipping', 'fee' => 49.90],
            ['name' => 'Reorder Fee', 'fee' => 9.90],
            ['name' => 'Chargeback Fee/Investigation', 'fee' => 40.00],
        ];

        foreach ($fee_concepts as $fc) {
            FeeConcept::create([
                'name' => $fc['name'],
                'fee' => $fc['fee']
            ]);
        }

        Artisan::call('update_prices_from_provider');
        Artisan::call('update_alquimia_tokens_command 1');
        Artisan::call('update_alquimia_accounts_id no');
        Artisan::call('load_retroactive_alquimia_transactions');
    }
}
