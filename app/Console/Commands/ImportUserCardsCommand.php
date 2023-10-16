<?php

namespace App\Console\Commands;

use App\Imports\UserCardsImport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;


class ImportUserCardsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import_user_cards_command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import User Cards Command';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //$spareTestUsers = User::whereIn('name', ['Mary', 'Account1', 'Account2'])->delete();

        Excel::import(new UserCardsImport(), 'cards2000.xlsx', 'public');
        die;

    }


}
