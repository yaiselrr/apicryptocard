<?php

namespace App\Console\Commands;

use App\Models\TemporalFile;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CleanTempsFilesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean_temps_files';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command clean the directory of temps files';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $tempsFiles = TemporalFile::where('created_at', '<', Carbon::yesterday())->get();
        foreach ($tempsFiles as $file) {
            \Storage::disk('public')->delete($file->originalUrl);
            $file->delete();
        }
    }
}
