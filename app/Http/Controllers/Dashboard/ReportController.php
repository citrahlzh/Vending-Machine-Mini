<?php

namespace App\Http\Controllers\Dashboard;

use App\Exports\Vending\VendingReportExport;
use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleLine;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        [$startDate, $endDate] = $this->resolveDateRange($request);
        $report = $this->buildReportData($startDate, $endDate);

        return view('dashboard.reports.index', compact('report'));
    }

    public function exportExcel(Request $request)
    {
        [$startDate, $endDate] = $this->resolveDateRange($request);
        $report = $this->buildReportData($startDate, $endDate);

        $filename = 'Laporan Vending Machine - ' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(new VendingReportExport($report), $filename);
    }

    private function buildReportData(Carbon $startDate, Carbon $endDate): array
    {
        $totalTransactions = Sale::whereBetween('transaction_date', [$startDate, $endDate])->count();
        $paidTransactions = Sale::where('status', 'paid')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->count();
        $pendingTransactions = Sale::where('status', 'pending')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->count();
        $failedTransactions = Sale::whereIn('status', ['failed', 'expired'])
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->count();

        $periodOmzet = (int) Sale::where('status', 'paid')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('total_amount');

        $totalProductsSold = SaleLine::query()
            ->where('status', 'success')
            ->whereHas('sale', function ($query) use ($startDate, $endDate) {
                $query->where('status', 'paid')
                    ->whereBetween('transaction_date', [$startDate, $endDate]);
            })
            ->count();

        $topProducts = SaleLine::query()
            ->select([
                'products.id as product_id',
                'products.product_name',
                DB::raw('COUNT(sales_lines.id) as sold_qty'),
                DB::raw('COALESCE(SUM(prices.price), 0) as omzet'),
            ])
            ->join('sales', 'sales.id', '=', 'sales_lines.sale_id')
            ->join('product_displays', 'product_displays.id', '=', 'sales_lines.product_display_id')
            ->join('products', 'products.id', '=', 'product_displays.product_id')
            ->leftJoin('prices', 'prices.id', '=', 'product_displays.price_id')
            ->where('sales.status', 'paid')
            ->where('sales_lines.status', 'success')
            ->whereBetween('sales.transaction_date', [$startDate, $endDate])
            ->groupBy('products.id', 'products.product_name')
            ->orderByDesc('sold_qty')
            ->limit(5)
            ->get();

        $salesByDay = $this->buildDailySalesStats($startDate, $endDate);

        $averageTransaction = $paidTransactions > 0 ? (int) round($periodOmzet / $paidTransactions) : 0;
        $successRate = $totalTransactions > 0 ? round(($paidTransactions / $totalTransactions) * 100, 1) : 0.0;
        $failedRate = $totalTransactions > 0 ? round(($failedTransactions / $totalTransactions) * 100, 1) : 0.0;
        $pendingRate = $totalTransactions > 0 ? round(($pendingTransactions / $totalTransactions) * 100, 1) : 0.0;

        $recentTransactions = Sale::query()
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->latest('transaction_date')
            ->limit(10)
            ->get(['id', 'idempotency_key', 'transaction_date', 'status', 'total_amount']);

        $exportTransactions = Sale::query()
            ->with([
                'salesLines.productDisplay.product:id,product_name',
                'salesLines.productDisplay.cell:id,code',
            ])
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->latest('transaction_date')
            ->get(['id', 'idempotency_key', 'transaction_date', 'status', 'total_amount'])
            ->map(function (Sale $sale) {
                $products = $sale->salesLines
                    ->map(fn (SaleLine $line) => $line->productDisplay?->product?->product_name)
                    ->filter()
                    ->unique()
                    ->values()
                    ->implode(', ');

                $cells = $sale->salesLines
                    ->map(fn (SaleLine $line) => $line->productDisplay?->cell?->code)
                    ->filter()
                    ->unique()
                    ->values()
                    ->implode(', ');

                $statusLabel = match ($sale->status) {
                    'paid' => 'Sukses',
                    'pending' => 'Pending',
                    default => 'Gagal',
                };

                return [
                    'transaction_date' => $sale->transaction_date,
                    'idempotency_key' => $sale->idempotency_key,
                    'products' => $products !== '' ? $products : '-',
                    'total_amount' => (int) $sale->total_amount,
                    'status_label' => $statusLabel,
                    'cells' => $cells !== '' ? $cells : '-',
                ];
            });

        $exportProducts = SaleLine::query()
            ->select([
                'products.product_name',
                'prices.price as unit_price',
                'cells.code as cell_code',
                'cells.qty_current as stock_remaining',
                DB::raw('COUNT(sales_lines.id) as sold_qty'),
            ])
            ->join('sales', 'sales.id', '=', 'sales_lines.sale_id')
            ->join('product_displays', 'product_displays.id', '=', 'sales_lines.product_display_id')
            ->join('products', 'products.id', '=', 'product_displays.product_id')
            ->leftJoin('prices', 'prices.id', '=', 'product_displays.price_id')
            ->leftJoin('cells', 'cells.id', '=', 'product_displays.cell_id')
            ->where('sales.status', 'paid')
            ->where('sales_lines.status', 'success')
            ->whereBetween('sales.transaction_date', [$startDate, $endDate])
            ->groupBy('products.product_name', 'prices.price', 'cells.code', 'cells.qty_current')
            ->orderByDesc('sold_qty')
            ->get()
            ->map(fn ($row) => [
                'product_name' => $row->product_name ?? '-',
                'unit_price' => (int) ($row->unit_price ?? 0),
                'sold_qty' => (int) ($row->sold_qty ?? 0),
                'stock_remaining' => (int) ($row->stock_remaining ?? 0),
                'cell_code' => $row->cell_code ?? '-',
            ]);

        return [
            'generated_at' => now(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'period_label' => $startDate->format('d/m/Y') . ' - ' . $endDate->format('d/m/Y'),
            'total_transactions' => $totalTransactions,
            'paid_transactions' => $paidTransactions,
            'pending_transactions' => $pendingTransactions,
            'failed_transactions' => $failedTransactions,
            'total_omzet' => $periodOmzet,
            'period_omzet' => $periodOmzet,
            'total_products_sold' => $totalProductsSold,
            'average_transaction' => $averageTransaction,
            'success_rate' => $successRate,
            'failed_rate' => $failedRate,
            'pending_rate' => $pendingRate,
            'top_products' => $topProducts,
            'sales_by_day' => $salesByDay,
            'recent_transactions' => $recentTransactions,
            'export_transactions' => $exportTransactions,
            'export_products' => $exportProducts,
        ];
    }

    private function buildDailySalesStats(Carbon $startDate, Carbon $endDate): Collection
    {
        $rows = Sale::query()
            ->select([
                DB::raw('DATE(transaction_date) as day'),
                DB::raw('COUNT(*) as total_transactions'),
                DB::raw("SUM(CASE WHEN status = 'paid' THEN total_amount ELSE 0 END) as omzet"),
                DB::raw("SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as paid_transactions"),
            ])
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->keyBy('day');

        return collect(CarbonPeriod::create($startDate->copy()->startOfDay(), $endDate->copy()->startOfDay()))
            ->map(function ($date) use ($rows) {
                $key = $date->toDateString();
                $row = $rows->get($key);

                return [
                    'date' => $key,
                    'label' => $date->translatedFormat('d M'),
                    'total_transactions' => (int) ($row->total_transactions ?? 0),
                    'paid_transactions' => (int) ($row->paid_transactions ?? 0),
                    'omzet' => (int) ($row->omzet ?? 0),
                ];
            });
    }

    private function resolveDateRange(Request $request): array
    {
        $validated = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $hasStart = !empty($validated['start_date']);
        $hasEnd = !empty($validated['end_date']);

        if (!$hasStart && !$hasEnd) {
            return [now()->subDays(6)->startOfDay(), now()->endOfDay()];
        }

        if ($hasStart && $hasEnd) {
            return [
                Carbon::parse($validated['start_date'])->startOfDay(),
                Carbon::parse($validated['end_date'])->endOfDay(),
            ];
        }

        if ($hasStart) {
            return [
                Carbon::parse($validated['start_date'])->startOfDay(),
                now()->endOfDay(),
            ];
        }

        $endDate = Carbon::parse($validated['end_date'])->endOfDay();

        return [$endDate->copy()->subDays(6)->startOfDay(), $endDate];
    }
}
