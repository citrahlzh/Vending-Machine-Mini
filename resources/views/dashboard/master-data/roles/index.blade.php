@extends('dashboard.layouts.app', [
    'title' => 'Role Pengguna',
])

@section('content')
    <section class="space-y-6 p-2">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard.master-data.index') }}">
                    <img src="{{ asset('assets/icons/dashboard/back.svg') }}" alt="Kembali">
                </a>
                <h1 class="text-[28px] font-semibold leading-none text-[#3C1C5E]">Role Pengguna</h1>
            </div>

            <p class="mt-3 text-[18px] text-[#4F3970]">
                Halaman ini untuk menampilkan daftar role pengguna.
            </p>
        </div>

        <article class="rounded-2xl border border-[#ddd2ef] bg-white p-8 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
            <div id="rolesAddAction" class="hidden">
                <button id="openCreateRoleModal" type="button"
                    class="rounded-lg bg-[#5A2F7E] px-5 py-2 text-[14px] font-semibold text-white transition hover:bg-[#4B1F74]">
                    Tambah Data
                </button>
            </div>

            <div class="overflow-x-auto">
                <table id="rolesTable" class="dashboard-datatable display w-full">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Nama Role</th>
                            <th>Kode Role</th>
                            <th class="text-center">Jumlah Pengguna</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($roles as $role)
                            <tr data-role-id="{{ $role->id }}" data-role-name="{{ e($role->name) }}"
                                data-role-slug="{{ e($role->slug) }}"
                                data-role-description="{{ e($role->description ?? '') }}"
                                data-role-active="{{ $role->is_active ? '1' : '0' }}"
                                data-role-users-count="{{ $role->users_count }}">
                                <td class="text-center font-semibold">{{ $loop->iteration }}</td>
                                <td>{{ $role->name }}</td>
                                <td>{{ $role->slug }}</td>
                                <td class="text-center">{{ $role->users_count }}</td>
                                <td class="text-center">
                                    @if ($role->is_active)
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
                                        <button type="button" class="open-edit-role-modal">
                                            <img src="{{ asset('assets/icons/dashboard/edit.svg') }}" alt="Ubah">
                                        </button>
                                        <button type="button" class="open-delete-role-modal">
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

    <div id="createRoleModal"
        class="fixed inset-0 z-50 hidden items-center justify-center p-4 backdrop-blur-[1px]"
        style="background-color: rgba(31, 17, 48, 0.48);">
        <div class="w-full overflow-hidden border border-[#ddd2ef] bg-white p-8 shadow-[0_12px_28px_rgba(60,28,94,0.2)]"
            style="max-width: 560px; border-radius: 22px;">
            <h2 class="text-center text-[24px] font-semibold text-[#3C1C5E]">Tambah Role</h2>

            <form id="createRoleForm" class="mt-6 space-y-4">
                <div>
                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]" for="roleName">Nama Role</label>
                    <input id="roleName" name="name" type="text" required
                        class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none placeholder:text-[#b5a3ca] focus:border-[#6B3E93]"
                        placeholder="Masukkan nama role">
                </div>

                <div>
                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]"
                        for="roleSlug">Kode Role</label>
                    <input id="roleSlug" name="slug" type="text"
                        class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none placeholder:text-[#b5a3ca] focus:border-[#6B3E93]"
                        placeholder="Contoh: admin, staff">
                </div>

                <div>
                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]"
                        for="roleDescription">Deskripsi</label>
                    <input id="roleDescription" name="description" type="text"
                        class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none placeholder:text-[#b5a3ca] focus:border-[#6B3E93]"
                        placeholder="Tambahkan deskripsi role (opsional)">
                </div>

                <div>
                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]"
                        for="roleStatus">Status</label>
                    <select id="roleStatus" name="is_active" required
                        class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">
                        <option value="">Pilih Status</option>
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-4">
                    <button id="cancelCreateRole" type="button"
                        class="h-10 rounded-lg border border-[#5A2F7E] bg-white text-[15px] font-semibold text-[#4B1F74] transition hover:bg-[#f8f4ff]">
                        Batal
                    </button>
                    <button id="submitCreateRole" type="submit"
                        class="h-10 rounded-lg bg-[#5A2F7E] text-[15px] font-semibold text-white transition hover:bg-[#4B1F74]">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="editRoleModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 backdrop-blur-[1px]"
        style="background-color: rgba(31, 17, 48, 0.48);">
        <div class="w-full overflow-hidden border border-[#ddd2ef] bg-white p-8 shadow-[0_12px_28px_rgba(60,28,94,0.2)]"
            style="max-width: 560px; border-radius: 22px;">
            <h2 class="text-center text-[24px] font-semibold text-[#3C1C5E]">Ubah Role</h2>

            <form id="editRoleForm" class="mt-6 space-y-4">
                <input id="editRoleId" type="hidden">

                <div>
                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]"
                        for="editRoleName">Nama Role</label>
                    <input id="editRoleName" name="name" type="text" required
                        class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none placeholder:text-[#b5a3ca] focus:border-[#6B3E93]"
                        placeholder="Masukkan nama role">
                </div>

                <div>
                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]"
                        for="editRoleSlug">Kode Role</label>
                    <input id="editRoleSlug" name="slug" type="text"
                        class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none placeholder:text-[#b5a3ca] focus:border-[#6B3E93]"
                        placeholder="Contoh: admin, staff">
                </div>

                <div>
                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]"
                        for="editRoleDescription">Deskripsi</label>
                    <input id="editRoleDescription" name="description" type="text"
                        class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none placeholder:text-[#b5a3ca] focus:border-[#6B3E93]"
                        placeholder="Tambahkan deskripsi role (opsional)">
                </div>

                <div>
                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]"
                        for="editRoleStatus">Status</label>
                    <select id="editRoleStatus" name="is_active" required
                        class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">
                        <option value="1">Aktif</option>
                        <option value="0">Tidak Aktif</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-4">
                    <button id="cancelEditRole" type="button"
                        class="h-10 rounded-lg border border-[#5A2F7E] bg-white text-[15px] font-semibold text-[#4B1F74] transition hover:bg-[#f8f4ff]">
                        Batal
                    </button>
                    <button id="submitEditRole" type="submit"
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
                selector: '#rolesTable',
                pageLength: 10,
                actionContainerSelector: '#rolesAddAction',
            });
        });

        (() => {
            const createModal = document.getElementById('createRoleModal');
            const editModal = document.getElementById('editRoleModal');
            const openCreateButton = document.getElementById('openCreateRoleModal');
            const cancelCreateButton = document.getElementById('cancelCreateRole');
            const cancelEditButton = document.getElementById('cancelEditRole');
            const createForm = document.getElementById('createRoleForm');
            const editForm = document.getElementById('editRoleForm');
            const editId = document.getElementById('editRoleId');
            const submitCreateButton = document.getElementById('submitCreateRole');
            const submitEditButton = document.getElementById('submitEditRole');

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

            const normalizeValue = (value) => String(value || '').trim();

            const openEditModal = (role) => {
                editId.value = role.id || '';
                document.getElementById('editRoleName').value = role.name || '';
                document.getElementById('editRoleSlug').value = role.slug || '';
                document.getElementById('editRoleDescription').value = role.description || '';
                document.getElementById('editRoleStatus').value = role.isActive || '0';
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

            document.querySelectorAll('.open-edit-role-modal').forEach((button) => {
                button.addEventListener('click', () => {
                    const row = button.closest('tr');
                    if (!row) return;

                    const usageCount = Number(row.dataset.roleUsersCount || 0);
                    if (usageCount > 0) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Role sedang digunakan',
                            text: `Role ini dipakai oleh ${usageCount} pengguna.`
                        });
                    }

                    openEditModal({
                        id: row.dataset.roleId,
                        name: row.dataset.roleName,
                        slug: row.dataset.roleSlug,
                        description: row.dataset.roleDescription,
                        isActive: row.dataset.roleActive,
                    });
                });
            });

            createForm.addEventListener('submit', async (event) => {
                event.preventDefault();

                const name = normalizeValue(document.getElementById('roleName').value);
                const slug = normalizeValue(document.getElementById('roleSlug').value);
                const description = normalizeValue(document.getElementById('roleDescription').value);
                const isActiveValue = document.getElementById('roleStatus').value;

                if (!name || isActiveValue === '') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data belum lengkap',
                        text: 'Nama role dan status wajib diisi.',
                    });
                    return;
                }

                submitCreateButton.disabled = true;
                submitCreateButton.textContent = 'Menyimpan...';

                try {
                    const response = await fetch('/api/role/store', {
                        method: 'POST',
                        headers: {
                            Accept: 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            name,
                            slug: slug || null,
                            description: description || null,
                            is_active: isActiveValue === '1',
                        }),
                    });

                    const data = await response.json();
                    if (!response.ok) {
                        const errors = data?.errors ? Object.values(data.errors).flat() : [];
                        const message = errors.length > 0 ? errors[0] : (data?.message ||
                            'Gagal menambah role.');
                        throw new Error(message);
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data?.message || 'Role berhasil ditambahkan.',
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
                const name = normalizeValue(document.getElementById('editRoleName').value);
                const slug = normalizeValue(document.getElementById('editRoleSlug').value);
                const description = normalizeValue(document.getElementById('editRoleDescription').value);
                const isActiveValue = document.getElementById('editRoleStatus').value;

                if (!id || !name || isActiveValue === '') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data belum lengkap',
                        text: 'Nama role dan status wajib diisi.',
                    });
                    return;
                }

                submitEditButton.disabled = true;
                submitEditButton.textContent = 'Menyimpan...';

                try {
                    const response = await fetch(`/api/role/update/${id}`, {
                        method: 'POST',
                        headers: {
                            Accept: 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            name,
                            slug: slug || null,
                            description: description || null,
                            is_active: isActiveValue === '1',
                        }),
                    });

                    const data = await response.json();
                    if (!response.ok) {
                        const errors = data?.errors ? Object.values(data.errors).flat() : [];
                        const message = errors.length > 0 ? errors[0] : (data?.message ||
                            'Gagal memperbarui role.');
                        throw new Error(message);
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data?.message || 'Role berhasil diperbarui.',
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
                const deleteButton = event.target.closest('.open-delete-role-modal');
                if (!deleteButton) return;

                const row = deleteButton.closest('tr');
                if (!row) return;

                const id = row.dataset.roleId;
                const roleName = row.dataset.roleName || 'role ini';
                const usageCount = Number(row.dataset.roleUsersCount || 0);
                if (!id) return;

                if (usageCount > 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Role sedang digunakan',
                        text: `Role "${roleName}" dipakai oleh ${usageCount} pengguna.`,
                    });
                    return;
                }

                const result = await Swal.fire({
                    icon: 'warning',
                    title: 'Hapus data role?',
                    text: `Data "${roleName}" akan dihapus permanen.`,
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33',
                });

                if (!result.isConfirmed) return;

                deleteButton.disabled = true;

                try {
                    const response = await fetch(`/api/role/delete/${id}`, {
                        method: 'DELETE',
                        headers: {
                            Accept: 'application/json',
                        },
                    });

                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) {
                        throw new Error(data?.message || 'Gagal menghapus role.');
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data?.message || 'Role berhasil dihapus.',
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
