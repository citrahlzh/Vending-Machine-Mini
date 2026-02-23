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
            ['', '', '', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', '', ''],
            ['', 'Laporan Transaksi Vending Machine', '', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', '', ''],
            ['', 'No', 'Waktu Transaksi', 'ID Transaksi', 'Produk', 'Nominal Jumlah', 'Status', 'Sel', ''],
        ];

        $transactions = $this->report['export_transactions'] ?? collect();
        $rowNumber = 1;

        foreach ($transactions as $transaction) {
            $transactionDate = $transaction['transaction_date'] ?? null;
            $excelDate = null;

            if ($transactionDate !== null) {
                $excelDate = ExcelDate::dateTimeToExcel($transactionDate);
            }

            $rows[] = [
                '',
                $rowNumber,
                $excelDate,
                $transaction['idempotency_key'] ?? '-',
                $transaction['products'] ?? '-',
                (int) ($transaction['total_amount'] ?? 0),
                $transaction['status_label'] ?? '-',
                $transaction['cells'] ?? '-',
                '',
            ];

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
            'F' => 18.89,
            'G' => 15,
            'H' => 14.44,
            'I' => 8.89,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = max(7, 6 + count($this->report['export_transactions'] ?? []));

                $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial')->setSize(11);
                $sheet->getStyle("A1:I{$lastRow}")->getFont()->setName('Arial')->setSize(11);

                $sheet->mergeCells('B3:H3');

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

                $sheet->getStyle('B6:H6')->applyFromArray([
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
                    $sheet->getStyle("B7:H{$lastRow}")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => 'FF000000'],
                            ],
                        ],
                    ]);

                    $sheet->getStyle("B7:D{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("F7:H{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("E7:E{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle("B7:H{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("B7:H{$lastRow}")->getAlignment()->setWrapText(true);
                    $sheet->getStyle("E7:E{$lastRow}")->getAlignment()->setWrapText(true);

                    $sheet->getStyle("C7:C{$lastRow}")->getNumberFormat()->setFormatCode('dd/mm/yyyy hh:mm:ss');
                    $sheet->getStyle("F7:F{$lastRow}")->getNumberFormat()
                        ->setFormatCode('"Rp"#,##0.00;[Red]\\-"Rp"#,##0.00');
                }
            },
        ];
    }
}
