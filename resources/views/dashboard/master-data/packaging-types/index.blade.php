@extends('dashboard.layouts.app', [
    'title' => 'Jenis Kemasan',
])

@section('content')
    <section class="space-y-6 p-2">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard.master-data.index') }}">
                    <img src="{{ asset('assets/icons/dashboard/back.svg') }}" alt="Kembali">
                </a>
                <h1 class="text-[28px] font-semibold leading-none text-[#3C1C5E]">Jenis Kemasan</h1>
            </div>

            <p class="mt-3 text-[18px] text-[#4F3970]">
                Halaman ini untuk menampilkan daftar jenis kemasan produk.
            </p>
        </div>

        <article class="rounded-2xl border border-[#ddd2ef] bg-white p-8 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
            <div id="packagingTypesAddAction" class="hidden">
                <button id="openCreatePackagingTypeModal" type="button"
                    class="rounded-lg bg-[#5A2F7E] px-5 py-2 text-[14px] font-semibold text-white transition hover:bg-[#4B1F74]">
                    Tambah Data
                </button>
            </div>

            <div class="overflow-x-auto">
                <table id="packagingTypesTable" class="dashboard-datatable display w-full">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Jenis Kemasan</th>
                            <th>Dibuat Oleh</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($packagingTypes as $packagingType)
                            <tr data-packaging-type-id="{{ $packagingType->id }}"
                                data-packaging-type-name="{{ e($packagingType->packaging_type) }}"
                                data-packaging-type-user="{{ e($packagingType->user?->name ?? '-') }}">
                                <td class="text-center font-semibold">{{ $loop->iteration }}</td>
                                <td>{{ $packagingType->packaging_type }}</td>
                                <td>{{ $packagingType->user?->name ?? '-' }}</td>
                                <td class="text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" class="open-edit-packaging-type-modal">
                                            <img src="{{ asset('assets/icons/dashboard/edit.svg') }}" alt="Edit">
                                        </button>
                                        <button type="button" class="open-delete-packaging-type-modal">
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

    <div id="createPackagingTypeModal"
        class="fixed inset-0 z-50 hidden items-center justify-center p-4 backdrop-blur-[1px]"
        style="background-color: rgba(31, 17, 48, 0.48);">
        <div class="w-full overflow-hidden border border-[#ddd2ef] bg-white p-8 shadow-[0_12px_28px_rgba(60,28,94,0.2)]"
            style="max-width: 560px; border-radius: 22px;">
            <h2 class="text-center text-[24px] font-semibold text-[#3C1C5E]">Tambah Jenis Kemasan</h2>

            <form id="createPackagingTypeForm" class="mt-6 space-y-4">
                <div>
                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]"
                        for="packagingTypeName">Jenis Kemasan</label>
                    <input id="packagingTypeName" name="packaging_type" type="text" required
                        class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none placeholder:text-[#b5a3ca] focus:border-[#6B3E93]"
                        placeholder="Masukkan Jenis Kemasan">
                </div>

                <div class="grid grid-cols-2 gap-4 pt-4">
                    <button id="cancelCreatePackagingType" type="button"
                        class="h-10 rounded-lg border border-[#5A2F7E] bg-white text-[15px] font-semibold text-[#4B1F74] transition hover:bg-[#f8f4ff]">
                        Batal
                    </button>
                    <button id="submitCreatePackagingType" type="submit"
                        class="h-10 rounded-lg bg-[#5A2F7E] text-[15px] font-semibold text-white transition hover:bg-[#4B1F74]">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="editPackagingTypeModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 backdrop-blur-[1px]"
        style="background-color: rgba(31, 17, 48, 0.48);">
        <div class="w-full overflow-hidden border border-[#ddd2ef] bg-white p-8 shadow-[0_12px_28px_rgba(60,28,94,0.2)]"
            style="max-width: 560px; border-radius: 22px;">
            <h2 class="text-center text-[24px] font-semibold text-[#3C1C5E]">Edit Jenis Kemasan</h2>

            <form id="editPackagingTypeForm" class="mt-6 space-y-4">
                <input id="editPackagingTypeId" type="hidden">

                <div>
                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]"
                        for="editPackagingTypeName">Jenis Kemasan</label>
                    <input id="editPackagingTypeName" name="packaging_type" type="text" required
                        class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none placeholder:text-[#b5a3ca] focus:border-[#6B3E93]"
                        placeholder="Masukkan Jenis Kemasan">
                </div>

                <div class="grid grid-cols-2 gap-4 pt-4">
                    <button id="cancelEditPackagingType" type="button"
                        class="h-10 rounded-lg border border-[#5A2F7E] bg-white text-[15px] font-semibold text-[#4B1F74] transition hover:bg-[#f8f4ff]">
                        Batal
                    </button>
                    <button id="submitEditPackagingType" type="submit"
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
                selector: '#packagingTypesTable',
                pageLength: 10,
                actionContainerSelector: '#packagingTypesAddAction',
            });
        });

        (() => {
            const createModal = document.getElementById('createPackagingTypeModal');
            const editModal = document.getElementById('editPackagingTypeModal');
            const openCreateButton = document.getElementById('openCreatePackagingTypeModal');
            const cancelCreateButton = document.getElementById('cancelCreatePackagingType');
            const cancelEditButton = document.getElementById('cancelEditPackagingType');
            const createForm = document.getElementById('createPackagingTypeForm');
            const editForm = document.getElementById('editPackagingTypeForm');
            const editId = document.getElementById('editPackagingTypeId');
            const submitCreateButton = document.getElementById('submitCreatePackagingType');
            const submitEditButton = document.getElementById('submitEditPackagingType');

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

            const normalizeType = (value) => String(value || '').trim();

            const openEditModal = (packagingType) => {
                editId.value = packagingType.id || '';
                document.getElementById('editPackagingTypeName').value = packagingType.name || '';
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

            createModal.addEventListener('click', (event) => {
                if (event.target === createModal) closeCreateModal();
            });

            editModal.addEventListener('click', (event) => {
                if (event.target === editModal) closeEditModal();
            });

            document.querySelectorAll('.open-edit-packaging-type-modal').forEach((button) => {
                button.addEventListener('click', () => {
                    const row = button.closest('tr');
                    if (!row) return;

                    openEditModal({
                        id: row.dataset.packagingTypeId,
                        name: row.dataset.packagingTypeName,
                    });
                });
            });

            createForm.addEventListener('submit', async (event) => {
                event.preventDefault();

                const packagingType = normalizeType(document.getElementById('packagingTypeName').value);
                if (!packagingType) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data belum lengkap',
                        text: 'Jenis kemasan wajib diisi.',
                    });
                    return;
                }

                submitCreateButton.disabled = true;
                submitCreateButton.textContent = 'Menyimpan...';

                try {
                    const response = await fetch('/api/packaging-type/store', {
                        method: 'POST',
                        headers: {
                            Accept: 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            packaging_type: packagingType,
                        }),
                    });

                    const data = await response.json();
                    if (!response.ok) {
                        const errors = data?.errors ? Object.values(data.errors).flat() : [];
                        const message = errors.length > 0 ? errors[0] : (data?.message ||
                            'Gagal menambah jenis kemasan.');
                        throw new Error(message);
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data?.message || 'Jenis kemasan berhasil ditambahkan.',
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
                const packagingType = normalizeType(document.getElementById('editPackagingTypeName').value);

                if (!id || !packagingType) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data belum lengkap',
                        text: 'Jenis kemasan wajib diisi.',
                    });
                    return;
                }

                submitEditButton.disabled = true;
                submitEditButton.textContent = 'Menyimpan...';

                try {
                    const response = await fetch(`/api/packaging-type/update/${id}`, {
                        method: 'POST',
                        headers: {
                            Accept: 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            packaging_type: packagingType,
                        }),
                    });

                    const data = await response.json();
                    if (!response.ok) {
                        const errors = data?.errors ? Object.values(data.errors).flat() : [];
                        const message = errors.length > 0 ? errors[0] : (data?.message ||
                            'Gagal memperbarui jenis kemasan.');
                        throw new Error(message);
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data?.message || 'Jenis kemasan berhasil diperbarui.',
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
                const deleteButton = event.target.closest('.open-delete-packaging-type-modal');
                if (!deleteButton) return;

                const row = deleteButton.closest('tr');
                if (!row) return;

                const id = row.dataset.packagingTypeId;
                const packagingTypeName = row.dataset.packagingTypeName || 'jenis kemasan ini';
                if (!id) return;

                const result = await Swal.fire({
                    icon: 'warning',
                    title: 'Hapus data jenis kemasan?',
                    text: `Data "${packagingTypeName}" akan dihapus permanen.`,
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33',
                });

                if (!result.isConfirmed) return;

                deleteButton.disabled = true;

                try {
                    const response = await fetch(`/api/packaging-type/delete/${id}`, {
                        method: 'DELETE',
                        headers: {
                            Accept: 'application/json',
                        },
                    });

                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) {
                        throw new Error(data?.message || 'Gagal menghapus jenis kemasan.');
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data?.message || 'Jenis kemasan berhasil dihapus.',
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
