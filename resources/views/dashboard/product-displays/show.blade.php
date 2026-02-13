@extends('dashboard.layouts.app', [
    'title' => 'Detail Stok & Slot',
])

@section('content')
    <section class="space-y-6 p-2">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard.product-displays.index') }}">
                    <img src="{{ asset('assets/icons/dashboard/back.svg') }}" alt="Kembali">
                </a>
                <h1 class="text-[28px] font-semibold leading-none text-[#3C1C5E]">Detail Stok & Slot</h1>
            </div>

            <p class="mt-3 text-[18px] text-[#4F3970]">
                Halaman ini untuk menampilkan data display produk.
            </p>
        </div>

        <article class="rounded-[26px] border border-[#ddd2ef] bg-white p-10 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-[220px,1fr]">
                <div
                    class="flex h-[200px] w-[200px] items-center justify-center rounded-2xl border border-[#ccc3db] bg-white">
                    @if ($productDisplay->product?->image_url)
                        <img src="{{ asset('storage/' . $productDisplay->product->image_url) }}"
                            alt="{{ $productDisplay->product?->product_name }}" class="max-h-[150px] max-w-[150px] object-contain">
                    @endif
                </div>

                <div class="grid grid-cols-1 gap-x-16 gap-y-6 md:grid-cols-2">
                    <div>
                        <p class="text-[13px] font-semibold text-[#7a6798]">Nama Produk</p>
                        <p class="mt-1 text-[32px] font-semibold leading-none text-[#3C1C5E]">
                            {{ $productDisplay->product?->product_name ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[13px] font-semibold text-[#7a6798]">Harga</p>
                        <p class="mt-1 text-[32px] font-semibold leading-none text-[#3C1C5E]">
                            @if ($productDisplay->price)
                                Rp{{ number_format((int) $productDisplay->price->price, 0, ',', '.') }}
                            @else
                                -
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-[13px] font-semibold text-[#7a6798]">Sel</p>
                        <p class="mt-1 text-[32px] font-semibold leading-none text-[#3C1C5E]">
                            {{ $productDisplay->cell?->code ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[13px] font-semibold text-[#7a6798]">Status</p>
                        <div class="mt-2">
                            @if ($productDisplay->status === 'active')
                                <span
                                    class="inline-flex rounded-full bg-[#d7f2e1] px-4 py-1 text-[12px] font-semibold text-[#17914f]">
                                    Aktif
                                </span>
                            @elseif($productDisplay->status === 'inactive')
                                <span
                                    class="inline-flex rounded-full bg-[#fde0e1] px-4 py-1 text-[12px] font-semibold text-[#de1c24]">
                                    Tidak Aktif
                                </span>
                            @else
                                <span
                                    class="inline-flex rounded-full bg-[#ffe6c8] px-4 py-1 text-[12px] font-semibold text-[#c57a00]">
                                    Dihentikan
                                </span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <p class="text-[13px] font-semibold text-[#7a6798]">Stok</p>
                        <p class="mt-1 text-[32px] font-semibold leading-none text-[#3C1C5E]">
                            {{ $productDisplay->cell?->qty_current ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>
        </article>
    </section>
@endsection
