@extends('dashboard.layouts.app', [
    'title' => 'Produk',
])

@section('content')
    <section class="space-y-6 p-2">
        <div>
            <h1 class="text-[28px] font-semibold leading-none text-[#3C1C5E]">Produk</h1>
            <p class="mt-3 text-[18px] text-[#4F3970]">
                Halaman ini untuk menampilkan daftar produk yang akan dijual di Vending Machine.
            </p>
        </div>

        <article class="rounded-2xl border border-[#ddd2ef] bg-white p-8 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
            <div id="productsAddAction" class="hidden">
                <a href="{{ route('dashboard.products.create') }}"
                    class="inline-flex rounded-lg bg-[#5A2F7E] px-5 py-2 text-[14px] font-semibold text-white transition hover:bg-[#4B1F74]">
                    Tambah Data
                </a>
            </div>

            <div class="overflow-x-auto">
                <table id="productsTable" class="dashboard-datatable display w-full min-w-[1180px]">
                    <thead>
                        <tr>
                            <th class="text-center whitespace-nowrap">No</th>
                            <th class="whitespace-nowrap">Produk</th>
                            <th class="whitespace-nowrap">Kategori</th>
                            <th class="whitespace-nowrap">Merek</th>
                            <th class="whitespace-nowrap">Jenis Kemasan</th>
                            <th class="whitespace-nowrap">Ukuran Kemasan</th>
                            <th class="whitespace-nowrap">Dibuat Oleh</th>
                            <th class="text-center whitespace-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr data-product-id="{{ $product->id }}" data-product-name="{{ e($product->product_name) }}">
                                <td class="text-center font-semibold whitespace-nowrap">{{ $loop->iteration }}</td>
                                <td class="whitespace-nowrap min-w-[240px]">
                                    <div class="flex items-center gap-2">
                                        <img src="{{ asset('storage/' . $product->image_url) }}" alt="{{ $product->product_name }}"
                                            class="h-8 w-8 rounded-md object-cover">
                                        <span>{{ $product->product_name }}</span>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap">{{ $product->category?->category_name ?? '-' }}</td>
                                <td class="whitespace-nowrap">{{ $product->brand?->brand_name ?? '-' }}</td>
                                <td class="whitespace-nowrap">{{ $product->packagingType?->packaging_type ?? '-' }}</td>
                                <td class="whitespace-nowrap">{{ $product->packagingSize?->size ?? '-' }}</td>
                                <td class="whitespace-nowrap">{{ $product->user?->name ?? '-' }}</td>
                                <td class="text-center whitespace-nowrap min-w-[110px]">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('dashboard.products.show', ['id' => $product->id]) }}">
                                            <img src="{{ asset('assets/icons/dashboard/show.svg') }}" alt="Lihat">
                                        </a>
                                        <a href="{{ route('dashboard.products.edit', ['id' => $product->id]) }}">
                                            <img src="{{ asset('assets/icons/dashboard/edit.svg') }}" alt="Edit">
                                        </a>
                                        <button type="button" class="open-delete-product">
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
                selector: '#productsTable',
                pageLength: 10,
                actionContainerSelector: '#productsAddAction',
            });
        });

        (() => {
            document.addEventListener('click', async (event) => {
                const deleteButton = event.target.closest('.open-delete-product');
                if (!deleteButton) return;

                const row = deleteButton.closest('tr');
                if (!row) return;

                const productId = row.dataset.productId;
                const productName = row.dataset.productName || 'produk ini';
                if (!productId) return;

                const result = await Swal.fire({
                    icon: 'warning',
                    title: 'Hapus data produk?',
                    text: `Data "${productName}" akan dihapus permanen.`,
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33',
                });

                if (!result.isConfirmed) return;

                deleteButton.disabled = true;

                try {
                    const response = await fetch(`/api/product/delete/${productId}`, {
                        method: 'DELETE',
                        headers: {
                            Accept: 'application/json',
                        },
                    });

                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) {
                        throw new Error(data?.message || 'Gagal menghapus produk.');
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data?.message || 'Produk berhasil dihapus.',
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
