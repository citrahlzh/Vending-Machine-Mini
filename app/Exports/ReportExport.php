<?php

namespace App\Exports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromArray;

class ReportExport implements FromArray
{
    public function __construct(private readonly array $report)
    {
    }

    public function array(): array
    {
        $rows = [];

        $rows[] = ['Laporan Vending Machine'];
        $rows[] = ['Periode', $this->report['period_label']];
        $rows[] = ['Dibuat pada', Carbon::parse($this->report['generated_at'])->format('d/m/Y H:i')];
        $rows[] = [];

        $rows[] = ['Ringkasan Utama'];
        $rows[] = ['Total transaksi', $this->report['total_transactions']];
        $rows[] = ['Total omzet', (int) $this->report['total_omzet']];
        $rows[] = ['Produk terjual', $this->report['total_products_sold']];
        $rows[] = ['Rata-rata nilai transaksi', (int) $this->report['average_transaction']];
        $rows[] = [];

        $rows[] = ['Statistik Transaksi'];
        $rows[] = ['Sukses', $this->report['paid_transactions'], $this->report['success_rate'] . '%'];
        $rows[] = ['Pending', $this->report['pending_transactions'], $this->report['pending_rate'] . '%'];
        $rows[] = ['Gagal/Expired', $this->report['failed_transactions'], $this->report['failed_rate'] . '%'];
        $rows[] = [];

        $rows[] = ['Produk Terlaris'];
        $rows[] = ['Produk', 'Terjual', 'Omzet'];

        if ($this->report['top_products']->isEmpty()) {
            $rows[] = ['Belum ada data penjualan sukses.'];
        } else {
            foreach ($this->report['top_products'] as $product) {
                $rows[] = [
                    $product->product_name,
                    (int) $product->sold_qty,
                    (int) $product->omzet,
                ];
            }
        }

        $rows[] = [];
        $rows[] = ['Statistik Penjualan Harian'];
        $rows[] = ['Tanggal', 'Total Transaksi', 'Transaksi Sukses', 'Omzet'];

        foreach ($this->report['sales_by_day'] as $day) {
            $rows[] = [
                $day['date'],
                $day['total_transactions'],
                $day['paid_transactions'],
                (int) $day['omzet'],
            ];
        }

        return $rows;
    }
}
