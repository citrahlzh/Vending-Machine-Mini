@extends('dashboard.layouts.app', [
    'title' => 'Detail Transaksi',
])

@section('content')
    @php
        $statusLabel = match ($sale->status) {
            'paid' => 'Sukses',
            'failed', 'expired' => 'Gagal',
            default => 'Pending',
        };

        $statusClass = match ($sale->status) {
            'paid' => 'bg-[#d7f2e1] text-[#17914f]',
            'failed', 'expired' => 'bg-[#fde0e1] text-[#de1c24]',
            default => 'bg-[#ffe6c8] text-[#c57a00]',
        };

        $transactionDate = optional($sale->transaction_date ?? $sale->created_at)->format('d/m/Y H:m:s');
        $transactionAmount = (int) ($sale->total_amount ?: $orderTotal);
    @endphp

    <section class="space-y-6 p-2">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard.transactions.index') }}">
                    <img src="{{ asset('assets/icons/dashboard/back.svg') }}" alt="Kembali">
                </a>
                <h1 class="text-[28px] font-semibold leading-none text-[#3C1C5E]">Detail Transaksi</h1>
            </div>
            <p class="mt-3 text-[18px] text-[#4F3970]">Halaman ini untuk menampilkan detail transaksi.</p>
        </div>

        <article class="rounded-[26px] border border-[#ddd2ef] bg-white p-10 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
            <h2 class="text-[25px] font-semibold text-[#3C1C5E] leading-none">Detail Order</h2>

            <div class="mt-6 grid gap-6 md:grid-cols-2">
                <div>
                    <p class="text-[15px] font-semibold text-[#9b90b0]">ID Order</p>
                    <p class="mt-1 text-[16px] font-semibold text-[#3C1C5E]">{{ $sale->idempotency_key }}</p>
                </div>
                <div>
                    <p class="text-[15px] font-semibold text-[#9b90b0]">Waktu Transaksi</p>
                    <p class="mt-1 text-[16px] font-semibold text-[#3C1C5E]">
                        {{ $transactionDate }}
                    </p>
                </div>
                <div>
                    <p class="text-[15px] font-semibold text-[#9b90b0]">Nominal Transaksi</p>
                    <p class="mt-1 text-[16px] font-semibold text-[#3C1C5E]">
                        Rp{{ number_format($transactionAmount, 0, ',', '.') }},00
                    </p>
                </div>
                <div>
                    <p class="text-[15px] font-semibold text-[#9b90b0]">Status Transaksi</p>
                    <span class="mt-2 inline-flex rounded-full px-4 py-1 text-[16px] font-medium {{ $statusClass }}">
                        {{ $statusLabel }}
                    </span>
                </div>
            </div>

            <div class="mt-9">
                <h3 class="text-[25px] font-semibold text-[#3C1C5E]">Detail Produk yang Dipesan</h3>

                <div class="mt-4 overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left">
                                <th class="pb-4 text-[15px] font-semibold text-[#9b90b0]">Produk</th>
                                <th class="pb-4 text-right text-[15px] font-semibold text-[#9b90b0]">Qty</th>
                                <th class="pb-4 text-right text-[15px] font-semibold text-[#9b90b0]">Harga</th>
                                <th class="pb-4 text-right text-[15px] font-semibold text-[#9b90b0]">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orderItems as $item)
                                <tr>
                                    <td class="pt-1 text-16px] font-semibold leading-tight text-[#3C1C5E]">
                                        {{ $item['product_name'] }}
                                    </td>
                                    <td class="pt-1 text-right text-[16px] font-semibold leading-tight text-[#3C1C5E]">
                                        {{ $item['qty'] }}
                                    </td>
                                    <td class="pt-1 text-right text-[16px] font-semibold leading-tight text-[#3C1C5E]">
                                        Rp{{ number_format((int) $item['price'], 0, ',', '.') }},00
                                    </td>
                                    <td class="pt-1 text-right text-[16px] font-semibold leading-tight text-[#3C1C5E]">
                                        Rp{{ number_format((int) $item['subtotal'], 0, ',', '.') }},00
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="pt-5 text-center text-[16px] text-[#9b90b0]">
                                        Tidak ada detail produk pada transaksi ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" class="pt-5 text-right text-[15px] font-semibold text-[#9b90b0]">Total:</td>
                                <td class="pt-5 text-right text-[16px] font-semibold text-[#3C1C5E] leading-none">
                                    Rp{{ number_format((int) $orderTotal, 0, ',', '.') }},00
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </article>
    </section>
@endsection
