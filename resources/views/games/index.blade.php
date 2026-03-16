@extends('games.layouts.app', [
    'title' => 'Pilih Permainan',
])

@section('content')
    <div class="h-auto flex flex-col items-center justify-center flex-1 text-center px-6">

        <h1 class="text-4xl md:text-4xl font-bold text-[#4B2A73] mb-20">
            Pilih permainan yang <br> ingin Anda ikuti!
        </h1>

        <div class="grid grid-cols-2 gap-10 max-w-xl">

            @if (isset($games['quiz']))
                <a href="{{ route('games.play', $games['quiz']->id) }}"
                    class="bg-white rounded-2xl px-16 py-10 text-2xl font-semibold
               border-2 border-[#5A2F7E]
               shadow-[8px_8px_0px_#5A2F7E]
               hover:translate-y-1 hover:shadow-[6px_6px_0px_#5A2F7E]
               transition duration-200">

                    Kuis

                </a>
            @endif


            @if (isset($games['guess_image']))
                <a href="{{ route('games.play', $games['guess_image']->id) }}"
                    class="bg-white rounded-2xl px-16 py-10 text-2xl font-semibold
               border-2 border-[#5A2F7E]
               shadow-[8px_8px_0px_#5A2F7E]
               hover:translate-y-1 hover:shadow-[6px_6px_0px_#5A2F7E]
               transition duration-200">
                    Tebak <br> Gambar
                </a>
            @endif

        </div>

        @if (isset($games['spin']))
            <div class="mt-10">
                <a href="{{ route('games.play', $games['spin']->id) }}"
                    class="bg-white rounded-2xl px-20 py-10 text-2xl font-semibold border-2 border-[#5A2F7E] shadow-[8px_8px_0px_#5A2F7E] hover:translate-y-1 hover:shadow-[6px_6px_0px_#5A2F7E] transition duration-200">
                    Roda Putar Berhadiah
                </a>
            </div>
        @endif

    </div>
@endsection

@push('script')
    <script>
        localStorage.removeItem('vm_success_audio_played');
        localStorage.removeItem('vm_fail_audio_played');
    </script>
@endpush
