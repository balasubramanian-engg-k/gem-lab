<?php

namespace App\Exports;

use App\Models\Gem;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GemsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        // Fetch the data you want
        return Gem::select('summary_no', 'description', 'clarity', 'gross_weight', 'diamond_weight', 'comment')->get();
    }

    public function headings(): array
    {
        return [
            'SUMMARY NO',
            'DESCRIPTION',
            'COLOR / CLARITY',
            'GROSS WEIGHT',
            'STONE WEIGHT',
            'CONCLUSION',
        ];
    }
}
