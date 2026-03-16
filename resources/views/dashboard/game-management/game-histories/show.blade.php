@extends('dashboard.layouts.app', [
    'title' => 'Detail Riwayat Permainan',
])

@section('content')
    <section class="space-y-6 p-2">

        {{-- HEADER --}}
        <div>

            <div class="flex items-center gap-2">

                <a href="{{ route('dashboard.game-management.game-history.index') }}">
                    <img src="{{ asset('assets/icons/dashboard/back.svg') }}">
                </a>

                <h1 class="text-[28px] font-semibold text-[#3C1C5E]">
                    Detail Riwayat Permainan
                </h1>

            </div>

            <p class="mt-2 text-[#4F3970]">
                Menampilkan detail sesi permainan pengguna.
            </p>

        </div>


        {{-- PLAY INFORMATION --}}
        <article class="rounded-[26px] border border-[#ddd2ef] bg-white p-8">

            <h2 class="text-[20px] font-semibold text-[#3C1C5E] mb-6">
                Informasi Permainan
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                <div>
                    <p class="text-[13px] font-semibold text-[#7a6798]">Game</p>
                    <p class="mt-1 text-[18px] font-semibold text-[#3C1C5E]">
                        {{ $play->game?->name ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-[13px] font-semibold text-[#7a6798]">ID Permainan</p>
                    <p class="mt-1 text-[18px] font-semibold text-[#3C1C5E]">
                        {{ $play->idempotency_key }}
                    </p>
                </div>

                <div>
                    <p class="text-[13px] font-semibold text-[#7a6798]">Status</p>
                    <p class="mt-1 text-[18px] font-semibold text-[#3C1C5E]">
                        {{ ucfirst($play->status) }}
                    </p>
                </div>

                <div>
                    <p class="text-[13px] font-semibold text-[#7a6798]">Mulai</p>
                    <p class="mt-1 text-[16px] text-[#3C1C5E]">
                        {{ $play->started_at }}
                    </p>
                </div>

                <div>
                    <p class="text-[13px] font-semibold text-[#7a6798]">Selesai</p>
                    <p class="mt-1 text-[16px] text-[#3C1C5E]">
                        {{ $play->finished_at ?? '-' }}
                    </p>
                </div>

            </div>

        </article>


        {{-- PLAYER RESPONSES --}}
        <article class="rounded-[26px] border border-[#ddd2ef] bg-white p-8">

            <h2 class="text-[20px] font-semibold text-[#3C1C5E] mb-6">
                Jawaban Pemain
            </h2>

            <div class="overflow-x-auto">

                <table class="w-full text-left">

                    <thead class="border-b">

                        <tr class="text-[#7a6798] text-[13px]">

                            <th class="py-3 pr-6">Soal</th>
                            <th class="pr-6">Jawaban Pemain</th>
                            <th>Status</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($play->responses as $response)
                            <tr class="border-b">

                                <td class="py-4 pr-6 text-[#3C1C5E]">
                                    {{ $response->quest?->prompt ?? '-' }}
                                </td>

                                <td class="pr-6">
                                    {{ $response->answer }}
                                </td>

                                <td>

                                    @if ($response->is_correct)
                                        <span class="text-green-600 font-semibold">
                                            Benar
                                        </span>
                                    @else
                                        <span class="text-red-600 font-semibold">
                                            Salah
                                        </span>
                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="3" class="py-8 text-center text-gray-400">
                                    Belum ada jawaban pemain
                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </article>


        {{-- REWARD --}}
        <article class="rounded-[26px] border border-[#ddd2ef] bg-white p-8">

            <h2 class="text-[20px] font-semibold text-[#3C1C5E] mb-6">
                Reward Diberikan
            </h2>

            @php
                $issuedReward = $play->issuedRewards->first();
            @endphp

            @if ($issuedReward)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <div>

                        <p class="text-[13px] font-semibold text-[#7a6798]">
                            Kode Reward
                        </p>

                        <p class="mt-1 text-[18px] font-semibold text-[#3C1C5E]">
                            {{ $issuedReward->code }}
                        </p>

                    </div>

                    <div>

                        <p class="text-[13px] font-semibold text-[#7a6798]">
                            Nama Reward
                        </p>

                        <p class="mt-1 text-[18px] font-semibold text-[#3C1C5E]">
                            {{ $issuedReward->reward?->name ?? '-' }}
                        </p>

                    </div>

                    <div>

                        <p class="text-[13px] font-semibold text-[#7a6798]">
                            Status
                        </p>

                        <p class="mt-1 text-[18px] font-semibold text-[#3C1C5E]">
                            {{ ucfirst($issuedReward->status) }}
                        </p>

                    </div>

                </div>
            @else
                <p class="text-gray-500">
                    Tidak ada reward yang diberikan.
                </p>
            @endif

        </article>

    </section>
@endsection
