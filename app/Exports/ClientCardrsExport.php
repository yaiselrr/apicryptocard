<?php

namespace App\Exports;

use Illuminate\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class ClientCardrsExport implements FromView, WithColumnFormatting
{
    public $clients;

    public function __construct(Array $dbClients){
        $this->clients = $dbClients;
    }

    public function view(): View
    {
        return view('exports.client_cards', [
            'clients' => $this->clients
        ]);
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
        ];
    }
}
