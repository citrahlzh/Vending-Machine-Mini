@extends('dashboard.layouts.app', [
    'title' => 'Detail Harga',
])

@section('content')
    <section class="space-y-6 p-2">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard.prices.index') }}">
                    <img src="{{ asset('assets/icons/dashboard/back.svg') }}" alt="Kembali">
                </a>
                <h1 class="text-[28px] font-semibold leading-none text-[#3C1C5E]">Detail Harga</h1>
            </div>

            <p class="mt-3 text-[18px] text-[#4F3970]">
                Halaman ini untuk menampilkan data harga.
            </p>
        </div>

        <article class="rounded-[26px] border border-[#ddd2ef] bg-white p-10 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-[220px,1fr]">
                <div
                    class="flex h-[200px] w-[200px] items-center justify-center rounded-2xl border border-[#ccc3db] bg-white">
                    @if ($price->product?->image_url)
                        <img src="{{ asset('storage/' . $price->product->image_url) }}" alt="{{ $price->product?->product_name }}"
                            class="max-h-[150px] max-w-[150px] object-contain">
                    @endif
                </div>

                <div class="grid grid-cols-1 gap-x-16 gap-y-6 md:grid-cols-2">
                    <div>
                        <p class="text-[13px] font-semibold text-[#7a6798]">Nama Produk</p>
                        <p class="mt-1 text-[30px] font-semibold leading-none text-[#3C1C5E]">
                            {{ $price->product?->product_name ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[13px] font-semibold text-[#7a6798]">Harga</p>
                        <p class="mt-1 text-[30px] font-semibold leading-none text-[#3C1C5E]">
                            Rp{{ number_format((int) $price->price, 0, ',', '.') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[13px] font-semibold text-[#7a6798]">Masa Berlaku</p>
                        <p class="mt-1 text-[30px] font-semibold leading-none text-[#3C1C5E]">
                            {{ optional($price->start_date)->format('d/m/Y') }} -
                            {{ optional($price->end_date)->format('d/m/Y') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[13px] font-semibold text-[#7a6798]">Status</p>
                        <div class="mt-2">
                            @if ($price->is_active)
                                <span
                                    class="inline-flex rounded-full bg-[#d7f2e1] px-4 py-1 text-[12px] font-semibold text-[#17914f]">
                                    Aktif
                                </span>
                            @else
                                <span
                                    class="inline-flex rounded-full bg-[#fde0e1] px-4 py-1 text-[12px] font-semibold text-[#de1c24]">
                                    Tidak Aktif
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </article>
    </section>
@endsection
