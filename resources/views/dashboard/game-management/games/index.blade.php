@extends('dashboard.layouts.app', [
    'title' => 'Daftar Permainan',
])

@section('content')
    <section class="space-y-6 p-2">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard.game-management.index') }}">
                    <img src="{{ asset('assets/icons/dashboard/back.svg') }}" alt="Kembali">
                </a>
                <h1 class="text-[28px] font-semibold leading-none text-[#3C1C5E]">Daftar Permainan</h1>
            </div>

            <p class="mt-3 text-[18px] text-[#4F3970]">
                Halaman ini untuk menampilkan daftar permainan yang ada pada Vending Machine.
            </p>
        </div>

        <article class="rounded-2xl border border-[#ddd2ef] bg-white p-8 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
            <div id="gamesAddAction" class="hidden">
                <button id="openCreateGamePage" type="button"
                    class="inline-flex rounded-lg bg-[#5A2F7E] px-5 py-2 text-[14px] font-semibold text-white transition hover:bg-[#4B1F74]">
                    Tambah Data
                </button>
            </div>

            <div class="overflow-x-auto">
                <table id="gamesTable" class="dashboard-datatable display w-full">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Nama</th>
                            <th>Tipe</th>
                            <th>Masa Aktif</th>
                            <th>Jumlah Soal / Segmen</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($games as $game)
                            <tr data-game-id="{{ $game->id }}" data-game-name="{{ e($game->name) }}">
                                <td class="text-center font-semibold">{{ $loop->iteration }}</td>
                                <td>{{ $game->name }}</td>
                                <td>{{ $game->type }}</td>
                                <td>{{ optional($game->start_date)->format('d/m/Y') }} -
                                    {{ optional($game->end_date)->format('d/m/Y') }}</td>
                                <td>
                                    @if ($game->type === 'spin')
                                        {{ $game->spin_segments_count }}
                                    @else
                                        {{ $game->quests_count }}
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($game->is_active)
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
                                        <a href="{{ route('dashboard.game-management.games.show', ['id' => $game->id]) }}">
                                            <img src="{{ asset('assets/icons/dashboard/show.svg') }}" alt="Lihat">
                                        </a>
                                        <a href="{{ route('dashboard.game-management.games.edit', ['id' => $game->id]) }}">
                                            <img src="{{ asset('assets/icons/dashboard/edit.svg') }}" alt="Ubah">
                                        </a>
                                        <button type="button" class="open-delete-game">
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

    <div id="createGameModal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center">

        <div class="bg-white rounded-xl p-6 w-[400px]">

            <div class="flex justify-between">
                <h2 class="text-lg font-semibold mb-4">
                    Pilih Tipe Game
                </h2>

                <button id="closeGameModal" class="absolute top-3 right-4 text-gray-500">
                    ✕
                </button>
            </div>


            <div class="space-y-3">

                <button class="game-type-btn w-full border p-3 rounded" data-type="quiz">
                    Quiz
                </button>

                <button class="game-type-btn w-full border p-3 rounded" data-type="guess_image">
                    Tebak Gambar
                </button>

                <button class="game-type-btn w-full border p-3 rounded" data-type="spin">
                    Spin Wheel
                </button>

            </div>

        </div>

    </div>
@endsection

@push('script')
    <script>
        $(function() {
            initDashboardDataTable({
                selector: '#gamesTable',
                pageLength: 10,
                actionContainerSelector: '#gamesAddAction',
                autowidth: false,
            });

            $('#openCreateGamePage').on('click', function() {
                $('#createGameModal').removeClass('hidden');
            });

            $('#closeGameModal').on('click', function() {
                $('#createGameModal').addClass('hidden');
            });

            document.querySelectorAll('.game-type-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const type = this.dataset.type;
                    window.location.href =
                        `/dashboard/game-management/games/create?type=${type}`;
                });
            });
        });

        (() => {
            document.addEventListener('click', async (event) => {
                const deleteButton = event.target.closest('.open-delete-game');
                if (!deleteButton) return;

                const row = deleteButton.closest('tr');
                if (!row) return;

                const gameId = row.dataset.gameId;
                const gameName = row.dataset.gameName || 'permainan ini';
                if (!gameId) return;

                const result = await Swal.fire({
                    icon: 'warning',
                    title: 'Hapus data permainan?',
                    text: `Data "${gameName}" akan dihapus permanen.`,
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33',
                });

                if (!result.isConfirmed) return;

                deleteButton.disabled = true;

                try {
                    const response = await fetch(`/api/game/delete/${gameId}`, {
                        method: 'DELETE',
                        headers: {
                            Accept: 'application/json',
                        },
                    });

                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) {
                        throw new Error(data?.message || 'Gagal menghapus data permainan.');
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data?.message || 'Data permainan berhasil dihapus.',
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
