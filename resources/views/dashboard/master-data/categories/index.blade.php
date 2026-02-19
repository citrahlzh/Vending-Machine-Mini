@extends('dashboard.layouts.app', [
    'title' => 'Kategori Produk',
])

@section('content')
    <section class="space-y-6 p-2">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard.master-data.index') }}">
                    <img src="{{ asset('assets/icons/dashboard/back.svg') }}" alt="Kembali">
                </a>
                <h1 class="text-[28px] font-semibold leading-none text-[#3C1C5E]">Kategori Produk</h1>
            </div>

            <p class="mt-3 text-[18px] text-[#4F3970]">
                Halaman ini untuk menampilkan daftar kategori produk.
            </p>
        </div>

        <article class="rounded-2xl border border-[#ddd2ef] bg-white p-8 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
            <div id="categoriesAddAction" class="hidden">
                <button id="openCreateCategoryModal" type="button"
                    class="rounded-lg bg-[#5A2F7E] px-5 py-2 text-[14px] font-semibold text-white transition hover:bg-[#4B1F74]">
                    Tambah Data
                </button>
            </div>

            <div class="overflow-x-auto">
                <table id="categoriesTable" class="dashboard-datatable display w-full">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Nama Kategori</th>
                            <th>Dibuat Oleh</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $category)
                            <tr data-category-id="{{ $category->id }}"
                                data-category-name="{{ e($category->category_name) }}"
                                data-category-active="{{ $category->is_active ? '1' : '0' }}"
                                data-category-user="{{ e($category->user?->name ?? '-') }}">
                                <td class="text-center font-semibold">{{ $loop->iteration }}</td>
                                <td>{{ $category->category_name }}</td>
                                <td>{{ $category->user?->name ?? '-' }}</td>
                                <td class="text-center">
                                    @if ($category->is_active)
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
                                        <button type="button" class="open-edit-category-modal">
                                            <img src="{{ asset('assets/icons/dashboard/edit.svg') }}" alt="Edit">
                                        </button>
                                        <button type="button" class="open-delete-category-modal">
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

    <div id="createCategoryModal"
        class="fixed inset-0 z-50 hidden items-center justify-center p-4 backdrop-blur-[1px]"
        style="background-color: rgba(31, 17, 48, 0.48);">
        <div class="w-full overflow-hidden border border-[#ddd2ef] bg-white p-8 shadow-[0_12px_28px_rgba(60,28,94,0.2)]"
            style="max-width: 560px; border-radius: 22px;">
            <h2 class="text-center text-[24px] font-semibold text-[#3C1C5E]">Tambah Kategori</h2>

            <form id="createCategoryForm" class="mt-6 space-y-4">
                <div>
                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]"
                        for="categoryName">Kategori</label>
                    <input id="categoryName" name="category_name" type="text" required
                        class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none placeholder:text-[#b5a3ca] focus:border-[#6B3E93]"
                        placeholder="Masukkan Kategori">
                </div>

                <div>
                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]"
                        for="categoryStatus">Status</label>
                    <select id="categoryStatus" name="is_active" required
                        class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">
                        <option value="">Pilih Status Data</option>
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-4">
                    <button id="cancelCreateCategory" type="button"
                        class="h-10 rounded-lg border border-[#5A2F7E] bg-white text-[15px] font-semibold text-[#4B1F74] transition hover:bg-[#f8f4ff]">
                        Batal
                    </button>
                    <button id="submitCreateCategory" type="submit"
                        class="h-10 rounded-lg bg-[#5A2F7E] text-[15px] font-semibold text-white transition hover:bg-[#4B1F74]">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="editCategoryModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 backdrop-blur-[1px]"
        style="background-color: rgba(31, 17, 48, 0.48);">
        <div class="w-full overflow-hidden border border-[#ddd2ef] bg-white p-8 shadow-[0_12px_28px_rgba(60,28,94,0.2)]"
            style="max-width: 560px; border-radius: 22px;">
            <h2 class="text-center text-[24px] font-semibold text-[#3C1C5E]">Edit Kategori</h2>

            <form id="editCategoryForm" class="mt-6 space-y-4">
                <input id="editCategoryId" type="hidden">

                <div>
                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]"
                        for="editCategoryName">Kategori</label>
                    <input id="editCategoryName" name="category_name" type="text" required
                        class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none placeholder:text-[#b5a3ca] focus:border-[#6B3E93]"
                        placeholder="Masukkan Kategori">
                </div>

                <div>
                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]"
                        for="editCategoryStatus">Status</label>
                    <select id="editCategoryStatus" name="is_active" required
                        class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-4">
                    <button id="cancelEditCategory" type="button"
                        class="h-10 rounded-lg border border-[#5A2F7E] bg-white text-[15px] font-semibold text-[#4B1F74] transition hover:bg-[#f8f4ff]">
                        Batal
                    </button>
                    <button id="submitEditCategory" type="submit"
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
                selector: '#categoriesTable',
                pageLength: 10,
                actionContainerSelector: '#categoriesAddAction',
            });
        });

        (() => {
            const createModal = document.getElementById('createCategoryModal');
            const editModal = document.getElementById('editCategoryModal');
            const openCreateButton = document.getElementById('openCreateCategoryModal');
            const cancelCreateButton = document.getElementById('cancelCreateCategory');
            const cancelEditButton = document.getElementById('cancelEditCategory');
            const createForm = document.getElementById('createCategoryForm');
            const editForm = document.getElementById('editCategoryForm');
            const editId = document.getElementById('editCategoryId');
            const submitCreateButton = document.getElementById('submitCreateCategory');
            const submitEditButton = document.getElementById('submitEditCategory');

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

            const normalizeCategory = (value) => String(value || '').trim();

            const openEditModal = (category) => {
                editId.value = category.id || '';
                document.getElementById('editCategoryName').value = category.name || '';
                document.getElementById('editCategoryStatus').value = category.isActive || '0';
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


            document.querySelectorAll('.open-edit-category-modal').forEach((button) => {
                button.addEventListener('click', () => {
                    const row = button.closest('tr');
                    if (!row) return;

                    openEditModal({
                        id: row.dataset.categoryId,
                        name: row.dataset.categoryName,
                        isActive: row.dataset.categoryActive,
                    });
                });
            });

            createForm.addEventListener('submit', async (event) => {
                event.preventDefault();

                const categoryName = normalizeCategory(document.getElementById('categoryName').value);
                const isActiveValue = document.getElementById('categoryStatus').value;

                if (!categoryName || isActiveValue === '') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data belum lengkap',
                        text: 'Kategori dan status wajib diisi.',
                    });
                    return;
                }

                submitCreateButton.disabled = true;
                submitCreateButton.textContent = 'Menyimpan...';

                try {
                    const response = await fetch('/api/category/store', {
                        method: 'POST',
                        headers: {
                            Accept: 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            category_name: categoryName,
                            is_active: isActiveValue === '1',
                        }),
                    });

                    const data = await response.json();
                    if (!response.ok) {
                        const errors = data?.errors ? Object.values(data.errors).flat() : [];
                        const message = errors.length > 0 ? errors[0] : (data?.message ||
                            'Gagal menambah kategori.');
                        throw new Error(message);
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data?.message || 'Kategori berhasil ditambahkan.',
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
                const categoryName = normalizeCategory(document.getElementById('editCategoryName').value);
                const isActiveValue = document.getElementById('editCategoryStatus').value;

                if (!id || !categoryName || isActiveValue === '') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data belum lengkap',
                        text: 'Kategori dan status wajib diisi.',
                    });
                    return;
                }

                submitEditButton.disabled = true;
                submitEditButton.textContent = 'Menyimpan...';

                try {
                    const response = await fetch(`/api/category/update/${id}`, {
                        method: 'POST',
                        headers: {
                            Accept: 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            category_name: categoryName,
                            is_active: isActiveValue === '1',
                        }),
                    });

                    const data = await response.json();
                    if (!response.ok) {
                        const errors = data?.errors ? Object.values(data.errors).flat() : [];
                        const message = errors.length > 0 ? errors[0] : (data?.message ||
                            'Gagal memperbarui kategori.');
                        throw new Error(message);
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data?.message || 'Kategori berhasil diperbarui.',
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
                const deleteButton = event.target.closest('.open-delete-category-modal');
                if (!deleteButton) return;

                const row = deleteButton.closest('tr');
                if (!row) return;

                const id = row.dataset.categoryId;
                const categoryName = row.dataset.categoryName || 'kategori ini';
                if (!id) return;

                const result = await Swal.fire({
                    icon: 'warning',
                    title: 'Hapus data kategori?',
                    text: `Data "${categoryName}" akan dihapus permanen.`,
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33',
                });

                if (!result.isConfirmed) return;

                deleteButton.disabled = true;

                try {
                    const response = await fetch(`/api/category/delete/${id}`, {
                        method: 'DELETE',
                        headers: {
                            Accept: 'application/json',
                        },
                    });

                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) {
                        throw new Error(data?.message || 'Gagal menghapus kategori.');
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data?.message || 'Kategori berhasil dihapus.',
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
