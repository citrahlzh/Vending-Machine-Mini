@extends('dashboard.layouts.app', [
    'title' => 'Edit Game',
])

@section('content')
    <section class="space-y-6 p-2">

        <div>

            <div class="flex items-center gap-2">

                <a href="{{ route('dashboard.game-management.games.index') }}">
                    <img src="{{ asset('assets/icons/dashboard/back.svg') }}">
                </a>

                <h1 class="text-[28px] font-semibold text-[#3C1C5E]">
                    Edit Game
                </h1>

            </div>

            <p class="mt-2 text-[#4F3970]">
                Perbarui konfigurasi permainan.
            </p>

        </div>


        <article class="rounded-[26px] border border-[#ddd2ef] bg-white p-10 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">

            <form id="updateGameForm" class="space-y-4">

                <div>

                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]">
                        Nama Game
                    </label>

                    <input type="text" name="name" value="{{ $game->name }}"
                        class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">

                </div>


                <div>

                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]">
                        Tipe Game
                    </label>

                    <select name="type" id="gameType"
                        class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">

                        <option value="quiz" {{ $game->type == 'quiz' ? 'selected' : '' }}>
                            Quiz
                        </option>

                        <option value="spin" {{ $game->type == 'spin' ? 'selected' : '' }}>
                            Spin
                        </option>

                        <option value="guess_image" {{ $game->type == 'guess_image' ? 'selected' : '' }}>
                            Tebak Gambar
                        </option>

                    </select>

                </div>


                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]">Tanggal Mulai</label>

                        <input type="date" name="start_date"
                            value="{{ optional($game->start_date)->format('Y-m-d') }}"
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]">Tanggal Selesai</label>

                        <input type="date" name="end_date" value="{{ optional($game->end_date)->format('Y-m-d') }}"
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">
                    </div>

                </div>


                {{-- CONFIG QUIZ/GUESS IMAGE --}}
                <div id="quizConfig" class="hidden space-y-4">

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                        <div>
                            <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]">Time Limit
                                (detik)</label>
                            <input type="number" name="config[time_limit]"
                                value="{{ $game->config_json['time_limit'] ?? '' }}"
                                class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">
                        </div>

                        <div>
                            <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]">Cooldown
                                (detik)</label>
                            <input type="number" name="config[cooldown]"
                                value="{{ $game->config_json['cooldown'] ?? '' }}"
                                class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">
                        </div>

                        <div>
                            <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]">Jumlah Soal</label>
                            <input type="number" name="config[question_count]"
                                value="{{ $game->config_json['question_count'] ?? '' }}"
                                class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">
                        </div>

                    </div>

                    <hr class="my-4">

                    <h3 class="text-[16px] font-semibold text-[#3C1C5E]">Distribusi Reward</h3>

                    <div id="rewardDistribution" class="space-y-3"></div>

                    <button type="button" id="addRewardDistribution" onclick="addRewardDistributionRow()"
                        class="text-[#5A2F7E] text-sm font-semibold">
                        + Tambah Reward
                    </button>

                    <hr class="my-4">

                    <h3 class="text-[16px] font-semibold text-[#3C1C5E]">Pilih Soal</h3>

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
                                            <input type="checkbox" name="quests[]" value="{{ $quest->id }}"
                                                @checked($game->quests->contains('id', $quest->id))>
                                        </td>

                                        <td class="p-2">
                                            {{ $quest->prompt }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>

                    </div>

                </div>


                {{-- CONFIG SPIN --}}
                <div id="spinConfig" class="hidden space-y-4">

                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]">
                            Durasi Spin (detik)
                        </label>

                        <input type="number" name="config[spin_duration]"
                            value="{{ $game->config_json['spin_duration'] ?? '' }}"
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">
                    </div>


                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]">
                            Maksimal Spin per User
                        </label>

                        <input type="number" name="config[max_spin_per_user]"
                            value="{{ $game->config_json['max_spin_per_user'] ?? 1 }}"
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">
                    </div>


                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]">
                            Cooldown Spin (menit)
                        </label>

                        <input type="number" name="config[cooldown_minutes]"
                            value="{{ $game->config_json['cooldown_minutes'] ?? 0 }}"
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">
                    </div>

                </div>

                {{-- SPIN SEGMENTS --}}
                <div id="spinSegments" class="space-y-4">

                    <h3 class="text-[18px] font-semibold text-[#3C1C5E]">Spin Segments</h3>

                    <div id="segmentContainer" class="space-y-3">

                        @foreach ($game->spinSegments as $i => $segment)
                            <div class="grid grid-cols-1 gap-3 md:grid-cols-4 border border-[#e4d9f6] p-3 rounded-lg">

                                <input type="text" name="segments[{{ $i }}][label]"
                                    value="{{ $segment->label }}" placeholder="Label"
                                    class="h-10 rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">

                                <select name="segments[{{ $i }}][reward_id]"
                                    class="h-10 rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">

                                    @foreach ($rewards as $reward)
                                        <option value="{{ $reward->id }}"
                                            {{ $segment->reward_id == $reward->id ? 'selected' : '' }}>
                                            {{ $reward->name }}
                                        </option>
                                    @endforeach

                                </select>

                                <input type="number" name="segments[{{ $i }}][weight]"
                                    value="{{ $segment->weight }}" placeholder="Weight"
                                    class="h-10 rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">

                                <button type="button" onclick="removeSegment(this)"
                                    class="h-10 rounded-lg bg-red-500 px-3 text-[14px] font-semibold text-white">
                                    Hapus
                                </button>

                            </div>
                        @endforeach

                    </div>

                    <button type="button" onclick="addSegment()"
                        class="inline-flex items-center rounded-lg bg-[#5A2F7E] px-4 py-2 text-[14px] font-semibold text-white transition hover:bg-[#4B1F74]">

                        Tambah Segment

                    </button>

                </div>


                {{-- CONFIG TEBAK GAMBAR --}}
                <div id="guessConfig" class="hidden space-y-4"></div>


                <div>

                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]">
                        Status
                    </label>

                    <select name="is_active"
                        class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">

                        <option value="1" {{ $game->is_active ? 'selected' : '' }}>
                            Aktif
                        </option>

                        <option value="0" {{ !$game->is_active ? 'selected' : '' }}>
                            Nonaktif
                        </option>

                    </select>

                </div>


                <div class="grid grid-cols-1 gap-4 pt-8 md:grid-cols-2">
                    <a href="{{ route('dashboard.game-management.games.index') }}"
                        class="flex h-10 items-center justify-center rounded-lg border border-[#5A2F7E] bg-white text-[15px] font-semibold text-[#4B1F74] transition hover:bg-[#f8f4ff]">
                        Batal
                    </a>

                    <button type="submit"
                        class="h-10 rounded-lg bg-[#5A2F7E] text-[15px] font-semibold text-white transition hover:bg-[#4B1F74]">
                        Update Game
                    </button>
                </div>

            </form>

        </article>

    </section>
