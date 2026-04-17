@extends('dashboard.layouts.app', [
    'title' => 'Tambah Permainan',
])

@section('content')

    <section class="space-y-6 p-2">

        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard.game-management.games.index') }}">
                    <img src="{{ asset('assets/icons/dashboard/back.svg') }}" alt="Kembali">
                </a>

                <h1 class="text-[28px] font-semibold leading-none text-[#5E1C3D]">
                    Tambah Permainan {{ ucfirst(str_replace('_', ' ', $type)) }}
                </h1>
            </div>

            <p class="mt-3 text-[18px] text-[#703967]">
                Lengkapi data permainan yang akan ditambahkan.
            </p>
        </div>


        <article class="rounded-[26px] border border-[#efd2ea] bg-white p-10 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">

            <form id="gameForm" class="space-y-4" method="POST" enctype="multipart/form-data">

                <input type="hidden" name="type" value="{{ $type }}">

                {{-- BASIC DATA --}}
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#5E1C3D]">Nama Game</label>

                        <input name="name" type="text"
                            class="h-10 w-full rounded-lg border border-[#d896c4] px-3 text-[14px] text-[#5E1C3D] outline-none placeholder:text-[#caa3c0] focus:border-[#933e77]"
                            placeholder="Masukkan nama permainan">
                    </div>


                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#5E1C3D]">Status</label>

                        <select name="is_active"
                            class="h-10 w-full rounded-lg border border-[#d896c4] px-3 text-[14px] text-[#5E1C3D] outline-none focus:border-[#933e77]">

                            <option value="1">Aktif</option>
                            <option value="0">Non Aktif</option>

                        </select>
                    </div>


                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#5E1C3D]">Tanggal Mulai</label>

                        <input type="date" name="start_date"
                            class="h-10 w-full rounded-lg border border-[#d896c4] px-3 text-[14px] text-[#5E1C3D] outline-none focus:border-[#933e77]">
                    </div>


                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#5E1C3D]">Tanggal Selesai</label>

                        <input type="date" name="end_date"
                            class="h-10 w-full rounded-lg border border-[#d896c4] px-3 text-[14px] text-[#5E1C3D] outline-none focus:border-[#933e77]">
                    </div>

                </div>

                {{-- QUIZ CONFIG --}}
                @if ($type === 'quiz' || $type === 'guess_image')
                    <hr class="my-6">

                    <h2 class="text-[18px] font-semibold text-[#5E1C3D]">
                        Konfigurasi Permainan
                    </h2>

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                        <div>
                            <label class="mb-1.5 block text-[15px] font-semibold text-[#5E1C3D]">Time Limit
                                (detik)</label>
                            <input type="number" name="config[time_limit]"
                                class="h-10 w-full rounded-lg border border-[#d896c4] px-3 text-[14px] text-[#5E1C3D] outline-none focus:border-[#933e77]">
                        </div>

                        <div>
                            <label class="mb-1.5 block text-[15px] font-semibold text-[#5E1C3D]">Cooldown
                                (detik)</label>
                            <input type="number" name="config[cooldown]"
                                class="h-10 w-full rounded-lg border border-[#d896c4] px-3 text-[14px] text-[#5E1C3D] outline-none focus:border-[#933e77]">
                        </div>

                        <div>
                            <label class="mb-1.5 block text-[15px] font-semibold text-[#5E1C3D]">Jumlah Soal</label>
                            <input type="number" name="config[question_count]"
                                class="h-10 w-full rounded-lg border border-[#d896c4] px-3 text-[14px] text-[#5E1C3D] outline-none focus:border-[#933e77]">
                        </div>

                    </div>

                    <hr class="my-6">

                    <h2 class="text-[18px] font-semibold text-[#5E1C3D]">
                        Distribusi Reward
                    </h2>

                    <div id="rewardDistribution" class="space-y-3">

                        <div class="grid grid-cols-1 gap-3 md:grid-cols-3">

                            <input placeholder="Minimal Skor" name="config[reward_distribution][0][score]"
                                class="h-10 rounded-lg border border-[#d896c4] px-3 text-[14px] text-[#5E1C3D] outline-none focus:border-[#933e77]">

                            <select name="config[reward_distribution][0][reward_id]"
                                class="h-10 rounded-lg border border-[#d896c4] px-3 text-[14px] text-[#5E1C3D] outline-none focus:border-[#933e77]">

                                <option value="">Pilih Reward</option>

                                @foreach ($rewards as $reward)
                                    <option value="{{ $reward->id }}">
                                        {{ $reward->name }}
                                    </option>
                                @endforeach

                            </select>

                        </div>

                    </div>

                    <button type="button" id="addRewardDistribution" onclick="addRewardDistributionRow()"
                        class="text-[#802A76] text-sm font-semibold">
                        + Tambah Reward
                    </button>


                    <hr class="my-6">

                    <h2 class="text-[18px] font-semibold text-[#5E1C3D]">
                        Pilih Soal
                    </h2>

                    <div class="max-h-[300px] overflow-auto rounded-lg border border-[#e4d9f6]">

                        <table class="w-full text-sm">

                            <thead class="bg-[#f6f1ff] text-[#5b4a7a]">
                                <tr>
                                    <th></th>
                                    <th>Pertanyaan</th>
                                </tr>
                            </thead>

                            <tbody>

                                @foreach ($quests as $quest)
                                    <tr class="border-b">

                                        <td class="p-2 text-center">
                                            <input type="checkbox" name="quests[]" value="{{ $quest->id }}">
                                        </td>

                                        <td class="p-2">
                                            {{ $quest->prompt }}
                                        </td>

                                    </tr>
                                @endforeach

                            </tbody>

                        </table>

                    </div>
                @endif



                @if ($type === 'spin')
                    <hr class="my-6">
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                        <div>
                            <label class="mb-1.5 block text-[15px] font-semibold text-[#5E1C3D]">Maksimal Spin per
                                User</label>

                            <input type="number" name="config[max_spin_per_user]" value="1"
                                class="h-10 w-full rounded-lg border border-[#d896c4] px-3 text-[14px] text-[#5E1C3D] outline-none focus:border-[#933e77]">
                        </div>

                        <div>
                            <label class="mb-1.5 block text-[15px] font-semibold text-[#5E1C3D]">Cooldown Spin
                                (Menit)</label>

                            <input type="number" name="config[cooldown_minutes]" value="0"
                                class="h-10 w-full rounded-lg border border-[#d896c4] px-3 text-[14px] text-[#5E1C3D] outline-none focus:border-[#933e77]">
                        </div>

                    </div>

                    <h2 class="text-[18px] font-semibold text-[#5E1C3D]">
                        Spin Segments
                    </h2>

                    <div id="segmentsContainer" class="space-y-4 overflow-x-auto">

                        <div class="grid grid-cols-5 gap-3 min-w-[860px] segment-row">

                            <input name="segments[0][label]" placeholder="Label"
                                class="h-10 rounded-lg border border-[#d896c4] px-3 text-[14px] text-[#5E1C3D] outline-none focus:border-[#933e77]">

                            <select name="segments[0][reward_id]" class="h-10 border border-[#d896c4] rounded-lg px-3">

                                <option value="">Pilih Reward</option>

                                @foreach ($rewards as $reward)
                                    <option value="{{ $reward->id }}">
                                        {{ $reward->name }}
                                    </option>
                                @endforeach

                            </select>

                            <input type="number" name="segments[0][weight]" placeholder="Weight"
                                class="h-10 rounded-lg border border-[#d896c4] px-3 text-[14px] text-[#5E1C3D] outline-none focus:border-[#933e77]">

                            <input type="file" name="segments[0][image]"
                                class="block w-full text-sm text-[#5E1C3D] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#802A76] file:text-white hover:file:bg-[#741f58] border border-[#d896c4] rounded-lg cursor-pointer" />

                        </div>

                    </div>

                    <button type="button" id="addSegment" class="text-[#802A76] text-sm font-semibold">

                        + Tambah Segment

                    </button>
                @endif


                <div class="grid grid-cols-1 gap-4 pt-8 md:grid-cols-2">
                    <a href="{{ route('dashboard.game-management.games.index') }}"
                        class="flex h-10 items-center justify-center rounded-lg border border-[#802A76] bg-white text-[15px] font-semibold text-[#741f58] transition hover:bg-[#f8f4ff]">
                        Batal
                    </a>

                    <button type="submit"
                        class="h-10 rounded-lg bg-[#802A76] text-[15px] font-semibold text-white transition hover:bg-[#741f58]">
                        Simpan Game
                    </button>
                </div>


            </form>

        </article>

    </section>

