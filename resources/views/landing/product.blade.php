@extends('landing.layouts.app', [
    'title' => $product['short_name'] . ' - Vending Machine Mini',
])

@section('content')
    <div class="min-h-screen bg-gradient-to-b from-[#f7f3ff] via-white to-[#f3f0ff] px-4 sm:px-5 lg:px-6 py-4 sm:py-5">
        <a href="{{ route('landing.index') }}" aria-label="Kembali ke beranda"
            class="inline-flex h-9 items-center gap-1 rounded-full border border-[#dbcdf2] bg-white px-3 text-[12px] font-semibold text-[#5c2a94]">
            <img src="{{ asset('assets/icons/landing/back.svg') }}" alt="" class="h-[15px]">
            <span>Kembali</span>
        </a>

        <div
            class="flex flex-col mt-2 rounded-[16px] border border-[#e7dcf8] bg-white p-5 shadow-[0_8px_20px_rgba(60,34,97,0.08)] h-full">
            <div
                class="w-full rounded-[14px] border border-[#ece4f7] bg-gradient-to-b from-[#f4efff] via-[#f9f6ff] to-white bg-cover bg-center">
                @if ($product['image'])
                    <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}"
                        class="h-full w-full object-contain rounded-[14px]">
                @endif
            </div>

            <div class="mt-4">
                <div class="flex items-start justify-between gap-3">
                    <h1 class="text-[18px] font-semibold leading-tight text-[#2b1a43]">{{ $product['name'] }}</h1>
                    <div
                        class="shrink-0 inline-flex items-center rounded-full px-3 py-1 text-[12px] font-medium {{ $product['stock'] > 0 ? 'bg-[#f4efff] text-[#5c2a94]' : 'bg-[#fdecea] text-[#c0392b]' }}">
                        Sisa stok: {{ $product['stock'] > 0 ? $product['stock'] : 'Habis' }}
                    </div>
                </div>
                <div class="mt-1 text-[18px] font-semibold text-[#6d5a88]">Rp
                    {{ number_format($product['price'], 0, ',', '.') }}</div>
                <p class="mt-4 text-[13px] leading-relaxed text-[#5f4c7f]">{{ $product['description'] }}</p>
            </div>

            <div class="mt-5 w-full">
                <div class="flex items-center gap-3">
                    <button id="btn-min" type="button"
                        class="h-10 w-10 rounded-full border border-[#ceb9ee] text-[20px] leading-none text-[#5c2a94] disabled:cursor-not-allowed disabled:opacity-40">-</button>
                    <div id="qty-display"
                        class="flex h-10 min-w-[50px] items-center justify-center rounded-[10px] border border-[#e1d7f0] bg-white text-[16px] font-semibold text-[#2b1a43]">
                        1
                    </div>
                    <button id="btn-plus" type="button"
                        class="h-10 w-10 rounded-full bg-[#5c2a94] text-[20px] leading-none text-white disabled:cursor-not-allowed disabled:bg-[#b9a3db] disabled:opacity-60">+</button>
                </div>
                <button id="btn-buy-now" type="button"
                    class="mt-3 h-12 w-full rounded-full bg-[#5c2a94] text-[16px] font-semibold text-white disabled:cursor-not-allowed disabled:opacity-50">
                    Beli Sekarang
                </button>
            </div>
            <div id="qty-note" class="mt-2 text-[12px] text-[#6b5a84]"></div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        const product = @json($product);
        const cartKey = 'vm_demo_cart';
        const btnMin = document.getElementById('btn-min');
        const btnPlus = document.getElementById('btn-plus');
        const btnBuyNow = document.getElementById('btn-buy-now');
        const qtyDisplay = document.getElementById('qty-display');
        const qtyNote = document.getElementById('qty-note');

        let qty = product.stock > 0 ? 1 : 0;

        const renderQty = () => {
            qtyDisplay.textContent = String(qty);
            btnMin.disabled = qty <= 1;
            btnPlus.disabled = qty >= Number(product.stock || 0);
            btnBuyNow.disabled = Number(product.stock || 0) <= 0;

            if (Number(product.stock || 0) <= 0) {
                if (qtyNote) qtyNote.textContent = 'Stok habis, produk tidak bisa dibeli.';
                return;
            }

            if (qtyNote) qtyNote.textContent = `Maksimal pembelian ${product.stock} item.`;
        };

        btnPlus?.addEventListener('click', () => {
            qty = Math.min(Number(product.stock || 0), qty + 1);
            renderQty();
        });

        btnMin?.addEventListener('click', () => {
            qty = Math.max(1, qty - 1);
            renderQty();
        });

        btnBuyNow?.addEventListener('click', () => {
            if (Number(product.stock || 0) <= 0) return;

            const cart = JSON.parse(localStorage.getItem(cartKey) || '{}');
            cart[product.id] = {
                name: product.name,
                price: Number(product.price || 0),
                qty: Number(qty || 1),
            };

            localStorage.setItem(cartKey, JSON.stringify(cart));
            window.location.href = "{{ route('landing.index') }}";
        });

        renderQty();
    </script>
@endpush
