@extends('games.layouts.app', [
    'title' => 'Pilih Permainan',
])

@section('content')
    <div class="h-auto flex flex-col items-center justify-center flex-1 text-center px-6">

        <h1 class="text-4xl md:text-4xl font-bold text-[#4B2A73] mb-20">
            Pilih permainan yang <br> ingin Anda ikuti!
        </h1>

        @php
            $cardClass = "bg-white rounded-2xl px-12 py-10 text-xl font-semibold
        border-2 border-[#5A2F7E]
        shadow-[8px_8px_0px_#5A2F7E]
        hover:translate-y-1 hover:shadow-[6px_6px_0px_#5A2F7E]
        transition duration-200 flex items-center justify-center text-center";
        @endphp

        <div class="grid grid-cols-2 gap-8 max-w-xl w-full">

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
            class="fixed bottom-24 left-6 sm:left-10 lg:left-[72px] inline-flex items-center gap-2 rounded-full bg-[#5A2F7E] px-6 py-2.5 text-sm font-semibold text-white shadow-[0_10px_24px_rgba(90,47,126,0.25)] transition hover:-translate-y-0.5">
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
