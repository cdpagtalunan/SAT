<?php

namespace App\Exports\Sheets;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class LineBalanceSheet implements FromView, ShouldAutoSize, WithEvents, WithTitle, WithColumnWidths
{
    use Exportable;

    protected $sat;

    public function __construct($sat)
    {
        $this->sat= $sat;
    }

    public function view(): View
    {
	    // This will be find in views
        return view('exports.sat');
    }

    public function title(): string
    {
        return 'Line Balance';
    }
    public function columnWidths(): array
    {
        return [
            'B' => 20,  // Column B width
        ];
    }

    public function registerEvents(): array
    {
        // $masterlist = $this->masterlist;
        $sat= $this->sat;
        // Styles
        $border = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
        $border_buttom = [
            'borders' => [
                'bottom' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
        $border_outer = [
            'borders' => [
                'outline' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
        $center_align = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
        $right_align = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];
        $header = [
            'font' => [
                'bold' => true,
                'size' => 11,
            ],
        ];
        return [
            AfterSheet::class => function (AfterSheet $event) use (
                $sat,
                $border,
                $border_buttom,
                $center_align,
                $header,
                $right_align,
                $border_outer
            ) {
                $sheet = $event->sheet->getDelegate();
                $sheet->setShowGridlines(false);
                
                $sheet->setCellValue('B5', 'Station');
                $sheet->mergeCells('B5:B6');
                $sheet->setCellValue('B7', 'Station SAT');
                $sheet->setCellValue('B8', 'No. of Operators');
                $sheet->setCellValue('B9', 'TACT');
                $sheet->setCellValue('B10', 'UPH');

                $counter = 1;
                $column = $sheet->getHighestColumn();
                $column++;
                $initial_column = $column;

                foreach ($sat->satProcessDetails as $key => $value) {
                    $sheet->setCellValue("{$column}5", $counter);
                    $sheet->setCellValue("{$column}6", $value['process_name']);

                    $obsValues = [
                        $value['obs_1'],
                        $value['obs_2'],
                        $value['obs_3'],
                        $value['obs_4'],
                        $value['obs_5']
                    ];

                    $filtered = array_filter($obsValues, function ($value) {
                        return $value !== null;
                    });
                    $average = array_sum($filtered) / count($filtered);
                    $round_up = round($average, 2);

                    // Example: write the average to the next row (e.g., row 7)
                    $sheet->setCellValue("{$column}7", $round_up);

                    $sheet->setCellValue("{$column}8", $value['lb_no_operator']);
                    $tact =  $average / $value['lb_no_operator'];
                    
                    // $sheet->setCellValue("{$column}9", '=IF('.$column.'7="","",'.$column.'7/'.$column.'8)');
                    $sheet->setCellValue("{$column}9", round($tact, 2));
                    // $sheet->getStyle("{$column}9")->getNumberFormat()->setFormatCode('0.00');
                    $uph = 3600 / $tact;

                    
                    // $sheet->setCellValue("{$column}10", '=IF('.$column.'7="","",3600/'.$column.'9)');
                    $sheet->setCellValue("{$column}10", round($uph, 2));
                    $sheet->getStyle("{$column}10")->getNumberFormat()->setFormatCode('0.00');
                    $counter++;
                    $sheet->getColumnDimension($column)->setWidth(12);
                    $column++;
                    
                
                }
                $sheet->getStyle("B5:{$column}{$sheet->getHighestRow()}")->applyFromArray($border);

                $last_column = chr(ord($column) - 1);
                $sheet->setCellValue("{$last_column}11", "Assembly SAT");
                $sheet->setCellValue("{$last_column}12", "Line Balance");
                $sheet->setCellValue("{$last_column}13", "Output per Hour");


                $sheet->setCellValue("{$column}5", "Total");
                $sheet->mergeCells("{$column}5:{$column}6");

                $sheet->setCellValue("{$column}7", "=SUM({$initial_column}7:{$last_column}7)");
                $sheet->getStyle("{$column}7")->getNumberFormat()->setFormatCode('0.00');
                
                $sheet->setCellValue("{$column}8", "=SUM({$initial_column}8:{$last_column}8)");
                
                $sheet->setCellValue("{$column}11", '=MAX('.$initial_column.'9:'.$last_column.'9)*'.$column.'8');
                $sheet->setCellValue("{$column}12", '='.$column.'7/'.$column.'11');
                $sheet->setCellValue("{$column}13", '=3600/(MAX('.$initial_column.'9:'.$last_column.'9))');
                $sheet->getStyle("{$column}7:{$column}13")->getNumberFormat()->setFormatCode('0.00');
                $sheet->getStyle("{$column}12")->getNumberFormat()->setFormatCode('0.00%');
                $sheet->getStyle("{$last_column}11:{$column}13")->applyFromArray($border);


                $sheet->getStyle("{$initial_column}5:{$column}{$sheet->getHighestRow()}")->getAlignment()->setWrapText(true);
                $sheet->getStyle("{$initial_column}5:{$column}{$sheet->getHighestRow()}")->applyFromArray($center_align);






            },
        ];
    }
}
