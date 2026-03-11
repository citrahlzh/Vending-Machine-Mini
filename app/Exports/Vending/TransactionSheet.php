<?php

namespace App\Exports\Vending;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TransactionSheet implements FromArray, WithEvents, WithColumnWidths, WithTitle
{
    private array $mergeRows = [];

    public function __construct(private readonly array $report)
    {
    }

    public function title(): string
    {
        return 'Transaksi';
    }

    public function array(): array
    {
        $rows = [
            ['', '', '', '', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', '', '', ''],
            ['', 'Laporan Transaksi Vending Machine', '', '', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', '', '', ''],
            ['', 'No', 'Waktu Transaksi', 'ID Transaksi', 'Produk', 'Qty', 'Harga', 'Nominal Jumlah', 'Status', 'Sel'],
        ];

        $transactions = $this->report['export_transactions'] ?? collect();

        $rowNumber = 1;
        $currentRow = 7;

        foreach ($transactions as $transaction) {

            $excelDate = null;

            if (!empty($transaction['transaction_date'])) {
                $excelDate = ExcelDate::dateTimeToExcel($transaction['transaction_date']);
            }

            $products = $transaction['products_detail'] ?? [];

            $startRow = $currentRow;

            foreach ($products as $index => $product) {

                $rows[] = [
                    '',
                    $index === 0 ? $rowNumber : '',
                    $index === 0 ? $excelDate : '',
                    $index === 0 ? $transaction['idempotency_key'] : '',
                    $product['name'],
                    $product['qty'],
                    $product['price'],
                    $index === 0 ? (int) $transaction['total_amount'] : '',
                    $index === 0 ? $transaction['status_label'] : '',
                    $product['cell']
                ];

                $currentRow++;
            }

            $endRow = $currentRow - 1;

            $this->mergeRows[] = [$startRow, $endRow];

            $rowNumber++;
        }

        return $rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5.55,
            'B' => 5,
            'C' => 20.78,
            'D' => 24.44,
            'E' => 52.89,
            'F' => 11,
            'G' => 15,
            'H' => 18.89,
            'I' => 11,
            'J' => 11
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial')->setSize(11);
                $sheet->getStyle("A1:J{$lastRow}")->getFont()->setName('Arial')->setSize(11);

                $sheet->mergeCells('B3:H3');

                foreach ($this->mergeRows as [$start, $end]) {

                    if ($start === $end)
                        continue;

                    $sheet->mergeCells("B{$start}:B{$end}");
                    $sheet->mergeCells("C{$start}:C{$end}");
                    $sheet->mergeCells("D{$start}:D{$end}");
                    $sheet->mergeCells("H{$start}:H{$end}");
                    $sheet->mergeCells("I{$start}:I{$end}");
                }

                $sheet->getStyle('B3')->applyFromArray([
                    'font' => [
                        'name' => 'Arial',
                        'bold' => true,
                        'size' => 12,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $sheet->getStyle('B6:J6')->applyFromArray([
                    'font' => [
                        'name' => 'Arial',
                        'bold' => true,
                        'size' => 11,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFEDEDED'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FF000000'],
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                if ($lastRow >= 7) {
                    $sheet->getStyle("B7:J{$lastRow}")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => 'FF000000'],
                            ],
                        ],
                    ]);

                    $sheet->getStyle("B7:D{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("F7:J{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("E7:E{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle("B7:H{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("B7:I{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("B7:J{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("B7:H{$lastRow}")->getAlignment()->setWrapText(true);
                    $sheet->getStyle("E7:E{$lastRow}")->getAlignment()->setWrapText(true);

                    $sheet->getStyle("C7:C{$lastRow}")->getNumberFormat()->setFormatCode('dd/mm/yyyy hh:mm:ss');
                    $sheet->getStyle("G7:G{$lastRow}")->getNumberFormat()
                        ->setFormatCode('"Rp"#,##0.00;[Red]\\-"Rp"#,##0.00');
                    $sheet->getStyle("H7:H{$lastRow}")->getNumberFormat()
                        ->setFormatCode('"Rp"#,##0.00;[Red]\\-"Rp"#,##0.00');
                }
            },
        ];
    }
}
