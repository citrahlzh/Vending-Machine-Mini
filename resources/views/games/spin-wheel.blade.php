@extends('games.layouts.app', [
    'title' => 'Spin Wheel Game',
])

@section('content')
    <div class="w-full max-w-[1120px] flex flex-col gap-10 lg:gap-12">
        @include('games.partials.page-header', [
            'mode' => 'badge',
            'badge' => 'Kesempatan Tersisa: ' . ($spinRemaining ?? 1) . 'x',
        ])

        <div class="flex flex-col items-center mt-5">
            <h1 class="text-4xl font-bold text-[#802A76] mb-[100px] text-center">
                Putar dan dapatkan hadiahnya!
            </h1>

            <div class="relative">

                <div class="absolute left-1/2 -translate-x-1/2 -top-10 z-20">
                <svg width="68" height="81" viewBox="0 0 68 81" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g filter="url(#filter0_d_556_25)">
                        <path d="M34 78L2 0.999998L34 15.6364L66 0.999998L34 78Z" fill="#802A76" />
                    </g>
                    <defs>
                        <filter id="filter0_d_556_25" x="0" y="0" width="68" height="81" filterUnits="userSpaceOnUse"
                            color-interpolation-filters="sRGB">
                            <feFlood flood-opacity="0" result="BackgroundImageFix" />
                            <feColorMatrix in="SourceAlpha" type="matrix"
                                values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                            <feOffset dy="1" />
                            <feGaussianBlur stdDeviation="1" />
                            <feComposite in2="hardAlpha" operator="out" />
                            <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0" />
                            <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_556_25" />
                            <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_556_25" result="shape" />
                        </filter>
                    </defs>
                </svg>
                </div>

                <div class="flex w-[430px] h-[430px] justify-center items-center">
                    <svg id="spinWheel" viewBox="0 0 360 360" class="w-full h-full"></svg>
                </div>

            </div>

            <p id="spinEmptyNotice" class="mt-6 text-sm text-[#874a6c] hidden">
                Segmen spin belum tersedia. Silakan coba lagi nanti.
            </p>
            <p id="spinStatusNotice" class="mt-3 text-sm text-[#874a6c] hidden"></p>

            <button id="spinButton"
                class="mt-12 bg-white px-10 py-4 rounded-full text-2xl font-semibold
               border-2 border-[#802A76] text-[#802A76]
               shadow-[8px_8px_0px_#802A76]
               hover:translate-y-1 hover:shadow-[6px_6px_0px_#802A76]
               transition duration-200">
                Putar Sekarang
            </button>
        </div>
    </div>
@endsection

@push('script')
    <script>
        window.spinSegments = @json($segments);
        window.gameId = {{ $game->id }};
        window.spinConfig = @json($game->config_json ?? []);
        window.spinUrl = @json(url('/games/spin/' . $game->id));
        window.spinAudioUrl = @json(asset('assets/audio/games/spin-wheel.mp3'));
    </script>

    @vite('resources/js/spin-wheel.js')
@endpush
