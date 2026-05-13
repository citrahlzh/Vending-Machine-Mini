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
        for ($hour = 0; $hour <= 23; $hour++) {
            $chartLabels[] = sprintf('%02d:00', $hour);
            $chartValues[] = (int) ($hourlyRows[$hour] ?? 0);
        }

        $machineIdentity = [
            'name' => machine_setting('name', '-'),
            'code' => machine_setting('code', '-'),
            'serial_number' => machine_setting('serial_number', '-'),
            'location' => machine_setting('location', '-'),
            'operator_name' => machine_setting('operator_name', '-'),
            'category' => machine_setting('category', '-'),
            'size' => machine_setting('size', '-'),
            'is_android' => machine_setting('is_android', false),
            'status' => machine_setting('status', 'inactive'),
            'condition_status' => machine_setting('condition_status', 'good'),
            'photo_url' => machine_asset_url('photo_url'),
        ];

        return view('dashboard.index', compact(
            'omzetHariIni',
            'transaksiSukses',
            'transaksiGagal',
            'chartLabels',
            'chartValues',
            'machineIdentity'
        ));
    }

    public function masterIndex()
    {
        return view('dashboard.master-data.index');
    }

    public function gameManagementIndex()
    {
        return view('dashboard.game-management.index');
    }
}
