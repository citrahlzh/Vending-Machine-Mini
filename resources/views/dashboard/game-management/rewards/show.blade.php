@extends('dashboard.layouts.app', [
    'title' => 'Detail Hadiah',
])

@section('content')
    <section class="space-y-6 p-2">

        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard.game-management.rewards.index') }}">
                    <img src="{{ asset('assets/icons/dashboard/back.svg') }}">
                </a>

                <h1 class="text-[28px] font-semibold leading-none text-[#3C1C5E]">
                    Detail Hadiah
                </h1>
            </div>

            <p class="mt-3 text-[18px] text-[#4F3970]">
                Halaman ini menampilkan detail hadiah permainan.
            </p>
        </div>


        <article class="rounded-[26px] border border-[#ddd2ef] bg-white p-10 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">

            <div class="grid grid-cols-1 gap-x-16 gap-y-6 md:grid-cols-2">

                <div>
                    <p class="text-[13px] font-semibold text-[#7a6798]">Kode Hadiah</p>
                    <p class="mt-1 text-[18px] font-semibold text-[#3C1C5E]">
                        {{ $reward->code }}
                    </p>
                </div>

                <div>
                    <p class="text-[13px] font-semibold text-[#7a6798]">Nama Hadiah</p>
                    <p class="mt-1 text-[18px] font-semibold text-[#3C1C5E]">
                        {{ $reward->name }}
                    </p>
                </div>

                <div>
                    <p class="text-[13px] font-semibold text-[#7a6798]">Tipe Hadiah</p>
                    <p class="mt-1 text-[18px] font-semibold text-[#3C1C5E]">
                        {{ $reward->type }}
                    </p>
                </div>

                <div>
                    <p class="text-[13px] font-semibold text-[#7a6798]">Produk Display</p>
                    <p class="mt-1 text-[18px] font-semibold text-[#3C1C5E]">
                        {{ $reward->productDisplay?->product_name ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-[13px] font-semibold text-[#7a6798]">Stok</p>
                    <p class="mt-1 text-[18px] font-semibold text-[#3C1C5E]">
                        {{ $reward->stock ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-[13px] font-semibold text-[#7a6798]">Status</p>

                    @if ($reward->is_active)
                        <span class="inline-flex rounded-full bg-[#d7f2e1] px-4 py-1 text-[12px] text-[#17914f]">
                            Aktif
                        </span>
                    @else
                        <span class="inline-flex rounded-full bg-[#fde0e1] px-4 py-1 text-[12px] text-[#de1c24]">
                            Tidak Aktif
                        </span>
                    @endif

                </div>

                <div class="md:col-span-2">
                    <p class="text-[13px] font-semibold text-[#7a6798]">Deskripsi</p>
                    <p class="mt-1 text-[16px] text-[#3C1C5E]">
                        {{ $reward->description ?? '-' }}
                    </p>
                </div>

            </div>

        </article>

    </section>
@endsection
