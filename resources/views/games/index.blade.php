@extends('games.layouts.app', [
    'title' => 'Pilih Permainan',
])

@section('content')
    <div class="h-auto flex flex-col items-center flex-1 text-center px-6">
        <div class="flex items-center gap-3 mb-24">
            <img src="{{ asset('assets/images/logo/nexsell.svg') }}" alt="">

            <div class="flex flex-col leading-none items-start">
                <h1 class="font-bold text-[#802A76] text-[45px] m-0">
                    NEXSELL
                </h1>
                <h1 class="font-bold text-[#802A76] text-[30px] m-0 mt-[2px]">
                    GAMES AREA
                </h1>
            </div>
        </div>

        <h1 class="text-[33px] font-bold text-[#802A76] mb-[70px]">
            Pilih permainan yang ingin Anda ikuti!
        </h1>

        @php
            $cardClass = "bg-white rounded-2xl px-8 py-7 text-2xl font-semibold
        border-2 border-[#802A76]
        shadow-[8px_8px_0px_#802A76]
        hover:translate-y-1 hover:shadow-[6px_6px_0px_#802A76]
        transition duration-200 flex items-center justify-center text-center";
        @endphp

        <div class="grid grid-cols-2 gap-8 max-w-xl w-full mb-36">

            @if ($activeTypes->contains('quiz'))
                <a href="{{ route('games.play-type', 'quiz') }}" class="{{ $cardClass }}">
                    Kuis
                </a>
            @endif

            @if ($activeTypes->contains('guess_image'))
                <a href="{{ route('games.play-type', 'guess_image') }}" class="{{ $cardClass }}">
                    Tebak <br> Gambar
                </a>
            @endif

            @if ($activeTypes->contains('spin'))
                <a href="{{ route('games.play-type', 'spin') }}" class="{{ $cardClass }} col-span-2 mx-auto max-w-xs">
                    Roda Putar Berhadiah
                </a>
            @endif

        </div>

        <a href="{{ route('landing.index') }}"
            class="fixed bottom-[110px] left-11 inline-flex items-center gap-2 rounded-full bg-[#802A76] px-6 py-2.5 text-sm font-semibold text-white shadow-[0_10px_24px_rgba(90,47,126,0.25)] transition hover:-translate-y-0.5">
            Kembali
        </a>

    </div>
@endsection

@push('script')
    <script>
        localStorage.removeItem('vm_success_audio_played');
        localStorage.removeItem('vm_fail_audio_played');
    </script>
@endpush