@endsection

@push('script')
    <script>
        var segmentIndex = 1;
        var rewards = @json($rewards);
        var rewardDistributionIndex = 1;

        var addSegmentButton = document.getElementById('addSegment');
        if (addSegmentButton) {
            addSegmentButton.addEventListener('click', function() {

                var container = document.getElementById('segmentsContainer')

                var rewardOptions = '<option value="">Pilih Reward</option>'

                rewards.forEach(function(r) {
                    rewardOptions += '<option value="' + r.id + '">' + r.name + '</option>'
                })

                var row = document.createElement('div')

                row.className = 'grid grid-cols-5 gap-3 min-w-[860px]'

                row.innerHTML =
                    '<input name="segments[' + segmentIndex + '][label]" placeholder="Label" ' +
                    'class="h-10 rounded-lg border border-[#d896c4] px-3 text-[14px] text-[#5E1C3D] outline-none focus:border-[#933e77]">' +
                    '<select name="segments[' + segmentIndex + '][reward_id]" ' +
                    'class="h-10 rounded-lg border border-[#d896c4] px-3 text-[14px] text-[#5E1C3D] outline-none focus:border-[#933e77]">' +
                    rewardOptions +
                    '</select>' +
                    '<input type="number" name="segments[' + segmentIndex + '][weight]" placeholder="Weight" ' +
                    'class="h-10 rounded-lg border border-[#d896c4] px-3 text-[14px] text-[#5E1C3D] outline-none focus:border-[#933e77]">' +
                    '<input type="file" name="segments[' + segmentIndex + '][image]" ' +
                    'class="block w-full text-sm text-[#5E1C3D] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#802A76] file:text-white hover:file:bg-[#741f58] border border-[#d896c4] rounded-lg cursor-pointer" />'

                container.appendChild(row)

                segmentIndex++

            })
        }

        window.addRewardDistributionRow = function() {
            var container = document.getElementById('rewardDistribution')
            if (!container) return

            var rewardOptions = '<option value="">Pilih Reward</option>'
            rewards.forEach(function(r) {
                rewardOptions += '<option value="' + r.id + '">' + r.name + '</option>'
            })

            var row = document.createElement('div')
            row.className = 'grid grid-cols-1 gap-3 md:grid-cols-3'
            row.innerHTML =
                '<input placeholder="Minimal Skor" name="config[reward_distribution][' + rewardDistributionIndex + '][score]" ' +
                'class="h-10 rounded-lg border border-[#d896c4] px-3 text-[14px] text-[#5E1C3D] outline-none focus:border-[#933e77]">' +
                '<select name="config[reward_distribution][' + rewardDistributionIndex + '][reward_id]" ' +
                'class="h-10 rounded-lg border border-[#d896c4] px-3 text-[14px] text-[#5E1C3D] outline-none focus:border-[#933e77]">' +
                rewardOptions +
                '</select>' +
                '<button type="button" ' +
                'class="h-10 rounded-lg border border-[#e2d8f3] bg-white px-3 text-[13px] font-semibold text-[#933e77] transition hover:bg-[#f8f4ff]" ' +
                'onclick="this.parentElement.remove()">' +
                'Hapus' +
                '</button>'

            container.appendChild(row)
            rewardDistributionIndex++
        }


        // SUBMIT GAME
        document.getElementById('gameForm').addEventListener('submit', function(e) {

            e.preventDefault()

            var formData = new FormData(this)

            fetch('/api/game/store', {
                method: 'POST',
                body: formData
            }).then(function(res) {
                return res.json().then(function(payload) {
                    return {
                        ok: res.ok,
                        data: payload
                    }
                })
            }).then(function(result) {
                if (!result.ok) {
                    throw new Error(result.data && result.data.message ? result.data.message :
                        'Terjadi kesalahan')
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: result.data.message
                })

                setTimeout(function() {
                    window.location.href = "/dashboard/game-management/games"
                }, 1400)
            }).catch(function(err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: err.message
                })
            })

        })
    </script>
@endpush
