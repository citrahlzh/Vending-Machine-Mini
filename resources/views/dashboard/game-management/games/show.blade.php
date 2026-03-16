@extends('dashboard.layouts.app', [
    'title' => 'Detail Game',
])

@section('content')
    <section class="space-y-6 p-2">

        <div>

            <div class="flex items-center gap-2">

                <a href="{{ route('dashboard.game-management.games.index') }}">
                    <img src="{{ asset('assets/icons/dashboard/back.svg') }}">
                </a>

                <h1 class="text-[28px] font-semibold text-[#3C1C5E]">
                    Detail Game
                </h1>

            </div>

            <p class="mt-3 text-[#4F3970]">
                Menampilkan detail konfigurasi permainan.
            </p>

        </div>


        <article class="rounded-[26px] border border-[#ddd2ef] bg-white p-10 shadow">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <div>
                    <p class="text-[13px] font-semibold text-[#7a6798]">
                        Nama Game
                    </p>

                    <p class="mt-1 text-[18px] font-semibold text-[#3C1C5E]">
                        {{ $game->name }}
                    </p>
                </div>


                <div>
                    <p class="text-[13px] font-semibold text-[#7a6798]">
                        Tipe Game
                    </p>

                    <p class="mt-1 text-[18px] font-semibold text-[#3C1C5E]">
                        {{ ucfirst($game->type) }}
                    </p>
                </div>


                <div class="md:col-span-2">

                    <p class="text-[13px] font-semibold text-[#7a6798]">
                        Config Game
                    </p>

                    <div class="mt-2 border rounded-lg p-4 bg-gray-50 text-sm">

                        <pre>{{ json_encode($game->config_json ?? [], JSON_PRETTY_PRINT) }}</pre>

                    </div>

                </div>


                <div>

                    <p class="text-[13px] font-semibold text-[#7a6798]">
                        Status
                    </p>

                    @if ($game->is_active)
                        <span class="inline-flex rounded-full bg-[#d7f2e1] px-4 py-1 text-[12px] text-[#17914f]">
                            Aktif
                        </span>
                    @else
                        <span class="inline-flex rounded-full bg-[#fde0e1] px-4 py-1 text-[12px] text-[#de1c24]">
                            Nonaktif
                        </span>
                    @endif

                </div>

                @if (in_array($game->type, ['quiz', 'guess_image']))
                    <div class="md:col-span-2">
                        <p class="text-[13px] font-semibold text-[#7a6798]">
                            Distribusi Reward
                        </p>

                        @php
                            $distribution = $game->config_json['reward_distribution'] ?? [];
                        @endphp

                        @if (!empty($distribution))
                            <div class="mt-3 space-y-2">
                                @foreach ($distribution as $item)
                                    <div class="flex items-center justify-between rounded-lg border border-[#e2d8f3] bg-[#fbf9ff] px-4 py-2 text-[14px] text-[#3C1C5E]">
                                        <span>Minimal Skor: {{ $item['score'] ?? '-' }}</span>
                                        <span>Reward ID: {{ $item['reward_id'] ?? '-' }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="mt-2 text-[14px] text-[#7a6798]">Belum ada distribusi reward.</p>
                        @endif
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-[13px] font-semibold text-[#7a6798]">
                            Soal Terpilih
                        </p>

                        @if ($game->quests->count())
                            <div class="mt-3 space-y-2">
                                @foreach ($game->quests as $quest)
                                    <div class="rounded-lg border border-[#e2d8f3] bg-[#fbf9ff] px-4 py-2 text-[14px] text-[#3C1C5E]">
                                        {{ $quest->prompt }}
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="mt-2 text-[14px] text-[#7a6798]">Belum ada soal yang dipilih.</p>
                        @endif
                    </div>
                @endif

                @if ($game->type === 'spin')
                    <div class="md:col-span-2">
                        <p class="text-[13px] font-semibold text-[#7a6798]">
                            Spin Segments
                        </p>

                        @if ($game->spinSegments->count())
                            <div class="mt-3 space-y-2">
                                @foreach ($game->spinSegments as $segment)
                                    <div class="flex items-center justify-between rounded-lg border border-[#e2d8f3] bg-[#fbf9ff] px-4 py-2 text-[14px] text-[#3C1C5E]">
                                        <span>{{ $segment->label }}</span>
                                        <span>{{ $segment->reward?->name ?? 'Tanpa reward' }}</span>
                                        <span>Bobot: {{ $segment->weight }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="mt-2 text-[14px] text-[#7a6798]">Belum ada segment.</p>
                        @endif
                    </div>
                @endif
            </div>

        </article>

    </section>
@endsection
