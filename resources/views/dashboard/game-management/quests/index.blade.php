@extends('dashboard.layouts.app', [
    'title' => 'Daftar Soal',
])

@section('content')
    <section class="space-y-6 p-2">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard.game-management.index') }}">
                    <img src="{{ asset('assets/icons/dashboard/back.svg') }}" alt="Kembali">
                </a>
                <h1 class="text-[28px] font-semibold leading-none text-[#3C1C5E]">Daftar Soal</h1>
            </div>

            <p class="mt-3 text-[18px] text-[#4F3970]">
                Halaman ini untuk menampilkan daftar soal yang digunakan pada permainan.
            </p>
        </div>

        <article class="rounded-2xl border border-[#ddd2ef] bg-white p-8 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
            <div id="questsAddAction" class="hidden">
                <button id="openCreateQuestPage" type="button"
                    class="inline-flex rounded-lg bg-[#5A2F7E] px-5 py-2 text-[14px] font-semibold text-white transition hover:bg-[#4B1F74]">
                    Tambah Data
                </button>
            </div>

            <div class="overflow-x-auto">
                <table id="questsTable" class="dashboard-datatable display w-full">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Soal</th>
                            <th>Tipe Soal</th>
                            <th>Tipe Permainan</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($quests as $quest)
                            <tr data-quest-id="{{ $quest->id }}"
                                data-quest-name="{{ Str::limit(e($quest->prompt), 50) }}">
                                <td class="text-center font-semibold">{{ $loop->iteration }}</td>
                                <td class="max-w-[300px] truncate">
                                    {{ $quest->prompt }}
                                </td>
                                <td>{{ $quest->type === 'text' ? 'Jawaban Singkat' : 'Pilihan Ganda'}}</td>
                                <td>{{ $quest->game_type === 'quiz' ? 'Kuis' : 'Tebak Gambar'}}</td>
                                <td class="text-center">
                                    @if ($quest->is_active)
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
                                        <a
                                            href="{{ route('dashboard.game-management.quests.show', ['id' => $quest->id]) }}">
                                            <img src="{{ asset('assets/icons/dashboard/show.svg') }}" alt="Lihat">
                                        </a>
                                        <a
                                            href="{{ route('dashboard.game-management.quests.edit', ['id' => $quest->id]) }}">
                                            <img src="{{ asset('assets/icons/dashboard/edit.svg') }}" alt="Ubah">
                                        </a>
                                        <button type="button" class="open-delete-quest">
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
                selector: '#questsTable',
                pageLength: 10,
                actionContainerSelector: '#questsAddAction',
                autowidth: false,
            });

            $('#openCreateQuestPage').on('click', function() {
                window.location.href = "{{ route('dashboard.game-management.quests.create') }}";
            });
        });

        (() => {
            document.addEventListener('click', async (event) => {
                const deleteButton = event.target.closest('.open-delete-quest');
                if (!deleteButton) return;

                const row = deleteButton.closest('tr');
                if (!row) return;

                const questId = row.dataset.questId;
                const questName = row.dataset.questName || 'soal ini';
                if (!questId) return;

                const result = await Swal.fire({
                    icon: 'warning',
                    title: 'Hapus data soal?',
                    text: `Data "${questName}" akan dihapus permanen.`,
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33',
                });

                if (!result.isConfirmed) return;

                deleteButton.disabled = true;

                try {
                    const response = await fetch(`/api/quest/delete/${questId}`, {
                        method: 'DELETE',
                        headers: {
                            Accept: 'application/json',
                        },
                    });

                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) {
                        throw new Error(data?.message || 'Gagal menghapus data soal.');
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data?.message || 'Data soal berhasil dihapus.',
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
