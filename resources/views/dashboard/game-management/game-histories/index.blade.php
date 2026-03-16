@extends('dashboard.layouts.app', [
    'title' => 'Daftar Riwayat Permainan',
])

@section('content')
    <section class="space-y-6 p-2">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard.game-management.index') }}">
                    <img src="{{ asset('assets/icons/dashboard/back.svg') }}" alt="Kembali">
                </a>
                <h1 class="text-[28px] font-semibold leading-none text-[#3C1C5E]">Daftar Riwayat Permainan</h1>
            </div>

            <p class="mt-3 text-[18px] text-[#4F3970]">
                Halaman ini untuk menampilkan daftar soal yang digunakan pada permainan.
            </p>
        </div>

        <article class="rounded-2xl border border-[#ddd2ef] bg-white p-8 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
            <div id="playsAddAction" class="hidden">
                <button id="openCreatePlayPage" type="button"
                    class="inline-flex rounded-lg bg-[#5A2F7E] px-5 py-2 text-[14px] font-semibold text-white transition hover:bg-[#4B1F74]">
                    Tambah Data
                </button>
            </div>

            <div class="overflow-x-auto">
                <table id="playsTable" class="dashboard-datatable display w-full">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>ID Permainan</th>
                            <th>Permainan</th>
                            <th>Skor</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Status Hadiah</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($plays as $play)
                            @php
                                $playStatusLabel = match ($play->status) {
                                    'started' => 'Sedang Dimainkan',
                                    'finished' => 'Selesai',
                                    'cancelled' => 'Dibatalkan'
                                };

                                $playStatusClass = match ($play->status) {
                                    'started' => 'bg-[#e3efff] text-[#1d4ed8]',
                                    'finished' => 'bg-[#d7f2e1] text-[#17914f]',
                                    'cancelled' => 'bg-[#f1f5f9] text-[#475569]',
                                };

                                $issuedReward = $play->issuedRewards->first();
                                $rewardStatusLabel = $issuedReward
                                    ? match ($issuedReward->status) {
                                        'issued' => 'Hadiah Diberikan',
                                        'redeemed' => 'Hadiah Diklaim',
                                        'expired' => 'Kadaluarsa',
                                        'void' => 'Dibatalkan',
                                        default => 'Tidak Diketahui'
                                    }
                                    : 'Tidak Ada';

                                $rewardStatusClass = $issuedReward
                                    ? match ($issuedReward->status) {
                                        'issued' => 'bg-[#e3efff] text-[#1d4ed8]',
                                        'redeemed' => 'bg-[#d7f2e1] text-[#17914f]',
                                        'expired' => 'bg-[#fff4d6] text-[#b45309]',
                                        'void' => 'bg-[#f1f5f9] text-[#475569]',
                                        default => 'bg-[#f1f5f9] text-[#475569]'
                                    }
                                    : 'bg-[#f1f5f9] text-[#475569]';
                            @endphp
                            <tr data-play-id="{{ $play->id }}" data-play-name="{{ e($play->idempotency_key) }}">
                                <td class="text-center font-semibold">{{ $loop->iteration }}</td>
                                <td>{{ $play->idempotency_key }}</td>
                                <td>{{ $play->game?->name ?? '-' }}</td>
                                <td>{{ $play->score }}</td>
                                <td class="text-center whitespace-nowrap">
                                    <span
                                        class="inline-flex rounded-full px-4 py-1 text-[12px] font-medium {{ $playStatusClass }}">
                                        {{ $playStatusLabel }}
                                    </span>
                                </td>
                                <td class="text-center whitespace-nowrap">
                                    <span
                                        class="inline-flex rounded-full px-4 py-1 text-[12px] font-medium {{ $rewardStatusClass }}">
                                        {{ $rewardStatusLabel }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('dashboard.game-management.game-history.show', ['id' => $play->id]) }}">
                                            <img src="{{ asset('assets/icons/dashboard/show.svg') }}" alt="Lihat">
                                        </a>
                                        {{-- <button type="button" class="open-delete-plays">
                                            <img src="{{ asset('assets/icons/dashboard/delete.svg') }}" alt="Hapus">
                                        </button> --}}
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
                selector: '#playsTable',
                pageLength: 10,
                actionContainerSelector: '#playsAddAction',
            });
        });

        // (() => {
        //     document.addEventListener('click', async (event) => {
        //         const deleteButton = event.target.closest('.open-delete-plays');
        //         if (!deleteButton) return;

        //         const row = deleteButton.closest('tr');
        //         if (!row) return;

        //         const playId = row.dataset.playId;
        //         const playName = row.dataset.playName || 'riwayat permainan ini';
        //         if (!playId) return;

        //         const result = await Swal.fire({
        //             icon: 'warning',
        //             title: 'Hapus data riwayat permainan?',
        //             text: `Data "${playName}" akan dihapus permanen.`,
        //             showCancelButton: true,
        //             confirmButtonText: 'Ya, hapus',
        //             cancelButtonText: 'Batal',
        //             confirmButtonColor: '#d33',
        //         });

        //         if (!result.isConfirmed) return;

        //         deleteButton.disabled = true;

        //         try {
        //             const response = await fetch(`/api/play-histor/delete/${playId}`, {
        //                 method: 'DELETE',
        //                 headers: {
        //                     Accept: 'application/json',
        //                 },
        //             });

        //             const data = await response.json().catch(() => ({}));
        //             if (!response.ok) {
        //                 throw new Error(data?.message || 'Gagal menghapus data riwayat permainan.');
        //             }

        //             Swal.fire({
        //                 icon: 'success',
        //                 title: 'Berhasil',
        //                 text: data?.message || 'Data riwayat permainan berhasil dihapus.',
        //                 timer: 1400,
        //                 showConfirmButton: false,
        //             });

        //             setTimeout(() => window.location.reload(), 1400);
        //         } catch (error) {
        //             Swal.fire({
        //                 icon: 'error',
        //                 title: 'Gagal',
        //                 text: error.message || 'Terjadi kesalahan saat menghapus data.',
        //             });
        //         } finally {
        //             deleteButton.disabled = false;
        //         }
        //     });
        // })();
    </script>
@endpush
