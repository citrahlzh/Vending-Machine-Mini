@extends('dashboard.layouts.app', [
    'title' => 'Harga',
])

@section('content')
    <section class="space-y-6 p-2">
        <div>
            <h1 class="text-[28px] font-semibold leading-none text-[#3C1C5E]">Harga</h1>
            <p class="mt-3 text-[18px] text-[#4F3970]">
                Halaman ini untuk menampilkan daftar harga produk yang telah didaftarkan.
            </p>
        </div>

        <article class="rounded-2xl border border-[#ddd2ef] bg-white p-8 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
            <div id="pricesAddAction" class="hidden">
                <a href="{{ route('dashboard.prices.create') }}"
                    class="inline-flex rounded-lg bg-[#5A2F7E] px-5 py-2 text-[14px] font-semibold text-white transition hover:bg-[#4B1F74]">
                    Tambah Data
                </a>
            </div>

            <div class="overflow-x-auto">
                <table id="pricesTable" class="dashboard-datatable display w-full">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Product</th>
                            <th>Harga</th>
                            <th>Masa Berlaku</th>
                            <th>Dibuat Oleh</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($prices as $price)
                            <tr data-price-id="{{ $price->id }}" data-price-product-name="{{ e($price->product?->product_name ?? '-') }}">
                                <td class="text-center font-semibold">{{ $loop->iteration }}</td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        @if ($price->product?->image_url)
                                            <img src="{{ asset('storage/' . $price->product->image_url) }}"
                                                alt="{{ $price->product->product_name }}" class="h-8 w-8 rounded-md object-cover">
                                        @endif
                                        <span>{{ $price->product?->product_name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td>Rp{{ number_format((int) $price->price, 0, ',', '.') }}</td>
                                <td>
                                    {{ optional($price->start_date)->format('d/m/Y') }} -
                                    {{ optional($price->end_date)->format('d/m/Y') }}
                                </td>
                                <td>{{ $price->user?->name ?? '-' }}</td>
                                <td class="text-center">
                                    @if ($price->is_active)
                                        <span
                                            class="inline-flex rounded-full bg-[#d7f2e1] px-4 py-1 text-[12px] font-medium text-[#17914f]">
                                            Aktif
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex rounded-full bg-[#fde0e1] px-4 py-1 text-[12px] font-medium text-[#de1c24]">
                                            Tidak Aktif
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('dashboard.prices.show', ['id' => $price->id]) }}">
                                            <img src="{{ asset('assets/icons/dashboard/show.svg') }}" alt="Lihat">
                                        </a>
                                        <a href="{{ route('dashboard.prices.edit', ['id' => $price->id]) }}">
                                            <img src="{{ asset('assets/icons/dashboard/edit.svg') }}" alt="Edit">
                                        </a>
                                        <button type="button" class="open-delete-price">
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
                selector: '#pricesTable',
                pageLength: 10,
                actionContainerSelector: '#pricesAddAction',
            });
        });

        (() => {
            document.addEventListener('click', async (event) => {
                const deleteButton = event.target.closest('.open-delete-price');
                if (!deleteButton) return;

                const row = deleteButton.closest('tr');
                if (!row) return;

                const priceId = row.dataset.priceId;
                const productName = row.dataset.priceProductName || 'harga ini';
                if (!priceId) return;

                const result = await Swal.fire({
                    icon: 'warning',
                    title: 'Hapus data harga?',
                    text: `Harga produk "${productName}" akan dihapus permanen.`,
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33',
                });

                if (!result.isConfirmed) return;

                deleteButton.disabled = true;

                try {
                    const response = await fetch(`/api/price/delete/${priceId}`, {
                        method: 'DELETE',
                        headers: {
                            Accept: 'application/json',
                        },
                    });

                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) {
                        throw new Error(data?.message || 'Gagal menghapus data harga.');
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data?.message || 'Data harga berhasil dihapus.',
                        timer: 1400,
                        showConfirmButton: false,
                    });

                    setTimeout(() => window.location.reload(), 1400);
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: error.message || 'Terjadi kesalahan saat menghapus data.',
                    });
                } finally {
                    deleteButton.disabled = false;
                }
            });
        })();
    </script>
@endpush
