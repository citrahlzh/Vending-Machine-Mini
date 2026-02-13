@extends('dashboard.layouts.app', [
    'title' => 'Transaksi',
])

@section('content')
    <section class="space-y-6 p-2">
        <div>
            <h1 class="text-[28px] font-semibold leading-none text-[#3C1C5E]">Transaksi</h1>
            <p class="mt-3 text-[18px] text-[#4F3970]">
                Halaman ini untuk menampilkan daftar transaksi yang telah terjadi pada Vending Machine.
            </p>
        </div>

        <article class="rounded-2xl border border-[#ddd2ef] bg-white p-6 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
            <div class="overflow-x-auto">
                <table id="transactionsTable" class="dashboard-datatable display w-full">
                    <thead>
                        <tr>
                            <th class="text-center">ID Order</th>
                            <th class="text-center whitespace-nowrap">Produk</th>
                            <th class="text-center whitespace-nowrap">Tanggal Transaksi</th>
                            <th class="text-center whitespace-nowrap">Nominal Jumlah</th>
                            <th class="text-center whitespace-nowrap">Status</th>
                            <th class="text-center whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $sale)
                            @php
                                $groupedProducts = $sale->salesLines
                                    ->groupBy('product_display_id')
                                    ->map(function ($lines) {
                                        $productName = $lines->first()?->productDisplay?->product?->product_name ?? 'Produk tidak ditemukan';
                                        $qty = $lines->count();
                                        return $qty > 1 ? $productName . ' (' . $qty . ')' : $productName;
                                    })
                                    ->values();

                                $productText = $groupedProducts->isNotEmpty() ? $groupedProducts->join(', ') : '-';

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
                            @endphp
                            <tr>
                                <td class="font-semibold text-[#1f1f1f] text-center">{{ $sale->idempotency_key }}</td>
                                <td class="">{{ $productText }}</td>
                                <td class="text-center whitespace-nowrap">{{ optional($sale->transaction_date)->format('d/m/Y') }}</td>
                                <td class="text-center whitespace-nowrap">Rp{{ number_format((int) $sale->total_amount, 0, ',', '.') }},00</td>
                                <td class="text-center whitespace-nowrap">
                                    <span
                                        class="inline-flex rounded-full px-4 py-1 text-[12px] font-medium {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="text-center whitespace-nowrap min-w-[110px]">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('dashboard.transactions.show', ['id' => $sale->id]) }}">
                                            <img src="{{ asset('assets/icons/dashboard/show.svg') }}" alt="Lihat">
                                        </a>
                                        <button type="button"
                                            data-transaction-id="{{ $sale->id }}"
                                            data-order-id="{{ $sale->idempotency_key }}"
                                            data-status="{{ $sale->status }}"
                                            title="{{ $sale->status === 'pending' ? 'Batalkan transaksi pending' : 'Hanya transaksi pending yang bisa dibatalkan' }}"
                                            @if ($sale->status !== 'pending') disabled @endif
                                            class="open-cancel-transaction {{ $sale->status !== 'pending' ? 'opacity-40 cursor-not-allowed' : '' }}">
                                            <img src="{{ asset('assets/icons/dashboard/delete.svg') }}" alt="Hapus">
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </article>
    </section>
@endsection


@push('script')
    <script>
        $(function() {
            initDashboardDataTable({
                selector: '#transactionsTable',
                pageLength: 4,
            });
        });

        document.addEventListener('click', async function(event) {
            const cancelButton = event.target.closest('.open-cancel-transaction');
            if (!cancelButton || cancelButton.disabled) return;

            const transactionId = cancelButton.getAttribute('data-transaction-id');
            const orderId = cancelButton.getAttribute('data-order-id');
            const status = cancelButton.getAttribute('data-status');

            if (!transactionId || status !== 'pending') return;

            const result = await Swal.fire({
                icon: 'warning',
                title: 'Batalkan transaksi?',
                text: `Transaksi "${orderId}" akan dibatalkan.`,
                showCancelButton: true,
                confirmButtonText: 'Ya, batalkan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#5A2F7E',
                cancelButtonColor: '#9b90b0',
            });

            if (!result.isConfirmed) return;

            cancelButton.disabled = true;
            cancelButton.classList.add('opacity-40', 'cursor-not-allowed');

            try {
                const response = await fetch(`/api/dashboard/transaction/cancel/${transactionId}`, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                });

                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data?.message || 'Gagal membatalkan transaksi.');
                }

                await Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: data?.message || 'Transaksi berhasil dibatalkan.',
                    confirmButtonColor: '#5A2F7E',
                });

                window.location.reload();
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: error.message || 'Terjadi kesalahan saat membatalkan transaksi.',
                    confirmButtonColor: '#5A2F7E',
                });
                cancelButton.disabled = false;
                cancelButton.classList.remove('opacity-40', 'cursor-not-allowed');
            }
        });
    </script>
@endpush

