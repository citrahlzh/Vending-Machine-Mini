@extends('dashboard.layouts.app', [
    'title' => 'Laporan',
])

@section('content')
    @php
        $maxDailyOmzet = max(1, (int) $report['sales_by_day']->max('omzet'));
    @endphp

    <section class="space-y-6 p-2">
        <div>
            <h1 class="text-[28px] font-semibold leading-none text-[#3C1C5E]">Laporan</h1>
            <p class="mt-3 text-[18px] text-[#4F3970]">
                Ringkasan statistik transaksi, omzet, penjualan produk, dan performa vending machine.
            </p>
            <p class="mt-2 text-[13px] font-semibold text-[#7a6798]">
                Periode aktif: {{ $report['period_label'] }}
            </p>

            <div class="mt-4">
                <div class="flex flex-col gap-2 lg:flex-row lg:items-end lg:justify-between">
                    <form method="GET" action="{{ route('dashboard.reports.index') }}"
                        class="grid w-full grid-cols-1 gap-2 sm:grid-cols-2 lg:w-auto lg:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto_auto] lg:items-end">
                        <div class="min-w-0">
                            <label for="start_date" class="block text-[12px] font-semibold text-[#7a6798]">Dari</label>
                            <input id="start_date" name="start_date" type="date" value="{{ request('start_date') }}"
                                class="h-10 w-full rounded-md border border-[#ddd2ef] bg-white px-3 text-[14px] text-[#3C1C5E] focus:border-[#5A2F7E] focus:outline-none" />
                        </div>
                        <div class="min-w-0">
                            <label for="end_date" class="block text-[12px] font-semibold text-[#7a6798]">Sampai</label>
                            <input id="end_date" name="end_date" type="date" value="{{ request('end_date') }}"
                                class="h-10 w-full rounded-md border border-[#ddd2ef] bg-white px-3 text-[14px] text-[#3C1C5E] focus:border-[#5A2F7E] focus:outline-none" />
                        </div>
                        <button type="submit"
                            class="inline-flex h-10 w-full items-center justify-center rounded-lg bg-[#5A2F7E] px-4 text-[14px] font-semibold text-white transition hover:bg-[#4B1F74] lg:w-auto">
                            Terapkan
                        </button>
                        <a href="{{ route('dashboard.reports.index') }}"
                            class="inline-flex h-10 w-full items-center justify-center rounded-lg border border-[#ddd2ef] bg-white px-4 text-[14px] font-semibold text-[#4F3970] transition hover:bg-[#f8f4ff] lg:w-auto">
                            Reset
                        </a>
                    </form>

                    <a href="{{ route('dashboard.reports.export-pdf', request()->only(['start_date', 'end_date'])) }}"
                        class="inline-flex h-10 w-full items-center justify-center rounded-lg bg-[#5A2F7E] px-5 text-[14px] font-semibold text-white transition hover:bg-[#4B1F74] lg:w-auto lg:shrink-0">
                        Export PDF
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-2xl border border-[#ddd2ef] bg-white p-5 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
                <p class="text-[13px] font-semibold text-[#7a6798]">Keseluruhan Transaksi</p>
                <p class="mt-2 text-[34px] font-semibold leading-none text-[#3C1C5E]">{{ $report['total_transactions'] }}</p>
            </article>
            <article class="rounded-2xl border border-[#ddd2ef] bg-white p-5 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
                <p class="text-[13px] font-semibold text-[#7a6798]">Produk Terjual</p>
                <p class="mt-2 text-[34px] font-semibold leading-none text-[#3C1C5E]">{{ $report['total_products_sold'] }}</p>
            </article>
            <article class="rounded-2xl border border-[#ddd2ef] bg-white p-5 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
                <p class="text-[13px] font-semibold text-[#7a6798]">Omzet Keseluruhan</p>
                <p class="mt-2 text-[34px] font-semibold leading-none text-[#3C1C5E]">
                    Rp{{ number_format((int) $report['total_omzet'], 0, ',', '.') }}
                </p>
            </article>
            <article class="rounded-2xl border border-[#ddd2ef] bg-white p-5 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
                <p class="text-[13px] font-semibold text-[#7a6798]">Omzet Pada Periode</p>
                <p class="mt-2 text-[34px] font-semibold leading-none text-[#3C1C5E]">
                    Rp{{ number_format((int) $report['period_omzet'], 0, ',', '.') }}
                </p>
            </article>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
            <article
                class="xl:col-span-2 rounded-2xl border border-[#ddd2ef] bg-white p-6 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
                <h2 class="text-[22px] font-semibold text-[#3C1C5E]">Statistik Penjualan Harian</h2>
                <p class="mt-1 text-[14px] text-[#7a6798]">Jumlah transaksi harian dan kontribusi omzet.</p>

                <div class="mt-6 grid grid-cols-1 gap-3">
                    @foreach ($report['sales_by_day'] as $day)
                        @php
                            $widthPercent = min(100, max(4, round(($day['omzet'] / $maxDailyOmzet) * 100)));
                        @endphp
                        <div>
                            <div class="mb-1 flex items-center justify-between text-[13px] text-[#5f4f78]">
                                <span>{{ $day['label'] }}</span>
                                <span>
                                    Trx {{ $day['total_transactions'] }} | Sukses {{ $day['paid_transactions'] }} | Rp{{ number_format((int) $day['omzet'], 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="h-2.5 rounded-full bg-[#eee6fb]">
                                <div class="h-full rounded-full bg-[#5A2F7E]" style="width: {{ $widthPercent }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </article>

            <article class="rounded-2xl border border-[#ddd2ef] bg-white p-6 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
                <h2 class="text-[22px] font-semibold text-[#3C1C5E]">Statistik Transaksi</h2>
                <p class="mt-1 text-[14px] text-[#7a6798]">Distribusi status transaksi.</p>

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
                    <p class="text-[13px] font-semibold text-[#7a6798]">Rata-rata Nilai Transaksi Sukses</p>
                    <p class="mt-1 text-[24px] font-semibold leading-none text-[#3C1C5E]">
                        Rp{{ number_format((int) $report['average_transaction'], 0, ',', '.') }}
                    </p>
                </div>
            </article>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
            <article class="rounded-2xl border border-[#ddd2ef] bg-white p-6 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
                <h2 class="text-[22px] font-semibold text-[#3C1C5E]">Produk Terlaris</h2>
                <p class="mt-1 text-[14px] text-[#7a6798]">Berdasarkan kuantitas produk terjual.</p>

                <div class="mt-5 overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left">
                                <th class="pb-2 text-[13px] font-semibold text-[#9b90b0]">Produk</th>
                                <th class="pb-2 text-right text-[13px] font-semibold text-[#9b90b0]">Terjual</th>
                                <th class="pb-2 text-right text-[13px] font-semibold text-[#9b90b0]">Omzet</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($report['top_products'] as $product)
                                <tr>
                                    <td class="border-t border-[#efe7fb] py-2 text-[14px] font-semibold text-[#3C1C5E]">
                                        {{ $product->product_name }}
                                    </td>
                                    <td class="border-t border-[#efe7fb] py-2 text-right text-[14px] font-semibold text-[#3C1C5E]">
                                        {{ (int) $product->sold_qty }}
                                    </td>
                                    <td class="border-t border-[#efe7fb] py-2 text-right text-[14px] font-semibold text-[#3C1C5E]">
                                        Rp{{ number_format((int) $product->omzet, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="pt-4 text-center text-[14px] text-[#9b90b0]">
                                        Belum ada data penjualan sukses.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>

            <article class="rounded-2xl border border-[#ddd2ef] bg-white p-6 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
                <h2 class="text-[22px] font-semibold text-[#3C1C5E]">Transaksi Terbaru</h2>
                <p class="mt-1 text-[14px] text-[#7a6798]">Monitoring transaksi terakhir pada vending machine.</p>

                <div class="mt-5 space-y-2">
                    @forelse ($report['recent_transactions'] as $transaction)
                        @php
                            $badgeClass = match ($transaction->status) {
                                'paid' => 'bg-[#d7f2e1] text-[#17914f]',
                                'pending' => 'bg-[#fff2df] text-[#c57a00]',
                                default => 'bg-[#fde0e1] text-[#de1c24]',
                            };
                        @endphp
                        <div class="rounded-xl border border-[#efe7fb] bg-[#fcfaff] p-3">
                            <div class="flex items-center justify-between gap-3">
                                <p class="truncate text-[14px] font-semibold text-[#3C1C5E]">{{ $transaction->idempotency_key }}</p>
                                <span class="inline-flex rounded-full px-3 py-0.5 text-[11px] font-semibold {{ $badgeClass }}">
                                    {{ strtoupper($transaction->status) }}
                                </span>
                            </div>
                            <div class="mt-1 flex items-center justify-between text-[12px] text-[#7a6798]">
                                <span>{{ optional($transaction->transaction_date)->format('d/m/Y H:i') }}</span>
                                <span class="font-semibold text-[#3C1C5E]">
                                    Rp{{ number_format((int) $transaction->total_amount, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-[14px] text-[#9b90b0]">Belum ada transaksi.</p>
                    @endforelse
                </div>
            </article>
        </div>
    </section>
@endsection
