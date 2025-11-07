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

class ObservationSheet implements FromView, ShouldAutoSize, WithEvents, WithTitle, WithDrawings, WithColumnWidths
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

    public function columnWidths(): array
    {
        return [
            'A' => 2,  // Column A width
            'B' => 5,  // Column B width
            'C' => 25,  // Column C width
            'D' => 15,  // Column D width
            'J' => 14,  // Column J width
            'K' => 14,  // Column K width
            'L' => 14,  // Column L width
            'M' => 14,  // Column M width
        ];
    }

    public function title(): string
    {
        return 'VSAT';
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('This is my logo');
        $drawing->setPath(public_path('pricon.png'));
        $drawing->setHeight(40);
        $drawing->setCoordinates('B2');

        return $drawing;
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

                $fields = [
                    ['B5', 'Device Name:', 'D5', $sat->device_name, 'D5:G5'],
                    ['B6', 'Operations Line:', 'D6', $sat->operation_line, 'D6:G6'],
                    ['B7', 'Assembly Line:', 'D7', $sat->assembly_line, 'D7:G7'],
                ];

                foreach ($fields as [$labelCell, $label, $valueCell, $value, $mergeRange]) {
                    $sheet->setCellValue($labelCell, $label);
                    $sheet->setCellValue($valueCell, $value);
                    $sheet->mergeCells($mergeRange);
                    $sheet->getStyle($mergeRange)->applyFromArray($border_buttom);
                    $sheet->getStyle($valueCell)->applyFromArray($center_align);
                }

                $rightFields = [
                    ['K4', 'QSAT:', 'L4', $sat->qsat, 'L4:M4'],
                    ['K5', 'Date Taken:', 'L5', Carbon::parse($sat->validated_at)->format('Y-m-d'), 'L5:M5'],
                    ['K6', 'Date Finished:', 'L6', Carbon::parse($sat->updated_at)->format('Y-m-d'), 'L6:M6'],
                    ['K7', 'No. of Pins:', 'L7', $sat->no_of_pins, 'L7:M7'],
                ];

                foreach ($rightFields as [$labelCell, $label, $valueCell, $value, $mergeRange]) {
                    $sheet->setCellValue($labelCell, $label);
                    $sheet->setCellValue($valueCell, $value);
                    $sheet->mergeCells($mergeRange);
                    $sheet->getStyle($mergeRange)->applyFromArray($border_buttom);
                    $sheet->getStyle($valueCell)->applyFromArray($center_align);
                }

                $sheet->setCellValue('B9', "Process");
                $sheet->mergeCells('B9:C10');

                $sheet->setCellValue('D9', "Operator");
                $sheet->mergeCells('D9:D10');

                $sheet->setCellValue('E9', "OBSERVATIONS (sec.per cycle unit)");
                $sheet->mergeCells('E9:I9');

                $sheet->setCellValue('E10', "1");
                $sheet->setCellValue('F10', "2");
                $sheet->setCellValue('G10', "3");
                $sheet->setCellValue('H10', "4");
                $sheet->setCellValue('I10', "5");

                $sheet->setCellValue('J9', "Obeserved Time (OT) (secs.)");
                $sheet->mergeCells('J9:J10');


                $sheet->setCellValue('K9', "Allowance Factor (AF) (%)");
                $sheet->mergeCells('K9:K10');

                $sheet->setCellValue('L9', "Normal Time (NT) (secs.)");
                $sheet->mergeCells('L9:L10');

                $sheet->setCellValue('M9', "Standard Time (ST) (secs.)");
                $sheet->mergeCells('M9:M10');

                $sheet->setCellValue('N9', "UPH");
                $sheet->mergeCells('N9:N10');

                $sheet->getStyle('B9:N10')->applyFromArray($center_align);
                $sheet->getStyle('J9:M9')->getAlignment()->setWrapText(true);
                $sheet->getStyle("B9:N10")->applyFromArray($header);
                $sheet->getRowDimension(9)->setRowHeight(33);

                $sheet->setCellValue('B8', "STANDARD ASSEMBLY TIME (SAT)");
                $sheet->mergeCells('B8:N8');
                $sheet->getStyle('B8')->applyFromArray($center_align);
                $sheet->getStyle('B8')->applyFromArray($header);

                $start_row = 11;
                $initial = 11;
                $ctr = 1;

                foreach ($sat->satProcessDetails as $process) {

                    $operatorCount = count($process->operators);
                    $firstRow = $start_row;
                    $lastRow = $start_row + $operatorCount - 1;

                    foreach ($process->operators as $op) {
                        $sheet->setCellValue('B' . $start_row, $ctr);
                        $sheet->setCellValue('C' . $start_row, $process->process_name);
                        $sheet->setCellValue('D' . $start_row, $op['operator']);
                        $sheet->setCellValue('E' . $start_row, $op['obs_1']);
                        $sheet->setCellValue('F' . $start_row, $op['obs_2']);
                        $sheet->setCellValue('G' . $start_row, $op['obs_3']);
                        $sheet->setCellValue('H' . $start_row, $op['obs_4']);
                        $sheet->setCellValue('I' . $start_row, $op['obs_5']);

                        $start_row++;
                    }

                    // merge cells for the computed columns
                    $sheet->mergeCells("B{$firstRow}:B{$lastRow}");
                    $sheet->mergeCells("C{$firstRow}:C{$lastRow}");
                    $sheet->mergeCells("J{$firstRow}:J{$lastRow}");
                    $sheet->mergeCells("K{$firstRow}:K{$lastRow}");
                    $sheet->mergeCells("L{$firstRow}:L{$lastRow}");
                    $sheet->mergeCells("M{$firstRow}:M{$lastRow}");
                    $sheet->mergeCells("N{$firstRow}:N{$lastRow}");

                    // set merged values once (top row)
                    $sheet->setCellValue("J{$firstRow}", round($process->average_obs, 2));
                    $sheet->getStyle("J{$firstRow}")->getNumberFormat()->setFormatCode('0.00');

                    $sheet->setCellValue("K{$firstRow}", $process->allowance / 100);
                    $sheet->getStyle("K{$firstRow}")->getNumberFormat()->setFormatCode('0.00%');

                    $sheet->setCellValue("L{$firstRow}", round($process->average_obs, 2));
                    $sheet->setCellValue("M{$firstRow}", round($process->standard_time, 2));
                    $sheet->setCellValue("N{$firstRow}", round($process->uph_time, 2));
                    $sheet->getStyle("L{$firstRow}:N{$firstRow}")->getNumberFormat()->setFormatCode('0.00');

                    $ctr++;
                }
                // $start_row = 11;
                // $initial = 11;
                // $ctr = 1;
                // foreach ($sat->satProcessDetails as $key => $value) {
                //     $sheet->setCellValue('B' . $start_row, $ctr);
                //     $sheet->setCellValue('C' . $start_row, $value->process_name);
                //     $sheet->setCellValue('D' . $start_row, $value->operator_name);
                //     $sheet->setCellValue('E' . $start_row, $value->obs_1);
                //     $sheet->setCellValue('F' . $start_row, $value->obs_2);
                //     $sheet->setCellValue('G' . $start_row, $value->obs_3);
                //     $sheet->setCellValue('H' . $start_row, $value->obs_4);
                //     $sheet->setCellValue('I' . $start_row, $value->obs_5);
                //     $sheet->setCellValue('J' .$start_row, '=IF(E' .$start_row.'="","",AVERAGE(E' .$start_row. ':I' .$start_row. '))');
                //     $sheet->getStyle('J' . $start_row)->getNumberFormat()->setFormatCode('0.00');
                //     $sheet->setCellValue('K' . $start_row, $value->allowance / 100);
                //     $sheet->getStyle('K' . $start_row)->getNumberFormat()->setFormatCode('0.00%');
                //     $sheet->setCellValue('L' . $start_row, '=J' .$start_row);
                //     $sheet->setCellValue('M' . $start_row, '=IF(J' .$start_row.'="","",L' .$start_row.'*(1+K' .$start_row. '))');
                //     $sheet->setCellValue('N' . $start_row, '=IF(M' .$start_row.'="","",3600/M' .$start_row. ')');

                //     $sheet->getStyle('L' . $start_row.':N' . $start_row)->getNumberFormat()->setFormatCode('0.00');
                //     $ctr++;
                //     $start_row++;
                // }
                // $sheet->getStyle("C{$initial}:C{$sheet->getHighestRow()}")->getAlignment()->setWrapText(true);
                // $sheet->getStyle("B{$initial}:N{$sheet->getHighestRow()}")->applyFromArray($center_align);
                // $sheet->setCellValue('B' . $start_row, "Total");
                // $sheet->mergeCells("B{$start_row}:K" . ($start_row));
                // $sheet->getStyle("B{$start_row}:K{$start_row}")->applyFromArray($right_align);
                // $sheet->setCellValue('L' . $start_row, "=SUM(L{$initial}:L" . ($start_row - 1) . ")");
                // $sheet->setCellValue('M' . $start_row, "=SUM(M{$initial}:M" . ($start_row - 1) . ")");
                $sheet->getStyle("L{$start_row}:M{$start_row}")->applyFromArray($center_align);
                $sheet->getStyle("B{$start_row}:M{$start_row}")->applyFromArray($header);
                $sheet->getStyle("B9:N{$sheet->getHighestRow()}")->applyFromArray($border);
                $sheet->getStyle("B8:N{$sheet->getHighestRow()}")->applyFromArray($center_align);
                $sheet->getStyle("B8:N{$sheet->getHighestRow()}")->getAlignment()->setWrapText(true);

                $signatories_initial = $sheet->getHighestRow();

                $signatories_start = $sheet->getHighestRow()+2;

                $sheet->setCellValue('B' . $signatories_start, "Prepared by:");
                $sheet->setCellValue('D' . $signatories_start, "Checked by:");
                $sheet->setCellValue('H' . $signatories_start, "Approved by:");
                $signatories_start = $signatories_start + 2;

                $sheet->setCellValue('B' . $signatories_start, $sat->validatedByDetails->name);
                $sheet->setCellValue('D' . $signatories_start, $sat->lineBalByDetails->name);
                $sheet->setCellValue('H' . $signatories_start, $sat->approverDetails->approver1Details->FirstName . ' ' . $sat->approverDetails->approver1Details->LastName);
                $sheet->setCellValue('L' . $signatories_start, $sat->approverDetails->approver2Details->FirstName . ' ' . $sat->approverDetails->approver2Details->LastName);
                $sheet->getStyle("B{$signatories_start}:M{$signatories_start}")->applyFromArray($header);

                $signatories_start++;
                $sheet->setCellValue('B' . $signatories_start, "SAT Validator");
                $sheet->setCellValue('D' . $signatories_start, "Jr. Engineer");
                $sheet->setCellValue('H' . $signatories_start, "Engineering Section Head");
                $sheet->setCellValue('L' . $signatories_start, "Production Section Head");

                $sheet->getStyle("B8:N{$sheet->getHighestRow()}")->applyFromArray($border_outer);

                

            },
        ];
    }
}
