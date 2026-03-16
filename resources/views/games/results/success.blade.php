@extends('games.layouts.app', [
    'title' => 'Hasil',
])

@section('content')
    @php
        $product = optional($reward->productDisplay)->product;
        $productImage = $product ? $product->image_url : null;
        $productName = $product ? $product->product_name : null;
        $imageSrc = null;
        if ($productImage) {
            if (\Illuminate\Support\Str::startsWith($productImage, ['http://', 'https://'])) {
                $imageSrc = $productImage;
            } elseif (\Illuminate\Support\Str::startsWith($productImage, ['/','assets/','storage/'])) {
                $imageSrc = $productImage[0] === '/' ? $productImage : asset($productImage);
            } else {
                $imageSrc = asset('storage/' . $productImage);
            }
        }
    @endphp

    <div class="relative flex w-full flex-col items-center text-center">
        <div class="w-full max-w-2xl space-y-6">
            <h1 class="text-4xl sm:text-5xl font-extrabold text-[#4b2a6a] tracking-wide">
                SELAMAT!
            </h1>

            @if ($imageSrc)
                <p class="text-lg sm:text-2xl text-[#5a3b77] font-semibold">
                    Kamu telah mendapatkan hadiah produk
                </p>
            @else
                <p class="text-lg sm:text-2xl text-[#5a3b77] font-semibold">
                    Kamu telah mendapatkan hadiah
                </p>
            @endif

            <div class="flex items-center justify-center py-4">
                @if ($imageSrc)
                    <div class="flex flex-col items-center gap-3">
                        <img src="{{ $imageSrc }}" alt="{{ $productName ?? $reward->name }}" loading="lazy" decoding="async" class="w-56 sm:w-72 md:w-80 max-h-[280px] object-contain drop-shadow-lg">
                        @if ($productName)
                            <div class="text-sm text-[#6b4a87]">{{ $productName }}</div>
                        @endif
                    </div>
                @else
                    <div
                        class="min-h-[120px] w-full rounded-3xl border border-[#e7d9f6] bg-white/70 px-8 py-6 text-[#4b2a6a] shadow-sm">
                        <div class="text-sm uppercase tracking-[0.2em] text-[#7a5a99]">Hadiah</div>
                        <div class="mt-2 text-2xl sm:text-3xl font-bold">
                            {{ $reward->name }}
                        </div>
                    </div>
                @endif
            </div>

            <a href="/" class="inline-flex items-center justify-center rounded-full bg-[#5A2F7E] px-12 py-4 text-lg font-semibold text-white shadow-[0_10px_24px_rgba(90,47,126,0.25)] transition hover:-translate-y-0.5">
                Kembali
            </a>
        </div>
    </div>
@endsection

@push('overlay')
    <div class="pointer-events-none absolute inset-0">
        <lottie-player
            src="{{ asset('assets/lottie/confetti.json') }}"
            background="transparent"
            speed="1"
            style="width: 100%; height: 100%;"
            autoplay>
        </lottie-player>
    </div>
@endpush

@push('script')
    <audio id="successAudio" src="{{ asset('assets/audio/games/success.mp3') }}" preload="auto"></audio>
    <script>
        (() => {
            const audio = document.getElementById('successAudio');
            if (!audio) return;

            const isBackNavigation = () => {
                const nav = performance.getEntriesByType?.('navigation')?.[0];
                return nav?.type === 'back_forward';
            };

            const storageKey = 'vm_success_audio_played';

            const tryPlay = (manual = false) => {
                if (!manual) {
                    if (isBackNavigation()) return;
                    if (localStorage.getItem(storageKey) === '1') return;
                }
                try {
                    audio.currentTime = 0;
                    const p = audio.play();
                    if (p && typeof p.catch === 'function') {
                        p.then(() => {
                            if (!manual) {
                                localStorage.setItem(storageKey, '1');
                            }
                        }).catch(() => {});
                    } else {
                        if (!manual) {
                            localStorage.setItem(storageKey, '1');
                        }
                    }
                } catch (_) {
                    // ignore
                }
            };

            window.addEventListener('pageshow', (event) => {
                if (event.persisted) {
                    return;
                }
                tryPlay();
            });

            document.addEventListener('click', () => {
                tryPlay(true);
            }, { once: true });
        })();
    </script>
@endpush
