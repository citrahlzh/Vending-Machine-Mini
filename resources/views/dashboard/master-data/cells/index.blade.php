@extends('dashboard.layouts.app', [
    'title' => 'Sel Produk',
])

@section('content')
    <section class="space-y-6 p-2">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard.master-data.index') }}">
                    <img src="{{ asset('assets/icons/dashboard/back.svg') }}" alt="Kembali">
                </a>
                <h1 class="text-[28px] font-semibold leading-none text-[#3C1C5E]">Sel Produk</h1>
            </div>

            <p class="mt-3 text-[18px] text-[#4F3970]">
                Halaman ini untuk menampilkan daftar sel produk di Vending Machine.
            </p>
        </div>

        <article class="rounded-2xl border border-[#ddd2ef] bg-white p-8 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
            <div id="cellsAddAction" class="hidden">
                <button id="openCreateCellModal" type="button"
                    class="rounded-lg bg-[#5A2F7E] px-5 py-2 text-[14px] font-semibold text-white transition hover:bg-[#4B1F74]">
                    Tambah Data
                </button>
            </div>

            <div class="overflow-x-auto">
                <table id="cellsTable" class="dashboard-datatable display w-full">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Kode</th>
                            <th>Baris</th>
                            <th>Kolom</th>
                            <th class="text-center">Kapasitas</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cells as $cell)
                            <tr data-cell-id="{{ $cell->id }}" data-cell-code="{{ e($cell->code) }}"
                                data-cell-row="{{ e($cell->row) }}" data-cell-column="{{ e($cell->column) }}"
                                data-cell-capacity="{{ $cell->capacity }}" data-cell-qty-current="{{ $cell->qty_current }}">
                                <td class="text-center font-semibold">{{ $loop->iteration }}</td>
                                <td>{{ $cell->code }}</td>
                                <td>{{ $cell->row }}</td>
                                <td>{{ $cell->column }}</td>
                                <td class="text-center">{{ $cell->capacity }}</td>
                                <td class="text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" class="open-edit-cell-modal">
                                            <img src="{{ asset('assets/icons/dashboard/edit.svg') }}" alt="Edit">
                                        </button>
                                        <button type="button" class="open-delete-cell-modal">
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

    <div id="createCellModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 backdrop-blur-[1px]"
        style="background-color: rgba(31, 17, 48, 0.48);">
        <div class="w-full overflow-hidden border border-[#ddd2ef] bg-white p-8 shadow-[0_12px_28px_rgba(60,28,94,0.2)]"
            style="max-width: 560px; border-radius: 22px;">
            <h2 class="text-center text-[24px] font-semibold text-[#3C1C5E]">Tambah Sel Produk</h2>

            <form id="createCellForm" class="mt-6 space-y-4">
                <div>
                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]" for="cellCode">Kode Sel</label>
                    <input id="cellCode" name="code" type="text" required
                        class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none placeholder:text-[#b5a3ca] focus:border-[#6B3E93]"
                        placeholder="Masukkan Kode Sel">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]" for="cellRow">Baris ke-</label>
                        <input id="cellRow" name="row" type="text" required
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none placeholder:text-[#b5a3ca] focus:border-[#6B3E93]"
                            placeholder="Masukkan Baris">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]"
                            for="cellColumn">Kolom ke-</label>
                        <input id="cellColumn" name="column" type="text" required
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none placeholder:text-[#b5a3ca] focus:border-[#6B3E93]"
                            placeholder="Masukkan Kolom">
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]" for="cellCapacity">Kapasitas
                        Sel</label>
                    <input id="cellCapacity" name="capacity" type="number" min="1" required
                        class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none placeholder:text-[#b5a3ca] focus:border-[#6B3E93]"
                        placeholder="Masukkan Kapasitas Sel">
                </div>

                <div class="grid grid-cols-2 gap-4 pt-4">
                    <button id="cancelCreateCell" type="button"
                        class="h-10 rounded-lg border border-[#5A2F7E] bg-white text-[15px] font-semibold text-[#4B1F74] transition hover:bg-[#f8f4ff]">
                        Batal
                    </button>
                    <button id="submitCreateCell" type="submit"
                        class="h-10 rounded-lg bg-[#5A2F7E] text-[15px] font-semibold text-white transition hover:bg-[#4B1F74]">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="editCellModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 backdrop-blur-[1px]"
        style="background-color: rgba(31, 17, 48, 0.48);">
        <div class="w-full overflow-hidden border border-[#ddd2ef] bg-white p-8 shadow-[0_12px_28px_rgba(60,28,94,0.2)]"
            style="max-width: 560px; border-radius: 22px;">
            <h2 class="text-center text-[24px] font-semibold text-[#3C1C5E]">Edit Sel Produk</h2>

            <form id="editCellForm" class="mt-6 space-y-4">
                <input id="editCellId" type="hidden">
                <input id="editCellQtyCurrent" type="hidden">

                <div>
                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]" for="editCellCode">Kode
                        Sel</label>
                    <input id="editCellCode" name="code" type="text" required
                        class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none placeholder:text-[#b5a3ca] focus:border-[#6B3E93]"
                        placeholder="Masukkan Kode Sel">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]" for="editCellRow">Baris
                            ke-</label>
                        <input id="editCellRow" name="row" type="text" required
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none placeholder:text-[#b5a3ca] focus:border-[#6B3E93]"
                            placeholder="Masukkan Baris">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]" for="editCellColumn">Kolom
                            ke-</label>
                        <input id="editCellColumn" name="column" type="text" required
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none placeholder:text-[#b5a3ca] focus:border-[#6B3E93]"
                            placeholder="Masukkan Kolom">
                    </div>
                </div>

                <div>
                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]" for="editCellCapacity">Kapasitas
                        Sel</label>
                    <input id="editCellCapacity" name="capacity" type="number" min="1" required
                        class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none placeholder:text-[#b5a3ca] focus:border-[#6B3E93]"
                        placeholder="Masukkan Kapasitas Sel">
                </div>

                <div class="grid grid-cols-2 gap-4 pt-4">
                    <button id="cancelEditCell" type="button"
                        class="h-10 rounded-lg border border-[#5A2F7E] bg-white text-[15px] font-semibold text-[#4B1F74] transition hover:bg-[#f8f4ff]">
                        Batal
                    </button>
                    <button id="submitEditCell" type="submit"
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
                selector: '#cellsTable',
                pageLength: 10,
                actionContainerSelector: '#cellsAddAction',
            });
        });

        (() => {
            const createModal = document.getElementById('createCellModal');
            const editModal = document.getElementById('editCellModal');
            const openCreateButton = document.getElementById('openCreateCellModal');
            const cancelCreateButton = document.getElementById('cancelCreateCell');
            const cancelEditButton = document.getElementById('cancelEditCell');
            const createForm = document.getElementById('createCellForm');
            const editForm = document.getElementById('editCellForm');
            const editCellId = document.getElementById('editCellId');
            const editCellQtyCurrent = document.getElementById('editCellQtyCurrent');
            const submitCreateButton = document.getElementById('submitCreateCell');
            const submitEditButton = document.getElementById('submitEditCell');

            if (!createModal || !editModal || !openCreateButton || !cancelCreateButton || !cancelEditButton || !
                createForm || !editForm || !editCellId || !editCellQtyCurrent || !submitCreateButton || !
                submitEditButton) return;

            const openModal = (modal) => {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            };

            const closeModal = (modal) => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            };

            const toUpperTrim = (value) => String(value || '').trim().toUpperCase();

            const openEditModal = (cell) => {
                editCellId.value = cell.id || '';
                editCellQtyCurrent.value = cell.qtyCurrent || '';
                document.getElementById('editCellCode').value = cell.code || '';
                document.getElementById('editCellRow').value = cell.row || '';
                document.getElementById('editCellColumn').value = cell.column || '';
                document.getElementById('editCellCapacity').value = cell.capacity || '';
                openModal(editModal);
            };

            const closeCreateModal = () => {
                closeModal(createModal);
                createForm.reset();
            };

            const closeEditModal = () => {
                closeModal(editModal);
                editForm.reset();
                editCellId.value = '';
                editCellQtyCurrent.value = '';
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

            document.querySelectorAll('.open-edit-cell-modal').forEach((button) => {
                button.addEventListener('click', () => {
                    const row = button.closest('tr');
                    if (!row) return;

                    openEditModal({
                        id: row.dataset.cellId,
                        code: row.dataset.cellCode,
                        row: row.dataset.cellRow,
                        column: row.dataset.cellColumn,
                        capacity: row.dataset.cellCapacity,
                        qtyCurrent: row.dataset.cellQtyCurrent,
                    });
                });
            });

            createForm.addEventListener('submit', async (event) => {
                event.preventDefault();

                const code = toUpperTrim(document.getElementById('cellCode').value);
                const rowValue = toUpperTrim(document.getElementById('cellRow').value);
                const column = toUpperTrim(document.getElementById('cellColumn').value);
                const capacity = Number(document.getElementById('cellCapacity').value);

                if (!code || !rowValue || !column || !capacity || capacity < 1) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data belum lengkap',
                        text: 'Mohon isi data sel dengan benar.',
                    });
                    return;
                }

                submitCreateButton.disabled = true;
                submitCreateButton.textContent = 'Menyimpan...';

                try {
                    const response = await fetch('/api/cell/store', {
                        method: 'POST',
                        headers: {
                            Accept: 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            code,
                            row: rowValue,
                            column,
                            capacity,
                        }),
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        const errors = data?.errors ? Object.values(data.errors).flat() : [];
                        const message = errors.length > 0 ? errors[0] : (data?.message ||
                            'Gagal menambah data sel.');
                        throw new Error(message);
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data?.message || 'Data sel berhasil ditambahkan.',
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

                const id = editCellId.value;
                const qtyCurrent = Number(editCellQtyCurrent.value || 0);
                const code = toUpperTrim(document.getElementById('editCellCode').value);
                const rowValue = toUpperTrim(document.getElementById('editCellRow').value);
                const column = toUpperTrim(document.getElementById('editCellColumn').value);
                const capacity = Number(document.getElementById('editCellCapacity').value);

                if (!id || !code || !rowValue || !column || !capacity || capacity < 1) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data belum lengkap',
                        text: 'Mohon isi data sel dengan benar.',
                    });
                    return;
                }

                if (capacity < qtyCurrent) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Kapasitas tidak valid',
                        text: 'Kapasitas tidak boleh lebih kecil dari stok saat ini.',
                    });
                    return;
                }

                submitEditButton.disabled = true;
                submitEditButton.textContent = 'Menyimpan...';

                try {
                    const response = await fetch(`/api/cell/update/${id}`, {
                        method: 'POST',
                        headers: {
                            Accept: 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            code,
                            row: rowValue,
                            column,
                            capacity,
                        }),
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        const errors = data?.errors ? Object.values(data.errors).flat() : [];
                        const message = errors.length > 0 ? errors[0] : (data?.message ||
                            'Gagal memperbarui data sel.');
                        throw new Error(message);
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data?.message || 'Data sel berhasil diperbarui.',
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
                const deleteButton = event.target.closest('.open-delete-cell-modal');
                if (!deleteButton) return;

                const row = deleteButton.closest('tr');
                if (!row) return;

                const id = row.dataset.cellId;
                const code = row.dataset.cellCode || 'sel ini';
                if (!id) return;

                const result = await Swal.fire({
                    icon: 'warning',
                    title: 'Hapus data sel?',
                    text: `Data "${code}" akan dihapus permanen.`,
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33',
                });

                if (!result.isConfirmed) return;

                deleteButton.disabled = true;

                try {
                    const response = await fetch(`/api/cell/delete/${id}`, {
                        method: 'DELETE',
                        headers: {
                            Accept: 'application/json',
                        },
                    });

                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) {
                        throw new Error(data?.message || 'Gagal menghapus data sel.');
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data?.message || 'Data sel berhasil dihapus.',
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
