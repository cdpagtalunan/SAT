<?php

namespace App\Exports;


Use Maatwebsite\Excel\Sheet;
use App\Exports\Sheets\ObservationSheet;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;


class ExportSat implements  WithMultipleSheets
{
    use Exportable;

    protected $sat;

    function __construct(
        $sat
    ){
        $this->sat  = $sat;

    }

    public function sheets(): array {
        $sheets = [];
        $sheets[] = new ObservationSheet($this->sat);
        return $sheets;
    }
}
