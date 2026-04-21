@props([
    'mode' => 'progress',
    'badge' => null,
    'progress' => null,
])

<div class="w-full max-w-[1120px] px-6">
    <div class="flex items-start justify-between gap-6 sm:items-center">
        <div class="flex items-center gap-3 sm:gap-5">
            <img src="{{ asset('assets/images/logo/nexsell-games.svg') }}" alt="Nexsell Games Area"
                class="h-[58px] w-auto sm:h-[78px] lg:h-[92px]">
        </div>

        @if ($badge)
            <div
                class="inline-flex min-h-[52px] items-center justify-center rounded-full border-[4px] border-[#97448e] bg-[#802A76]/20 px-6 py-3 text-center text-[16px] font-bold text-[#4B1745] shadow-[0_10px_30px_rgba(148,68,142,0.12)] sm:min-h-[64px] sm:px-9 sm:text-[22px] {{ $mode === 'progress' ? 'games-header-timer' : '' }}">
                {{ $badge }}
            </div>
        @endif
    </div>

    @if ($mode === 'progress')
        <div class="mt-12">
            <div class="h-[22px] w-full overflow-hidden rounded-full bg-[#c787be]/75 shadow-inner">
                <div id="gameHeaderProgressBar"
                    class="flex h-full min-w-[76px] items-center justify-center rounded-full bg-[#83297a] px-4 text-[11px] font-bold text-white transition-[width] duration-300 sm:text-[14px]"
                    style="width: {{ $progress ?? 0 }}%">
                    <span id="gameHeaderProgressLabel">{{ $progress ?? 0 }}%</span>
                </div>
            </div>
        </div>
    @endif
</div>
