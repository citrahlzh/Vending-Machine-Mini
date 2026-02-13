@extends('dashboard.layouts.app', [
    'title' => 'Data Pengguna',
])

@section('content')
    <section class="space-y-6 p-2">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard.master-data.index') }}">
                    <img src="{{ asset('assets/icons/dashboard/back.svg') }}" alt="Kembali">
                </a>
                <h1 class="text-[28px] font-semibold leading-none text-[#3C1C5E]">Data Pengguna</h1>
            </div>

            <p class="mt-3 text-[18px] text-[#4F3970]">
                Halaman ini untuk menampilkan daftar pengguna.
            </p>
        </div>

        <article class="rounded-2xl border border-[#ddd2ef] bg-white p-8 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
            <div id="usersAddAction" class="hidden">
                <button id="openCreateUserPage" type="button"
                    class="inline-flex rounded-lg bg-[#5A2F7E] px-5 py-2 text-[14px] font-semibold text-white transition hover:bg-[#4B1F74]">
                    Tambah Data
                </button>
            </div>

            <div class="overflow-x-auto">
                <table id="usersTable" class="dashboard-datatable display w-full">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Nama</th>
                            <th>Nama Pengguna</th>
                            <th>Nomor Telepon</th>
                            <th>Nomor Whatsapp</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr data-user-id="{{ $user->id }}" data-user-name="{{ e($user->name) }}">
                                <td class="text-center font-semibold">{{ $loop->iteration }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->phone_number }}</td>
                                <td>{{ $user->whatsapp_number ?: '-' }}</td>
                                <td class="text-center">
                                    @if ($user->is_active)
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
                                        <a href="{{ route('dashboard.master-data.users.show', ['id' => $user->id]) }}">
                                            <img src="{{ asset('assets/icons/dashboard/show.svg') }}" alt="Lihat">
                                        </a>
                                        <a href="{{ route('dashboard.master-data.users.edit', ['id' => $user->id]) }}">
                                            <img src="{{ asset('assets/icons/dashboard/edit.svg') }}" alt="Edit">
                                        </a>
                                        <button type="button" class="open-delete-user">
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
                selector: '#usersTable',
                pageLength: 10,
                actionContainerSelector: '#usersAddAction',
            });

            $('#openCreateUserPage').on('click', function() {
                window.location.href = "{{ route('dashboard.master-data.users.create') }}";
            });
        });

        (() => {
            document.addEventListener('click', async (event) => {
                const deleteButton = event.target.closest('.open-delete-user');
                if (!deleteButton) return;

                const row = deleteButton.closest('tr');
                if (!row) return;

                const userId = row.dataset.userId;
                const userName = row.dataset.userName || 'pengguna ini';
                if (!userId) return;

                const result = await Swal.fire({
                    icon: 'warning',
                    title: 'Hapus data pengguna?',
                    text: `Data "${userName}" akan dihapus permanen.`,
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33',
                });

                if (!result.isConfirmed) return;

                deleteButton.disabled = true;

                try {
                    const response = await fetch(`/api/user/delete/${userId}`, {
                        method: 'DELETE',
                        headers: {
                            Accept: 'application/json',
                        },
                    });

                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) {
                        throw new Error(data?.message || 'Gagal menghapus data pengguna.');
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data?.message || 'Data pengguna berhasil dihapus.',
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