@endsection

@push('script')
    <script>
        var type = document.getElementById('gameType');

        var quiz = document.getElementById('quizConfig');
        var spin = document.getElementById('spinConfig');
        var guess = document.getElementById('guessConfig');
        var rewards = @json($rewards);
        var rewardDistributionData = @json($game->config_json['reward_distribution'] ?? []);

        function updateConfig() {

            quiz.classList.add('hidden')
            spin.classList.add('hidden')
            guess.classList.add('hidden')

            if (type.value === 'quiz') quiz.classList.remove('hidden')
            if (type.value === 'spin') spin.classList.remove('hidden')
            if (type.value === 'guess_image') {
                quiz.classList.remove('hidden')
                guess.classList.remove('hidden')
            }

        }

        updateConfig()

        type.addEventListener('change', updateConfig)
    </script>

    <script>
        var rewardDistributionIndex = Array.isArray(rewardDistributionData) ? rewardDistributionData.length : 0;

        function buildRewardRow(index, scoreValue, rewardId) {
            var rewardOptions = '<option value="">Pilih Reward</option>'
            rewards.forEach(function(r) {
                var selected = rewardId && String(rewardId) === String(r.id) ? 'selected' : ''
                rewardOptions += '<option value="' + r.id + '" ' + selected + '>' + r.name + '</option>'
            })

            var safeScore = scoreValue ? scoreValue : ''

            return '' +
                '<div class="grid grid-cols-1 gap-3 md:grid-cols-3">' +
                '<input placeholder="Minimal Skor" name="config[reward_distribution][' + index + '][score]" ' +
                'value="' + safeScore + '" ' +
                'class="h-10 rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">' +
                '<select name="config[reward_distribution][' + index + '][reward_id]" ' +
                'class="h-10 rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">' +
                rewardOptions +
                '</select>' +
                '<button type="button" ' +
                'class="h-10 rounded-lg border border-[#e2d8f3] bg-white px-3 text-[13px] font-semibold text-[#6B3E93] transition hover:bg-[#f8f4ff]" ' +
                'onclick="this.parentElement.remove()">' +
                'Hapus' +
                '</button>' +
                '</div>'
        }

        function renderRewardDistribution() {
            var container = document.getElementById('rewardDistribution')
            if (!container) return

            container.innerHTML = ''

            if (Array.isArray(rewardDistributionData) && rewardDistributionData.length) {
                rewardDistributionData.forEach(function(item, index) {
                    container.insertAdjacentHTML('beforeend', buildRewardRow(index, item.score, item.reward_id))
                })
                rewardDistributionIndex = rewardDistributionData.length
            } else {
                container.insertAdjacentHTML('beforeend', buildRewardRow(0))
                rewardDistributionIndex = 1
            }
        }

        renderRewardDistribution()

        window.addRewardDistributionRow = function() {
            var container = document.getElementById('rewardDistribution')
            if (!container) return

            container.insertAdjacentHTML('beforeend', buildRewardRow(rewardDistributionIndex))
            rewardDistributionIndex++
        }



        document.getElementById('updateGameForm')
            .addEventListener('submit', function(e) {

                e.preventDefault()

                var formData = new FormData(this)

                fetch('/api/games/{{ $game->id }}', {
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
                        window.location.href =
                            "{{ route('dashboard.game-management.games.index') }}";
                    }, 1400);
                }).catch(function(err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: err.message
                    })
                })

            })
    </script>

    <script>
        let segmentIndex = {{ $game->spinSegments->count() }}

        function addSegment() {

            var container = document.getElementById('segmentContainer')

            var rewardOptions = '<option value="">Pilih Reward</option>'
            rewards.forEach(function(r) {
                rewardOptions += '<option value="' + r.id + '">' + r.name + '</option>'
            })

            var html = '' +
                '<div class="grid grid-cols-1 gap-3 md:grid-cols-4 border border-[#e4d9f6] p-3 rounded-lg">' +
                '<input type="text" name="segments[' + segmentIndex + '][label]" placeholder="Label" ' +
                'class="h-10 rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">' +
                '<select name="segments[' + segmentIndex + '][reward_id]" ' +
                'class="h-10 rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">' +
                rewardOptions +
                '</select>' +
                '<input type="number" name="segments[' + segmentIndex + '][weight]" placeholder="Weight" ' +
                'class="h-10 rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">' +
                '<button type="button" onclick="removeSegment(this)" ' +
                'class="h-10 rounded-lg bg-red-500 px-3 text-[14px] font-semibold text-white">' +
                'Hapus' +
                '</button>' +
                '</div>'

            container.insertAdjacentHTML('beforeend', html)

            segmentIndex++

        }

        function removeSegment(btn) {

            btn.parentElement.remove()

        }
    </script>
@endpush
