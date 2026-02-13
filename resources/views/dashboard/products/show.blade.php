@extends('dashboard.layouts.app', [
    'title' => 'Detail Produk',
])

@section('content')
    <section class="space-y-6 p-2">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard.products.index') }}">
                    <img src="{{ asset('assets/icons/dashboard/back.svg') }}" alt="Kembali">
                </a>
                <h1 class="text-[28px] font-semibold leading-none text-[#3C1C5E]">Detail Produk</h1>
            </div>

            <p class="mt-3 text-[18px] text-[#4F3970]">
                Halaman ini untuk menampilkan data produk.
            </p>
        </div>

        <article class="rounded-[26px] border border-[#ddd2ef] bg-white p-10 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-[220px,1fr]">
                <div
                    class="flex h-[200px] w-[200px] items-center justify-center rounded-2xl border border-[#ccc3db] bg-white">
                    <img src="{{ asset('storage/' . $product->image_url) }}" alt="{{ $product->product_name }}"
                        class="max-h-[150px] max-w-[150px] object-contain">
                </div>

                <div class="grid grid-cols-1 gap-x-16 gap-y-6 md:grid-cols-2">
                    <div>
                        <p class="text-[13px] font-semibold text-[#7a6798]">Nama Produk</p>
                        <p class="mt-1 text-[30px] font-semibold leading-none text-[#3C1C5E]">{{ $product->product_name }}</p>
                    </div>
                    <div>
                        <p class="text-[13px] font-semibold text-[#7a6798]">Dibuat Oleh</p>
                        <p class="mt-1 text-[30px] font-semibold leading-none text-[#3C1C5E]">{{ $product->user?->name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[13px] font-semibold text-[#7a6798]">Merek</p>
                        <p class="mt-1 text-[30px] font-semibold leading-none text-[#3C1C5E]">{{ $product->brand?->brand_name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-[13px] font-semibold text-[#7a6798]">Kategori</p>
                        <p class="mt-1 text-[30px] font-semibold leading-none text-[#3C1C5E]">
                            {{ $product->category?->category_name ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[13px] font-semibold text-[#7a6798]">Jenis Kemasan</p>
                        <p class="mt-1 text-[30px] font-semibold leading-none text-[#3C1C5E]">
                            {{ $product->packagingType?->packaging_type ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[13px] font-semibold text-[#7a6798]">Ukuran Kemasan</p>
                        <p class="mt-1 text-[30px] font-semibold leading-none text-[#3C1C5E]">
                            {{ $product->packagingSize?->size ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>
        </article>
    </section>
@endsection
