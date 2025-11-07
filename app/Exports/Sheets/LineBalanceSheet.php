<?php

namespace App\Exports\Sheets;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class LineBalanceSheet implements FromView, ShouldAutoSize, WithEvents, WithTitle, WithColumnWidths
{
    use Exportable;

    protected $sat;

    public function __construct($sat)
    {
        $this->sat = $sat;
    }

    public function view(): View
    {
        return view('exports.sat');
    }

    public function title(): string
    {
        return 'Line Balance';
    }

    public function columnWidths(): array
    {
        return [
            'B' => 20,
        ];
    }

    public function registerEvents(): array
    {
        $sat = $this->sat;

        $border = [
            'borders' => [
                'allBorders' => [
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
        $header = [
            'font' => [
                'bold' => true,
                'size' => 11,
            ],
        ];

        return [
            AfterSheet::class => function (AfterSheet $event) use ($sat, $border, $center_align, $header) {
                $sheet = $event->sheet->getDelegate();
                $sheet->setShowGridlines(false);

                // --- HEADER ---

                $sheet->setCellValue('B4', 'Line Balance');
                $sheet->setCellValue('B4', 'Line Balance');
                $sheet->setCellValue('B5', 'Station');
                $sheet->mergeCells('B5:B6');
                $sheet->setCellValue('B7', 'Station SAT');
                $sheet->setCellValue('B8', 'No. of Operators');
                $sheet->setCellValue('B9', 'TACT');
                $sheet->setCellValue('B10', 'UPH');
                $sheet->getStyle('B4:B10')->applyFromArray($header);

                $column = 'C';
                $initial_column = $column;
                $counter = 1;

                foreach ($sat->satProcessDetails as $process) {
                    // merge process name across operators if more than 1
                    $operatorCount = count($process->operators ?? []);

                    // header row (process index + process name)
                    $sheet->setCellValue("{$column}5", $counter);
                    $sheet->setCellValue("{$column}6", $process->process_name);

                    $rowStart = 7;
                    // foreach ($process->operators as $operator) {
                    //     $obs = array_filter([
                    //         $operator['obs_1'],
                    //         $operator['obs_2'],
                    //         $operator['obs_3'],
                    //         $operator['obs_4'],
                    //         $operator['obs_5']
                    //     ], function ($v) {
                    //         return $v !== null;
                    //     });

                        $average = $process->average_obs;
                        $tact = $process->tact_time;
                        $uph = $process->lb_uph_time;

                        $sheet->setCellValue("{$column}{$rowStart}", round($average, 2));
                        $sheet->setCellValue("{$column}" . ($rowStart + 1), $process->lb_no_operator);
                        $sheet->setCellValue("{$column}" . ($rowStart + 2), round($tact, 2));
                        $sheet->setCellValue("{$column}" . ($rowStart + 3), round($uph, 2));

                        // $rowStart += 4; // move to next operator section
                    // }
                    $sheet->getColumnDimension($column)->setWidth(12);
                    $counter++;
                    $column++;
                }
                $sheet->getStyle("B4:{$column}{$sheet->getHighestRow()}")->applyFromArray($border);

                $lastColumn = chr(ord($column) - 1);
                $sheet->setCellValue("{$lastColumn}11", "Assembly SAT");
                $sheet->setCellValue("{$lastColumn}12", "Line Balance");
                $sheet->setCellValue("{$lastColumn}13", "Output per Hour");
                $sheet->getStyle("{$lastColumn}11:{$lastColumn}13")->applyFromArray($header);


                $sheet->setCellValue("{$column}5", "Total");
                $sheet->mergeCells("{$column}5:{$column}6");
                $sheet->setCellValue("{$column}7", "=SUM({$initial_column}7:{$lastColumn}7)");
                $sheet->getStyle("{$column}7")->getNumberFormat()->setFormatCode('0.00');
                 $sheet->setCellValue("{$column}8", "=SUM({$initial_column}8:{$lastColumn}8)");
                
                $sheet->setCellValue("{$column}11", '=MAX('.$initial_column.'9:'.$lastColumn.'9)*'.$column.'8');
                $sheet->setCellValue("{$column}12", '='.$column.'7/'.$column.'11');
                $sheet->setCellValue("{$column}13", '=3600/(MAX('.$initial_column.'9:'.$lastColumn.'9))');
                $sheet->getStyle("{$column}7:{$column}13")->getNumberFormat()->setFormatCode('0.00');
                $sheet->getStyle("{$column}12")->getNumberFormat()->setFormatCode('0.00%');
                $sheet->getStyle("{$lastColumn}11:{$column}13")->applyFromArray($border);



                // // // Apply borders and alignments
                $sheet->getStyle("{$initial_column}4:{$column}{$sheet->getHighestRow()}")->getAlignment()->setWrapText(true);
                $sheet->mergeCells("B4:{$column}4");

                $sheet->getStyle("B4:{$column}{$sheet->getHighestRow()}")->applyFromArray($center_align);

                // $sheet->getStyle("B5:{$lastColumn}10")->applyFromArray($border);
                // $sheet->getStyle("B5:{$lastColumn}10")->applyFromArray($center_align);
            },
        ];
    }
}
