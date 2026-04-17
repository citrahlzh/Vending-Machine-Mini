@extends('dashboard.layouts.app', [
    'title' => 'Laporan',
])

@section('content')
    @php
        $maxDailyOmzet = max(1, (int) $report['sales_by_day']->max('omzet'));
    @endphp

    <section class="space-y-6 p-2">
        <div>
            <h1 class="text-[28px] font-semibold leading-none text-[#5E1C3D]">Laporan</h1>
            <p class="mt-3 text-[18px] text-[#703967]">
                Ringkasan statistik transaksi, omzet, penjualan produk, dan performa vending machine.
            </p>
            <p class="mt-2 text-[13px] font-semibold text-[#98678e]">
                Periode aktif: {{ $report['period_label'] }}
            </p>

            <div class="mt-4">
                <div class="flex flex-col gap-2 lg:flex-row lg:items-end lg:justify-between">
                    <form method="GET" action="{{ route('dashboard.reports.index') }}"
                        class="grid w-full grid-cols-1 gap-2 sm:grid-cols-2 lg:w-auto lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto_auto] lg:items-end">
                        <div class="min-w-0">
                            <label for="start_date" class="block text-[12px] font-semibold text-[#98678e]">Dari</label>
                            <input id="start_date" name="start_date" type="date" value="{{ request('start_date') }}"
                                class="h-10 w-full rounded-md border border-[#efd2e9] bg-white px-3 text-[14px] text-[#5E1C3D] focus:border-[#802A76] focus:outline-none" />
                        </div>
                        <div class="min-w-0">
                            <label for="end_date" class="block text-[12px] font-semibold text-[#98678e]">Sampai</label>
                            <input id="end_date" name="end_date" type="date" value="{{ request('end_date') }}"
                                class="h-10 w-full rounded-md border border-[#efd2e9] bg-white px-3 text-[14px] text-[#5E1C3D] focus:border-[#802A76] focus:outline-none" />
                        </div>
                        <button type="submit"
                            class="inline-flex h-10 w-full items-center justify-center rounded-lg bg-[#802A76] px-4 text-[14px] font-semibold text-white transition hover:bg-[#741f58] lg:w-auto">
                            Terapkan
                        </button>
                        <a href="{{ route('dashboard.reports.index') }}"
                            class="inline-flex h-10 w-full items-center justify-center rounded-lg border border-[#efd2e9] bg-white px-4 text-[14px] font-semibold text-[#703967] transition hover:bg-[#f8f4ff] lg:w-auto">
                            Reset
                        </a>
                    </form>

                    <a href="{{ route('dashboard.reports.export-excel', request()->only(['start_date', 'end_date'])) }}"
                        class="inline-flex h-10 w-full items-center justify-center rounded-lg bg-[#802A76] px-5 text-[14px] font-semibold text-white transition hover:bg-[#741f58] lg:w-auto lg:shrink-0">
                        Export Excel
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-2xl border border-[#efd2e9] bg-white p-5 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
                <p class="text-[13px] font-semibold text-[#98678e]">Keseluruhan Transaksi</p>
                <p class="mt-2 text-[34px] font-semibold leading-none text-[#5E1C3D]">{{ $report['total_transactions'] }}</p>
            </article>
            <article class="rounded-2xl border border-[#efd2e9] bg-white p-5 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
                <p class="text-[13px] font-semibold text-[#98678e]">Produk Terjual</p>
                <p class="mt-2 text-[34px] font-semibold leading-none text-[#5E1C3D]">{{ $report['total_products_sold'] }}</p>
            </article>
            <article class="rounded-2xl border border-[#efd2e9] bg-white p-5 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
                <p class="text-[13px] font-semibold text-[#98678e]">Omzet Keseluruhan</p>
                <p class="mt-2 text-[34px] font-semibold leading-none text-[#5E1C3D]">
                    Rp{{ number_format((int) $report['total_omzet'], 0, ',', '.') }}
                </p>
            </article>
            <article class="rounded-2xl border border-[#efd2e9] bg-white p-5 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
                <p class="text-[13px] font-semibold text-[#98678e]">Omzet Pada Periode</p>
                <p class="mt-2 text-[34px] font-semibold leading-none text-[#5E1C3D]">
                    Rp{{ number_format((int) $report['period_omzet'], 0, ',', '.') }}
                </p>
            </article>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <article
                class="xl:col-span-2 rounded-2xl border border-[#efd2e9] bg-white p-6 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
                <h2 class="text-[22px] font-semibold text-[#5E1C3D]">Statistik Penjualan Harian</h2>
                <p class="mt-1 text-[14px] text-[#98678e]">Jumlah transaksi harian dan kontribusi omzet.</p>

                <div class="mt-6 grid grid-cols-1 gap-3">
                    @foreach ($report['sales_by_day'] as $day)
                        @php
                            $widthPercent = min(100, max(4, round(($day['omzet'] / $maxDailyOmzet) * 100)));
                        @endphp
                        <div>
                            <div class="mb-1 flex items-center justify-between text-[13px] text-[#784f6c]">
                                <span>{{ $day['label'] }}</span>
                                <span>
                                    Trx {{ $day['total_transactions'] }} | Sukses {{ $day['paid_transactions'] }} | Rp{{ number_format((int) $day['omzet'], 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="h-2.5 rounded-full bg-[#eee6fb]">
                                <div class="h-full rounded-full bg-[#802A76]" style="width: {{ $widthPercent }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </article>

            <article class="rounded-2xl border border-[#efd2e9] bg-white p-6 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
                <h2 class="text-[22px] font-semibold text-[#5E1C3D]">Statistik Transaksi</h2>
                <p class="mt-1 text-[14px] text-[#98678e]">Distribusi status transaksi.</p>

                <div class="mt-5 space-y-4">
                    <div>
                        <div class="flex items-center justify-between text-[13px] font-semibold text-[#17914f]">
                            <span>Sukses</span>
                            <span>{{ $report['paid_transactions'] }} ({{ $report['success_rate'] }}%)</span>
                        </div>
                        <div class="mt-1 h-2 rounded-full bg-[#e8f6ee]">
                            <div class="h-full rounded-full bg-[#17914f]" style="width: {{ min(100, $report['success_rate']) }}%">
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between text-[13px] font-semibold text-[#c57a00]">
                            <span>Pending</span>
                            <span>{{ $report['pending_transactions'] }} ({{ $report['pending_rate'] }}%)</span>
                        </div>
                        <div class="mt-1 h-2 rounded-full bg-[#fff2df]">
                            <div class="h-full rounded-full bg-[#f5a524]" style="width: {{ min(100, $report['pending_rate']) }}%">
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between text-[13px] font-semibold text-[#de1c24]">
                            <span>Gagal / Expired</span>
                            <span>{{ $report['failed_transactions'] }} ({{ $report['failed_rate'] }}%)</span>
                        </div>
                        <div class="mt-1 h-2 rounded-full bg-[#ffe8e9]">
                            <div class="h-full rounded-full bg-[#de1c24]" style="width: {{ min(100, $report['failed_rate']) }}%">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 rounded-xl bg-[#f8f4ff] p-4">
                    <p class="text-[13px] font-semibold text-[#98678e]">Rata-rata Nilai Transaksi Sukses</p>
                    <p class="mt-1 text-[24px] font-semibold leading-none text-[#5E1C3D]">
                        Rp{{ number_format((int) $report['average_transaction'], 0, ',', '.') }}
                    </p>
                </div>
            </article>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            <article class="rounded-2xl border border-[#efd2e9] bg-white p-6 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
                <h2 class="text-[22px] font-semibold text-[#5E1C3D]">Produk Terlaris</h2>
                <p class="mt-1 text-[14px] text-[#98678e]">Berdasarkan kuantitas produk terjual.</p>

                <div class="mt-5 overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left">
                                <th class="pb-2 text-[13px] font-semibold text-[#b090a7]">Produk</th>
                                <th class="pb-2 text-right text-[13px] font-semibold text-[#b090a7]">Terjual</th>
                                <th class="pb-2 text-right text-[13px] font-semibold text-[#b090a7]">Omzet</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($report['top_products'] as $product)
                                <tr>
                                    <td class="border-t border-[#fbe7f7] py-2 text-[14px] font-semibold text-[#5E1C3D]">
                                        {{ $product->product_name }}
                                    </td>
                                    <td class="border-t border-[#fbe7f7] py-2 text-right text-[14px] font-semibold text-[#5E1C3D]">
                                        {{ (int) $product->sold_qty }}
                                    </td>
                                    <td class="border-t border-[#fbe7f7] py-2 text-right text-[14px] font-semibold text-[#5E1C3D]">
                                        Rp{{ number_format((int) $product->omzet, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="pt-4 text-center text-[14px] text-[#b090a7]">
                                        Belum ada data penjualan sukses.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>

            <article class="rounded-2xl border border-[#efd2e9] bg-white p-6 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
                <h2 class="text-[22px] font-semibold text-[#5E1C3D]">Transaksi Terbaru</h2>
                <p class="mt-1 text-[14px] text-[#98678e]">Monitoring transaksi terakhir pada vending machine.</p>

                <div class="mt-5 space-y-2">
                    @forelse ($report['recent_transactions'] as $transaction)
                        @php
                            $badgeClass = match ($transaction->status) {
                                'paid' => 'bg-[#d7f2e1] text-[#17914f]',
                                'pending' => 'bg-[#fff2df] text-[#c57a00]',
                                default => 'bg-[#fde0e1] text-[#de1c24]',
                            };
                        @endphp
                        <div class="rounded-xl border border-[#fbe7f7] bg-[#fcfaff] p-3">
                            <div class="flex items-center justify-between gap-3">
                                <p class="truncate text-[14px] font-semibold text-[#5E1C3D]">{{ $transaction->idempotency_key }}</p>
                                <span class="inline-flex rounded-full px-3 py-0.5 text-[11px] font-semibold {{ $badgeClass }}">
                                    {{ strtoupper($transaction->status) }}
                                </span>
                            </div>
                            <div class="mt-1 flex items-center justify-between text-[12px] text-[#98678e]">
                                <span>{{ optional($transaction->transaction_date)->format('d/m/Y H:i') }}</span>
                                <span class="font-semibold text-[#5E1C3D]">
                                    Rp{{ number_format((int) $transaction->total_amount, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-[14px] text-[#b090a7]">Belum ada transaksi.</p>
                    @endforelse
                </div>
            </article>
        </div>
    </section>
@endsection

