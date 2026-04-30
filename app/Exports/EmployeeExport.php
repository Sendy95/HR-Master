<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeeExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $employees;

    public function __construct($employees)
    {
        $this->employees = $employees;
    }

    public function view(): View
    {
        // Arahkan ke file baru khusus Excel, bukan file report yang ada Modal/JS
        return view('exports.employee_excel', [
            'data' => $this->employees
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Membuat header (baris 1) menjadi bold
            1 => ['font' => ['bold' => true]],
        ];
    }
}