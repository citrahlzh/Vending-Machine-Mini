@extends('dashboard.layouts.app', [
    'title' => 'Detail Soal',
])

@section('content')
    <section class="space-y-6 p-2">

        <div>
            <div class="flex items-center gap-2">

                <a href="{{ route('dashboard.game-management.quests.index') }}">
                    <img src="{{ asset('assets/icons/dashboard/back.svg') }}">
                </a>

                <h1 class="text-[28px] font-semibold text-[#3C1C5E]">
                    Detail Soal
                </h1>

            </div>

            <p class="mt-3 text-[18px] text-[#4F3970]">
                Halaman ini menampilkan detail soal permainan.
            </p>
        </div>


        <article class="rounded-[26px] border border-[#ddd2ef] bg-white p-10 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">

            <div class="grid grid-cols-1 gap-x-16 gap-y-8 md:grid-cols-2">

                <div>
                    <p class="text-[13px] font-semibold text-[#7a6798]">Tipe Game</p>
                    <p class="mt-1 text-[18px] font-semibold text-[#3C1C5E]">
                        {{ $quest->game_type === 'quiz' ? 'Kuis' : 'Tebak Gambar'}}
                    </p>
                </div>

                <div>
                    <p class="text-[13px] font-semibold text-[#7a6798]">Tipe Soal</p>
                    <p class="mt-1 text-[18px] font-semibold text-[#3C1C5E]">
                        {{ $quest->type === 'text' ? 'Jawaban Singkat' : 'Pilihan Ganda'}}
                    </p>
                </div>

                <div class="md:col-span-2">
                    <p class="text-[13px] font-semibold text-[#7a6798]">Pertanyaan</p>
                    <p class="mt-1 text-[18px] text-[#3C1C5E]">
                        {{ $quest->prompt }}
                    </p>
                </div>

                @if ($quest->game_type === 'guess_image')
                    <div class="md:col-span-2">
                        <p class="text-[13px] font-semibold text-[#7a6798]">Gambar</p>

                        @if ($quest->image_url)
                            <div class="mt-3 overflow-hidden rounded-2xl border border-[#d8ccee] bg-[#f7f3ff] p-4">
                                <img src="{{ asset('storage/' . $quest->image_url) }}"
                                    class="h-[260px] w-full object-contain">
                            </div>
                        @else
                            <div
                                class="mt-3 flex h-[260px] items-center justify-center rounded-2xl border border-dashed border-[#d8ccee] bg-[#fbf9ff] text-[14px] text-[#7a6798]">
                                Gambar belum diunggah.
                            </div>
                        @endif
                    </div>
                @endif


                @if ($quest->option)
                    <div class="md:col-span-2">

                        <p class="text-[13px] font-semibold text-[#7a6798]">
                            Pilihan Jawaban
                        </p>

                        <div class="mt-4 space-y-3">
                            @foreach ($quest->option as $opt)
                                @php
                                    $isCorrect = $quest->answer['correct_answer'] == $opt['key'];
                                @endphp
                                <div
                                    class="flex items-center gap-3 rounded-2xl border {{ $isCorrect ? 'border-[#7ac39a] bg-[#e9f7f0]' : 'border-[#e2d8f3] bg-[#fbf9ff]' }} p-3">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-lg {{ $isCorrect ? 'bg-[#17914f]' : 'bg-[#3C1C5E]' }} text-[14px] font-semibold text-white">
                                        {{ $opt['key'] }}.
                                    </div>
                                    <div class="flex-1 text-[15px] text-[#3C1C5E]">
                                        {{ $opt['text'] ?: '-' }}
                                    </div>
                                    <span
                                        class="inline-flex h-9 items-center rounded-lg {{ $isCorrect ? 'bg-[#17914f] text-white' : 'border border-[#d8ccee] bg-white text-[#3C1C5E]' }} px-4 text-[12px] font-semibold">
                                        {{ $isCorrect ? 'Benar' : 'Salah' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                    </div>
                @endif


                <div>
                    <p class="text-[13px] font-semibold text-[#7a6798]">Jawaban Benar</p>
                    <p class="mt-1 text-[18px] text-[#3C1C5E]">
                        {{ $quest->answer['correct_answer'] }}
                    </p>
                </div>


                <div>

                    <p class="text-[13px] font-semibold text-[#7a6798]">Status</p>

                    @if ($quest->is_active)
                        <span class="inline-flex rounded-full bg-[#d7f2e1] px-4 py-1 text-[12px] text-[#17914f]">
                            Aktif
                        </span>
                    @else
                        <span class="inline-flex rounded-full bg-[#fde0e1] px-4 py-1 text-[12px] text-[#de1c24]">
                            Tidak Aktif
                        </span>
                    @endif

                </div>

            </div>

        </article>

    </section>
@endsection
