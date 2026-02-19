@extends('dashboard.layouts.app', [
    'title' => 'Ukuran Kemasan',
])

@section('content')
    <section class="space-y-6 p-2">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard.master-data.index') }}">
                    <img src="{{ asset('assets/icons/dashboard/back.svg') }}" alt="Kembali">
                </a>
                <h1 class="text-[28px] font-semibold leading-none text-[#3C1C5E]">Ukuran Kemasan</h1>
            </div>

            <p class="mt-3 text-[18px] text-[#4F3970]">
                Halaman ini untuk menampilkan daftar ukuran kemasan produk.
            </p>
        </div>

        <article class="rounded-2xl border border-[#ddd2ef] bg-white p-8 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
            <div id="packagingSizesAddAction" class="hidden">
                <button id="openCreatePackagingSizeModal" type="button"
                    class="rounded-lg bg-[#5A2F7E] px-5 py-2 text-[14px] font-semibold text-white transition hover:bg-[#4B1F74]">
                    Tambah Data
                </button>
            </div>

            <div class="overflow-x-auto">
                <table id="packagingSizesTable" class="dashboard-datatable display w-full">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Ukuran Kemasan</th>
                            <th>Dibuat Oleh</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($packagingSizes as $packagingSize)
                            <tr data-packaging-size-id="{{ $packagingSize->id }}"
                                data-packaging-size-name="{{ e($packagingSize->size) }}"
                                data-packaging-size-products-count="{{ $packagingSize->products_count }}"
                                data-packaging-size-user="{{ e($packagingSize->user?->name ?? '-') }}">
                                <td class="text-center font-semibold">{{ $loop->iteration }}</td>
                                <td>{{ $packagingSize->size }}</td>
                                <td>{{ $packagingSize->user?->name ?? '-' }}</td>
                                <td class="text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" class="open-edit-packaging-size-modal">
                                            <img src="{{ asset('assets/icons/dashboard/edit.svg') }}" alt="Edit">
                                        </button>
                                        <button type="button" class="open-delete-packaging-size-modal">
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

    <div id="createPackagingSizeModal"
        class="fixed inset-0 z-50 hidden items-center justify-center p-4 backdrop-blur-[1px]"
        style="background-color: rgba(31, 17, 48, 0.48);">
        <div class="w-full overflow-hidden border border-[#ddd2ef] bg-white p-8 shadow-[0_12px_28px_rgba(60,28,94,0.2)]"
            style="max-width: 560px; border-radius: 22px;">
            <h2 class="text-center text-[24px] font-semibold text-[#3C1C5E]">Tambah Ukuran Kemasan</h2>

            <form id="createPackagingSizeForm" class="mt-6 space-y-4">
                <div>
                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]"
                        for="packagingSizeName">Ukuran Kemasan</label>
                    <input id="packagingSizeName" name="size" type="text" required
                        class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none placeholder:text-[#b5a3ca] focus:border-[#6B3E93]"
                        placeholder="Masukkan Ukuran Kemasan">
                </div>

                <div class="grid grid-cols-2 gap-4 pt-4">
                    <button id="cancelCreatePackagingSize" type="button"
                        class="h-10 rounded-lg border border-[#5A2F7E] bg-white text-[15px] font-semibold text-[#4B1F74] transition hover:bg-[#f8f4ff]">
                        Batal
                    </button>
                    <button id="submitCreatePackagingSize" type="submit"
                        class="h-10 rounded-lg bg-[#5A2F7E] text-[15px] font-semibold text-white transition hover:bg-[#4B1F74]">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="editPackagingSizeModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 backdrop-blur-[1px]"
        style="background-color: rgba(31, 17, 48, 0.48);">
        <div class="w-full overflow-hidden border border-[#ddd2ef] bg-white p-8 shadow-[0_12px_28px_rgba(60,28,94,0.2)]"
            style="max-width: 560px; border-radius: 22px;">
            <h2 class="text-center text-[24px] font-semibold text-[#3C1C5E]">Edit Ukuran Kemasan</h2>

            <form id="editPackagingSizeForm" class="mt-6 space-y-4">
                <input id="editPackagingSizeId" type="hidden">

                <div>
                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]"
                        for="editPackagingSizeName">Ukuran Kemasan</label>
                    <input id="editPackagingSizeName" name="size" type="text" required
                        class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none placeholder:text-[#b5a3ca] focus:border-[#6B3E93]"
                        placeholder="Masukkan Ukuran Kemasan">
                </div>

                <div class="grid grid-cols-2 gap-4 pt-4">
                    <button id="cancelEditPackagingSize" type="button"
                        class="h-10 rounded-lg border border-[#5A2F7E] bg-white text-[15px] font-semibold text-[#4B1F74] transition hover:bg-[#f8f4ff]">
                        Batal
                    </button>
                    <button id="submitEditPackagingSize" type="submit"
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
                selector: '#packagingSizesTable',
                pageLength: 10,
                actionContainerSelector: '#packagingSizesAddAction',
            });
        });

        (() => {
            const createModal = document.getElementById('createPackagingSizeModal');
            const editModal = document.getElementById('editPackagingSizeModal');
            const openCreateButton = document.getElementById('openCreatePackagingSizeModal');
            const cancelCreateButton = document.getElementById('cancelCreatePackagingSize');
            const cancelEditButton = document.getElementById('cancelEditPackagingSize');
            const createForm = document.getElementById('createPackagingSizeForm');
            const editForm = document.getElementById('editPackagingSizeForm');
            const editId = document.getElementById('editPackagingSizeId');
            const submitCreateButton = document.getElementById('submitCreatePackagingSize');
            const submitEditButton = document.getElementById('submitEditPackagingSize');

            if (!createModal || !editModal || !openCreateButton || !cancelCreateButton || !cancelEditButton || !
                createForm || !editForm || !editId || !submitCreateButton || !submitEditButton) return;

            const openModal = (modal) => {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            };

            const closeModal = (modal) => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            };

            const normalizeSize = (value) => String(value || '').trim();

            const openEditModal = (packagingSize) => {
                editId.value = packagingSize.id || '';
                document.getElementById('editPackagingSizeName').value = packagingSize.size || '';
                openModal(editModal);
            };

            const closeCreateModal = () => {
                closeModal(createModal);
                createForm.reset();
            };

            const closeEditModal = () => {
                closeModal(editModal);
                editForm.reset();
                editId.value = '';
            };

            openCreateButton.addEventListener('click', () => openModal(createModal));
            cancelCreateButton.addEventListener('click', closeCreateModal);
            cancelEditButton.addEventListener('click', closeEditModal);


            document.querySelectorAll('.open-edit-packaging-size-modal').forEach((button) => {
                button.addEventListener('click', async () => {
                    const row = button.closest('tr');
                    if (!row) return;

                    const usageCount = Number(row.dataset.packagingSizeProductsCount || 0);
                    if (usageCount > 0) {
                        const result = await Swal.fire({
                            icon: 'warning',
                            title: 'Ukuran kemasan sedang dipakai',
                            text: `Ukuran kemasan ini sedang dipakai oleh ${usageCount} produk. Lanjut edit?`,
                            showCancelButton: true,
                            confirmButtonText: 'Lanjut edit',
                            cancelButtonText: 'Batal',
                            confirmButtonColor: '#5A2F7E',
                        });

                        if (!result.isConfirmed) return;
                    }

                    openEditModal({
                        id: row.dataset.packagingSizeId,
                        size: row.dataset.packagingSizeName,
                    });
                });
            });

            createForm.addEventListener('submit', async (event) => {
                event.preventDefault();

                const size = normalizeSize(document.getElementById('packagingSizeName').value);
                if (!size) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data belum lengkap',
                        text: 'Ukuran kemasan wajib diisi.',
                    });
                    return;
                }

                submitCreateButton.disabled = true;
                submitCreateButton.textContent = 'Menyimpan...';

                try {
                    const response = await fetch('/api/packaging-size/store', {
                        method: 'POST',
                        headers: {
                            Accept: 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            size,
                        }),
                    });

                    const data = await response.json();
                    if (!response.ok) {
                        const errors = data?.errors ? Object.values(data.errors).flat() : [];
                        const message = errors.length > 0 ? errors[0] : (data?.message ||
                            'Gagal menambah ukuran kemasan.');
                        throw new Error(message);
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data?.message || 'Ukuran kemasan berhasil ditambahkan.',
                        timer: 1400,
                        showConfirmButton: false,
                    });

                    setTimeout(() => window.location.reload(), 1400);
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: error.message || 'Terjadi kesalahan saat menambah data.',
                    });
                } finally {
                    submitCreateButton.disabled = false;
                    submitCreateButton.textContent = 'Simpan';
                }
            });

            editForm.addEventListener('submit', async (event) => {
                event.preventDefault();

                const id = editId.value;
                const size = normalizeSize(document.getElementById('editPackagingSizeName').value);

                if (!id || !size) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data belum lengkap',
                        text: 'Ukuran kemasan wajib diisi.',
                    });
                    return;
                }

                submitEditButton.disabled = true;
                submitEditButton.textContent = 'Menyimpan...';

                try {
                    const response = await fetch(`/api/packaging-size/update/${id}`, {
                        method: 'POST',
                        headers: {
                            Accept: 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            size,
                        }),
                    });

                    const data = await response.json();
                    if (!response.ok) {
                        const errors = data?.errors ? Object.values(data.errors).flat() : [];
                        const message = errors.length > 0 ? errors[0] : (data?.message ||
                            'Gagal memperbarui ukuran kemasan.');
                        throw new Error(message);
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data?.message || 'Ukuran kemasan berhasil diperbarui.',
                        timer: 1400,
                        showConfirmButton: false,
                    });

                    setTimeout(() => window.location.reload(), 1400);
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: error.message || 'Terjadi kesalahan saat memperbarui data.',
                    });
                } finally {
                    submitEditButton.disabled = false;
                    submitEditButton.textContent = 'Simpan';
                }
            });

            document.addEventListener('click', async (event) => {
                const deleteButton = event.target.closest('.open-delete-packaging-size-modal');
                if (!deleteButton) return;

                const row = deleteButton.closest('tr');
                if (!row) return;

                const id = row.dataset.packagingSizeId;
                const size = row.dataset.packagingSizeName || 'ukuran kemasan ini';
                if (!id) return;

                const result = await Swal.fire({
                    icon: 'warning',
                    title: 'Hapus data ukuran kemasan?',
                    text: `Data "${size}" akan dihapus permanen.`,
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33',
                });

                if (!result.isConfirmed) return;

                deleteButton.disabled = true;

                try {
                    const response = await fetch(`/api/packaging-size/delete/${id}`, {
                        method: 'DELETE',
                        headers: {
                            Accept: 'application/json',
                        },
                    });

                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) {
                        throw new Error(data?.message || 'Gagal menghapus ukuran kemasan.');
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data?.message || 'Ukuran kemasan berhasil dihapus.',
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
