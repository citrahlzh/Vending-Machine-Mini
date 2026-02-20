<?php

namespace App\Exports\Vending;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SummarySheet implements FromArray, WithEvents, WithColumnWidths, WithTitle
{
    public function __construct(private readonly array $report)
    {
    }

    public function title(): string
    {
        return 'Ringkasan';
    }

    public function array(): array
    {
        $startDate = Carbon::parse($this->report['start_date'])->locale('id')->translatedFormat('d F Y');
        $endDate = Carbon::parse($this->report['end_date'])->locale('id')->translatedFormat('d F Y');
        $exportDate = Carbon::parse($this->report['generated_at'])->locale('id')->translatedFormat('d F Y');

        return [
            ['', '', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', ''],
            ['', 'Laporan Vending Machine PT Manusia Solusi Terbaik', '', '', '', '', '', ''],
            ['', 'Lokasi Stasiun Lenteng Agung', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', ''],
            ['', 'Periode', $startDate . ' - ' . $endDate, '', '', '', '', ''],
            ['', 'Tanggal Ekspor', $exportDate, '', '', '', '', ''],
            ['', '', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', ''],
            ['', 'Total Transaksi Gagal', (int) $this->report['failed_transactions'], '', '', '', '', ''],
            ['', 'Total Transaksi Berhasil', (int) $this->report['paid_transactions'], '', '', '', '', ''],
            ['', '', '', '', '', '', '', ''],
            ['', '', '', '', '', '', '', ''],
            ['', 'Total Pendapatan', (int) $this->report['total_omzet'], '', '', '', '', ''],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 4.44,
            'B' => 24.11,
            'C' => 37.66,
            'D' => 8.89,
            'E' => 8.89,
            'F' => 8.89,
            'G' => 4.44,
            'H' => 8.89,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = 16;

                $sheet->getParent()->getDefaultStyle()->getFont()->setName('Arial')->setSize(11);
                $sheet->getStyle("A1:H{$lastRow}")->getFont()->setName('Arial')->setSize(11);
                $sheet->getStyle("B3:H{$lastRow}")->getAlignment()->setWrapText(true);

                $sheet->mergeCells('B3:F3');
                $sheet->mergeCells('B4:F4');

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

                $sheet->getStyle('B4')->applyFromArray([
                    'font' => [
                        'name' => 'Arial',
                        'size' => 11,
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                $labelRows = [8, 9, 12, 13, 16];
                foreach ($labelRows as $row) {
                    $sheet->getStyle("B{$row}")->applyFromArray([
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
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);

                    $sheet->getStyle("C{$row}")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['argb' => 'FF000000'],
                            ],
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_LEFT,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                }

                $sheet->getStyle('C16')->getNumberFormat()
                    ->setFormatCode('"Rp"#,##0.00;[Red]\\-"Rp"#,##0.00');
            },
        ];
    }
}
