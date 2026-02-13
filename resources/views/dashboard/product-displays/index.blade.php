@extends('dashboard.layouts.app', [
    'title' => 'Stok dan Slot (Penataan Produk)',
])

@section('content')
    <section class="space-y-6 p-2">
        <div>
            <h1 class="text-[28px] font-semibold leading-none text-[#3C1C5E]">Stok dan Slot (Penataan Produk)</h1>
            <p class="mt-3 text-[18px] text-[#4F3970]">
                Halaman ini untuk menampilkan daftar produk yang ditampilkan di Vending Machine.
            </p>
        </div>

        <article class="rounded-2xl border border-[#ddd2ef] bg-white p-8 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
            <div id="productDisplaysActions" class="hidden">
                <button id="openCreateDisplayModal" type="button"
                    class="rounded-lg bg-[#5A2F7E] px-5 py-2 text-[14px] font-semibold text-white transition hover:bg-[#4B1F74]">
                    Tambah Data
                </button>
                <button id="openRestockModal" type="button"
                    class="rounded-lg bg-[#5A2F7E] px-5 py-2 text-[14px] font-semibold text-white transition hover:bg-[#4B1F74]">
                    Restock
                </button>
            </div>

            <div class="overflow-x-auto">
                <table id="productDisplaysTable" class="dashboard-datatable display w-full">
                    <thead>
                        <tr>
                            <th class="text-center whitespace-nowrap">No</th>
                            <th class="whitespace-nowrap">Produk</th>
                            <th class="whitespace-nowrap">Harga</th>
                            <th class="whitespace-nowrap">Kode Sel</th>
                            <th class="text-center whitespace-nowrap">Sisa Stok</th>
                            <th class="text-center whitespace-nowrap">Status</th>
                            <th class="text-center whitespace-nowrap">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($productDisplays as $productDisplay)
                            <tr data-product-display-id="{{ $productDisplay->id }}"
                                data-product-display-product-name="{{ e($productDisplay->product?->product_name ?? '-') }}"
                                data-product-display-cell-code="{{ e($productDisplay->cell?->code ?? '-') }}">
                                <td class="text-center font-semibold whitespace-nowrap">{{ $loop->iteration }}</td>
                                <td class="whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        @if ($productDisplay->product?->image_url)
                                            <img src="{{ asset('storage/' . $productDisplay->product->image_url) }}"
                                                alt="{{ $productDisplay->product->product_name }}"
                                                class="h-8 w-8 rounded-md object-cover">
                                        @endif
                                        <span>{{ $productDisplay->product?->product_name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap">
                                    @if ($productDisplay->price)
                                        Rp{{ number_format((int) $productDisplay->price->price, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="whitespace-nowrap">{{ $productDisplay->cell?->code ?? '-' }}</td>
                                <td class="text-center whitespace-nowrap">{{ $productDisplay->cell?->qty_current ?? 0 }}</td>
                                <td class="text-center whitespace-nowrap">
                                    @php
                                        $statusClass = match ($productDisplay->status) {
                                            'active' => 'bg-[#d7f2e1] text-[#17914f]',
                                            'inactive' => 'bg-[#fde0e1] text-[#de1c24]',
                                            default => 'bg-[#ffe6c8] text-[#c57a00]',
                                        };

                                        $statusLabel = match ($productDisplay->status) {
                                            'active' => 'Aktif',
                                            'inactive' => 'Tidak Aktif',
                                            default => 'Dihentikan',
                                        };
                                    @endphp
                                    <span
                                        class="inline-flex rounded-full px-4 py-1 text-[12px] font-medium {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="text-center min-w-[110px] whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" class="open-restock-row-modal"
                                            data-product-display-id="{{ $productDisplay->id }}">
                                            <img src="{{ asset('assets/icons/dashboard/restock.svg') }}" alt="Restock">
                                        </button>
                                        <a href="{{ route('dashboard.product-displays.show', ['id' => $productDisplay->id]) }}">
                                            <img src="{{ asset('assets/icons/dashboard/show.svg') }}" alt="Lihat">
                                        </a>
                                        <a href="{{ route('dashboard.product-displays.edit', ['id' => $productDisplay->id]) }}">
                                            <img src="{{ asset('assets/icons/dashboard/edit.svg') }}" alt="Edit">
                                        </a>
                                        <button type="button" class="open-delete-product-display">
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

    <div id="createProductDisplayModal"
        class="fixed inset-0 z-50 hidden items-center justify-center p-4 backdrop-blur-[1px]"
        style="background-color: rgba(31, 17, 48, 0.48);">
        <div class="w-full overflow-hidden border border-[#ddd2ef] bg-white p-8 shadow-[0_12px_28px_rgba(60,28,94,0.2)]"
            style="max-width: 860px; border-radius: 22px;">
            <h2 class="text-center text-[24px] font-semibold text-[#3C1C5E]">Tambah Data Stok dan Slot</h2>

            <form id="createProductDisplayForm" class="mt-6 space-y-4">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]" for="createProductId">Produk</label>
                        <select id="createProductId" name="product_id" required
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">
                            <option value="">Pilih produk</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]" for="createPriceId">Harga</label>
                        <select id="createPriceId" name="price_id" required
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">
                            <option value="">Pilih harga</option>
                            @foreach ($prices as $price)
                                <option value="{{ $price->id }}">
                                    {{ $price->product?->product_name ?? 'Produk' }} -
                                    Rp{{ number_format((int) $price->price, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]" for="createCellId">Sel</label>
                        <select id="createCellId" name="cell_id" required
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">
                            <option value="">Pilih sel</option>
                            @foreach ($cells as $cell)
                                <option value="{{ $cell->id }}">
                                    {{ $cell->code }} (stok {{ $cell->qty_current }}/{{ $cell->capacity }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]"
                            for="createQtyAdd">Jumlah Produk yang Ditambahkan</label>
                        <input id="createQtyAdd" name="qty_add" type="number" min="0" value="0"
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none placeholder:text-[#b5a3ca] focus:border-[#6B3E93]"
                            placeholder="10">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]" for="createStatus">Status</label>
                        <select id="createStatus" name="status" required
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">
                            <option value="active">Aktif</option>
                            <option value="inactive">Tidak Aktif</option>
                            <option value="discontinued">Dihentikan</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-4">
                    <button id="cancelCreateProductDisplay" type="button"
                        class="h-10 rounded-lg border border-[#5A2F7E] bg-white text-[15px] font-semibold text-[#4B1F74] transition hover:bg-[#f8f4ff]">
                        Batal
                    </button>
                    <button id="submitCreateProductDisplay" type="submit"
                        class="h-10 rounded-lg bg-[#5A2F7E] text-[15px] font-semibold text-white transition hover:bg-[#4B1F74]">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="restockProductDisplayModal"
        class="fixed inset-0 z-50 hidden items-center justify-center p-4 backdrop-blur-[1px]"
        style="background-color: rgba(31, 17, 48, 0.48);">
        <div class="w-full overflow-hidden border border-[#ddd2ef] bg-white p-8 shadow-[0_12px_28px_rgba(60,28,94,0.2)]"
            style="max-width: 560px; border-radius: 22px;">
            <h2 class="text-center text-[24px] font-semibold text-[#3C1C5E]">Restock Produk</h2>

            <form id="restockProductDisplayForm" class="mt-6 space-y-4">
                <div>
                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]"
                        for="restockProductDisplayId">Produk</label>
                    <select id="restockProductDisplayId" name="product_display_id" required
                        class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">
                        <option value="">Pilih produk</option>
                        @foreach ($productDisplays as $productDisplay)
                            <option value="{{ $productDisplay->id }}">
                                {{ $productDisplay->product?->product_name ?? '-' }} - {{ $productDisplay->cell?->code ?? '-' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]" for="restockQtyAdd">Jumlah yang
                        Ingin Ditambahkan</label>
                    <input id="restockQtyAdd" name="qty_add" type="number" min="1" required value="10"
                        class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none placeholder:text-[#b5a3ca] focus:border-[#6B3E93]"
                        placeholder="10">
                </div>

                <div class="grid grid-cols-2 gap-4 pt-4">
                    <button id="cancelRestockProductDisplay" type="button"
                        class="h-10 rounded-lg border border-[#5A2F7E] bg-white text-[15px] font-semibold text-[#4B1F74] transition hover:bg-[#f8f4ff]">
                        Batal
                    </button>
                    <button id="submitRestockProductDisplay" type="submit"
                        class="h-10 rounded-lg bg-[#5A2F7E] text-[15px] font-semibold text-white transition hover:bg-[#4B1F74]">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(function() {
            initDashboardDataTable({
                selector: '#productDisplaysTable',
                pageLength: 10,
                actionContainerSelector: '#productDisplaysActions',
            });
        });

        (() => {
            const createModal = document.getElementById('createProductDisplayModal');
            const restockModal = document.getElementById('restockProductDisplayModal');
            const openCreateButton = document.getElementById('openCreateDisplayModal');
            const openRestockButton = document.getElementById('openRestockModal');
            const cancelCreateButton = document.getElementById('cancelCreateProductDisplay');
            const cancelRestockButton = document.getElementById('cancelRestockProductDisplay');
            const createForm = document.getElementById('createProductDisplayForm');
            const restockForm = document.getElementById('restockProductDisplayForm');
            const restockProductDisplayIdInput = document.getElementById('restockProductDisplayId');
            const submitCreateButton = document.getElementById('submitCreateProductDisplay');
            const submitRestockButton = document.getElementById('submitRestockProductDisplay');

            if (!createModal || !restockModal || !openCreateButton || !openRestockButton || !cancelCreateButton || !
                cancelRestockButton || !createForm || !restockForm || !restockProductDisplayIdInput ||
                !submitCreateButton || !submitRestockButton) return;

            const openModal = (modal) => {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            };

            const closeModal = (modal) => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            };

            const closeCreateModal = () => {
                closeModal(createModal);
                createForm.reset();
                document.getElementById('createStatus').value = 'active';
                document.getElementById('createQtyAdd').value = '0';
            };

            const closeRestockModal = () => {
                closeModal(restockModal);
                restockForm.reset();
                document.getElementById('restockQtyAdd').value = '10';
            };

            const getErrorMessage = (data, fallback) => {
                const errors = data?.errors ? Object.values(data.errors).flat() : [];
                if (errors.length > 0) return errors[0];
                return data?.message || fallback;
            };

            const doRestock = async (productDisplayId, qtyAdd) => {
                const response = await fetch(`/api/product-display/restock/${productDisplayId}`, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        qty_add: qtyAdd,
                    }),
                });

                const data = await response.json().catch(() => ({}));
                if (!response.ok) {
                    throw new Error(getErrorMessage(data, 'Gagal melakukan restock.'));
                }

                return data;
            };

            openCreateButton.addEventListener('click', () => openModal(createModal));
            openRestockButton.addEventListener('click', () => openModal(restockModal));
            cancelCreateButton.addEventListener('click', closeCreateModal);
            cancelRestockButton.addEventListener('click', closeRestockModal);

            createModal.addEventListener('click', (event) => {
                if (event.target === createModal) closeCreateModal();
            });

            restockModal.addEventListener('click', (event) => {
                if (event.target === restockModal) closeRestockModal();
            });

            document.addEventListener('click', async (event) => {
                const restockRowButton = event.target.closest('.open-restock-row-modal');
                if (restockRowButton) {
                    const productDisplayId = restockRowButton.dataset.productDisplayId;
                    if (!productDisplayId) return;
                    restockProductDisplayIdInput.value = productDisplayId;
                    openModal(restockModal);
                    return;
                }

                const deleteButton = event.target.closest('.open-delete-product-display');
                if (!deleteButton) return;

                const row = deleteButton.closest('tr');
                if (!row) return;

                const productDisplayId = row.dataset.productDisplayId;
                const productName = row.dataset.productDisplayProductName || 'data ini';
                const cellCode = row.dataset.productDisplayCellCode || '-';
                if (!productDisplayId) return;

                const result = await Swal.fire({
                    icon: 'warning',
                    title: 'Hapus data stok dan slot?',
                    text: `Data "${productName}" pada sel "${cellCode}" akan dihapus permanen.`,
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33',
                });

                if (!result.isConfirmed) return;
                deleteButton.disabled = true;

                try {
                    const response = await fetch(`/api/product-display/delete/${productDisplayId}`, {
                        method: 'DELETE',
                        headers: {
                            Accept: 'application/json',
                        },
                    });

                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) {
                        throw new Error(getErrorMessage(data, 'Gagal menghapus data stok dan slot.'));
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data?.message || 'Data stok dan slot berhasil dihapus.',
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

            createForm.addEventListener('submit', async (event) => {
                event.preventDefault();

                const payload = {
                    product_id: Number(document.getElementById('createProductId').value),
                    price_id: Number(document.getElementById('createPriceId').value),
                    cell_id: Number(document.getElementById('createCellId').value),
                    status: document.getElementById('createStatus').value,
                };
                const qtyAdd = Number(document.getElementById('createQtyAdd').value || 0);

                if (!payload.product_id || !payload.price_id || !payload.cell_id || !payload.status || qtyAdd < 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data belum lengkap',
                        text: 'Mohon lengkapi data stok dan slot dengan benar.',
                    });
                    return;
                }

                payload.is_empty = qtyAdd <= 0;

                submitCreateButton.disabled = true;
                submitCreateButton.textContent = 'Menyimpan...';

                try {
                    const response = await fetch('/api/product-display/store', {
                        method: 'POST',
                        headers: {
                            Accept: 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(payload),
                    });

                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) {
                        throw new Error(getErrorMessage(data, 'Gagal menambah data stok dan slot.'));
                    }

                    const createdId = data?.data?.id;
                    let successText = data?.message || 'Data stok dan slot berhasil ditambahkan.';

                    if (createdId && qtyAdd > 0) {
                        const restockData = await doRestock(createdId, qtyAdd);
                        const actualAdded = Number(restockData?.actual_added || 0);
                        if (actualAdded < qtyAdd) {
                            successText =
                                `Data berhasil ditambahkan. Restock masuk ${actualAdded} dari ${qtyAdd} karena kapasitas sel terbatas.`;
                        } else {
                            successText = `Data berhasil ditambahkan dan restock ${actualAdded} item.`;
                        }
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: successText,
                        timer: 1500,
                        showConfirmButton: false,
                    });

                    setTimeout(() => window.location.reload(), 1500);
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: error.message || 'Terjadi kesalahan saat menyimpan data.',
                    });
                } finally {
                    submitCreateButton.disabled = false;
                    submitCreateButton.textContent = 'Simpan';
                }
            });

            restockForm.addEventListener('submit', async (event) => {
                event.preventDefault();

                const productDisplayId = Number(restockProductDisplayIdInput.value);
                const qtyAdd = Number(document.getElementById('restockQtyAdd').value);

                if (!productDisplayId || !qtyAdd || qtyAdd < 1) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data belum lengkap',
                        text: 'Pilih produk dan isi jumlah restock dengan benar.',
                    });
                    return;
                }

                submitRestockButton.disabled = true;
                submitRestockButton.textContent = 'Menyimpan...';

                try {
                    const data = await doRestock(productDisplayId, qtyAdd);
                    const actualAdded = Number(data?.actual_added || 0);
                    const successText = actualAdded < qtyAdd ?
                        `Restock masuk ${actualAdded} dari ${qtyAdd} karena kapasitas sel terbatas.` :
                        `Restock berhasil menambahkan ${actualAdded} item.`;

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: successText,
                        timer: 1500,
                        showConfirmButton: false,
                    });

                    setTimeout(() => window.location.reload(), 1500);
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: error.message || 'Terjadi kesalahan saat restock.',
                    });
                } finally {
                    submitRestockButton.disabled = false;
                    submitRestockButton.textContent = 'Simpan';
                }
            });
        })();
    </script>
@endpush
