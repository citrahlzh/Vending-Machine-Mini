@extends('games.layouts.app', [
    'title' => 'Hasil',
])

@section('content')
    <div class="flex w-full flex-col items-center text-center justify-center">
        <div class="w-full max-w-2xl space-y-6 items-center">
            <h1 class="text-4xl sm:text-5xl font-extrabold text-[#4b2a6a] tracking-wide">
                YAHH...
            </h1>

            <p class="text-lg sm:text-2xl text-[#5a3b77] font-semibold">
                Kamu belum beruntung, coba lagi nanti ya!
            </p>

            {{-- <div class="flex items-center justify-center py-4">
                <div
                    class="min-h-[120px] w-full rounded-3xl border border-[#e7d9f6] bg-white/70 px-8 py-6 text-[#4b2a6a] shadow-sm">
                    <div class="text-sm uppercase tracking-[0.2em] text-[#7a5a99]">Hadiah</div>
                    <div class="mt-2 text-2xl sm:text-3xl font-bold">
                        Belum Ada
                    </div>
                    <div class="mt-2 text-sm text-[#6b4a87]">
                        Jangan khawatir, kesempatan berikutnya bisa jadi milikmu.
                    </div>
                </div>
            </div> --}}
            <div class="h-[500px] w-[400px] flex justify-center items-center mx-auto my-40">
                <img src="{{ asset('assets/images/landing/games/sad-face.png') }}" alt="" class="h-[350px] w-[350px]">
            </div>

            <a href="/"
                class="inline-flex items-center justify-center rounded-full bg-[#5A2F7E] px-12 py-4 text-lg font-semibold text-white shadow-[0_10px_24px_rgba(90,47,126,0.25)] transition hover:-translate-y-0.5">
                Kembali
            </a>
        </div>
    </div>
@endsection

@push('script')
    <audio id="failAudio" src="{{ asset('assets/audio/games/failed.mp3') }}" preload="auto"></audio>
    <script>
        (() => {
            const audio = document.getElementById('failAudio');
            if (!audio) return;

            const isBackNavigation = () => {
                const nav = performance.getEntriesByType?.('navigation')?.[0];
                return nav?.type === 'back_forward';
            };

            const storageKey = 'vm_fail_audio_played';

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
