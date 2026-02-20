<?php

namespace App\Exports\Vending;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ProductSheet implements FromArray, WithEvents, WithColumnWidths, WithTitle
{
    public function __construct(private readonly array $report)
    {
    }

    public function title(): string
    {
        return 'Produk';
    }

    public function array(): array
    {
        $rows = [
            ['', '', '', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', '', ''],
            ['', 'Laporan Produk Terjual Vending Machine', '', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', '', ''],
            ['', 'No', 'Nama', 'Harga', 'Terjual', 'Omzet', 'Sisa Stok', 'Sel', ''],
        ];

        $products = $this->report['export_products'] ?? collect();
        $rowNumber = 1;

        foreach ($products as $product) {
            $unitPrice = (int) ($product['unit_price'] ?? 0);
            $soldQty = (int) ($product['sold_qty'] ?? 0);
            $omzet = $unitPrice * $soldQty;

            $rows[] = [
                '',
                $rowNumber,
                $product['product_name'] ?? '-',
                $unitPrice,
                $soldQty,
                $omzet,
                (int) ($product['stock_remaining'] ?? 0),
                $product['cell_code'] ?? '-',
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
            'B' => 6,
            'C' => 56.78,
            'D' => 22.22,
            'E' => 14.11,
            'F' => 22.22,
            'G' => 14,
            'H' => 14,
            'I' => 8.89,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = max(7, 6 + count($this->report['export_products'] ?? []));

                $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial')->setSize(11);
                $sheet->getStyle("A1:I{$lastRow}")->getFont()->setName('Arial')->setSize(11);
                $sheet->getStyle("B6:H{$lastRow}")->getAlignment()->setWrapText(true);

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

                    $sheet->getStyle("B7:B{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("D7:H{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("C7:C{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle("B7:H{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

                    $sheet->getStyle("D7:D{$lastRow}")->getNumberFormat()
                        ->setFormatCode('"Rp"#,##0;[Red]\\-"Rp"#,##0');
                    $sheet->getStyle("F7:F{$lastRow}")->getNumberFormat()
                        ->setFormatCode('"Rp"#,##0;[Red]\\-"Rp"#,##0');
                }
            },
        ];
    }
}
