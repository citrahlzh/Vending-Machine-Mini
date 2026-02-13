<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $startOfDay = $today->copy()->startOfDay();
        $endOfDay = $today->copy()->endOfDay();

        $paidTodayQuery = Sale::query()
            ->where('status', 'paid')
            ->whereBetween('transaction_date', [$startOfDay, $endOfDay]);

        $omzetHariIni = (int) $paidTodayQuery->sum('total_amount');
        $transaksiSukses = (int) (clone $paidTodayQuery)->count();

        $transaksiGagal = (int) Sale::query()
            ->whereIn('status', ['failed', 'expired'])
            ->whereBetween('transaction_date', [$startOfDay, $endOfDay])
            ->count();

        $hourlyRows = Sale::query()
            ->selectRaw('HOUR(transaction_date) as hour_number, COUNT(*) as total')
            ->whereBetween('transaction_date', [$startOfDay, $endOfDay])
            ->groupBy(DB::raw('HOUR(transaction_date)'))
            ->pluck('total', 'hour_number');

        $chartLabels = [];
        $chartValues = [];
        for ($hour = 8; $hour <= 16; $hour++) {
            $chartLabels[] = sprintf('%02d:00', $hour);
            $chartValues[] = (int) ($hourlyRows[$hour] ?? 0);
        }

        return view('dashboard.index', compact(
            'omzetHariIni',
            'transaksiSukses',
            'transaksiGagal',
            'chartLabels',
            'chartValues'
        ));
    }

    public function masterIndex()
    {
        return view('dashboard.master-data.index');
    }
}
