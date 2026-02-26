@extends('landing.layouts.app', [
    'title' => 'Vending Machine Mini',
])

@push('style')
    <style>
        .carousel-viewport {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .carousel-track {
            display: flex;
            width: max-content;
            gap: 14px;
            will-change: transform;
            padding-left: 8px;
            padding-right: 8px;
        }

        .carousel-track.is-dragging {
            cursor: grabbing;
        }

        .carousel-viewport::-webkit-scrollbar {
            display: none;
        }

        .carousel-viewport::-webkit-scrollbar-thumb {
            background: rgba(92, 42, 148, 0.25);
            border-radius: 999px;
        }

        .cart-preview-clamp {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .animated-gradient {
            background: linear-gradient(270deg, #5c2a94, #a76ade, #5c2a94, #a76ade);
            background-size: 100% 100%;
            animation: gradientAnimation 5s ease infinite;
        }

        @keyframes gradientAnimation {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        .guide-priority-btn {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #5c2a94, #7d3bc5);
            color: #ffffff;
            border: 0;
            box-shadow: 0 10px 24px rgba(92, 42, 148, 0.34), 0 0 0 2px rgba(255, 255, 255, 0.55) inset;
            animation: guidePulse 1.8s ease-in-out infinite;
        }

        .guide-priority-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -55%;
            width: 45%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.38), transparent);
            transform: skewX(-20deg);
        }

        .guide-priority-btn:hover {
            transform: translateY(-1px) scale(1.01);
            box-shadow: 0 14px 28px rgba(92, 42, 148, 0.40), 0 0 0 2px rgba(255, 255, 255, 0.60) inset;
        }

        .guide-priority-btn:focus-visible {
            outline: 0;
            box-shadow: 0 0 0 4px rgba(125, 59, 197, 0.25), 0 12px 28px rgba(92, 42, 148, 0.34), 0 0 0 2px rgba(255, 255, 255, 0.6) inset;
        }

        @keyframes guidePulse {

            0%,
            100% {
                box-shadow: 0 10px 24px rgba(92, 42, 148, 0.34), 0 0 0 0 rgba(125, 59, 197, 0.32), 0 0 0 2px rgba(255, 255, 255, 0.55) inset;
            }

            50% {
                box-shadow: 0 12px 28px rgba(92, 42, 148, 0.40), 0 0 0 7px rgba(125, 59, 197, 0.1), 0 0 0 2px rgba(255, 255, 255, 0.55) inset;
            }
        }
    </style>
@endpush

@section('content')
    <div class="min-h-full bg-[#f7f3ff]">
        <div id="default-carousel" class="relative w-full px-4 sm:px-5 lg:px-6 pt-4 sm:pt-5 pb-5" data-carousel="slide">
            <!-- Carousel wrapper -->
            <div class="relative overflow-hidden rounded-base h-[132px] sm:h-[145px]">
                @foreach ($ads as $ad)
                    <div class="hidden duration-700 ease-in-out bg-cover shadow-[0_24px_60px_rgba(89,42,155,0.18)]"
                        data-carousel-item>
                        <img src="{{ asset('/image/' . $ad->image_url) }}"
                            class="absolute block -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2 w-full h-[190px] sm:h-[220px] lg:h-[360px]"
                            alt="...">
                    </div>
                @endforeach
            </div>
        </div>

        <div class="px-4 sm:px-5 lg:px-6 pb-5">
            <div class="flex gap-3 lg:gap-8 items-start">
                <div id="products-carousel" class="flex-1 min-w-0 space-y-[16px]">
                    @for ($i = 0; $i < 2; $i++)
                        <div class="carousel-viewport w-full">
                            <div class="carousel-track" data-carousel-track></div>
                        </div>
                    @endfor
                </div>

                @php
                    $callPhoneDisplay = $callCenterPhone ?? '0812-0000-0000';
                    $callWaDisplay = $callCenterWhatsapp ?? $callPhoneDisplay;
                    $callPhoneHref = preg_replace('/[^0-9+]/', '', $callPhoneDisplay) ?: '081200000000';
                    $callWaDigits = preg_replace('/[^0-9]/', '', $callWaDisplay) ?: '6281200000000';
                    if (str_starts_with($callWaDigits, '0')) {
                        $callWaDigits = '62' . substr($callWaDigits, 1);
                    }
                @endphp
            </div>
        </div>
    </div>

    <div id="checkout-float"
        class="fixed bottom-3 left-1/2 z-40 hidden w-[calc(100%-20px)] max-w-[480px] -translate-x-1/2 rounded-[14px] border border-[#e4d7f6] bg-white/95 p-3 shadow-[0_12px_30px_rgba(60,34,97,0.18)] backdrop-blur">
        <div id="checkout-expanded" class="flex items-center gap-3">
            <div class="flex-1">
                <div class="text-[11px] text-[#6d5a88]">Total Belanja</div>
                <div id="cart-total-floating" class="text-[15px] font-semibold text-[#2b1a43]">Rp 0</div>
                <div id="cart-items-count" class="mt-0.5 text-[11px] text-[#6d5a88]">0 item</div>
                <div id="cart-items-preview" class="cart-preview-clamp mt-1 text-[11px] leading-tight text-[#4d3a6f]"></div>
            </div>
            <div class="flex items-center gap-2">
                <button id="btn-clear-cart-floating" type="button" aria-label="Kosongkan keranjang"
                    class="h-[38px] min-w-[38px] rounded-full border border-[#d8c9f0] bg-white text-[#5c2a94] px-[10px] justify-center items-center">
                    <img src="{{ asset('assets/icons/landing/delete.svg') }}" alt="" class="h-[16px]">
                </button>
                <button id="btn-pay-floating"
                    class="h-[38px] min-w-[130px] rounded-full bg-[#5c2a94] px-4 text-[12px] font-semibold text-white disabled:cursor-not-allowed disabled:opacity-50">
                    Bayar Sekarang
                </button>
                <button id="btn-minimize-checkout-floating" type="button" aria-label="Minimalkan panel checkout"
                    class="h-[38px] min-w-[38px] rounded-full border border-[#d8c9f0] bg-white text-[#5c2a94] px-[8px] justify-center items-center">
                    <img src="{{ asset('assets/icons/landing/down.svg') }}" alt="" class="h-[20px]">
                </button>
            </div>
        </div>

        <div id="checkout-minimized" class="hidden flex items-center gap-2">
            <div class="flex min-w-0 flex-1 items-center justify-between border-[#d8c9f0] px-3 py-2 text-left">
                <div class="min-w-0">
                    <div class="text-[10px] leading-none text-[#6d5a88]">Total Belanja</div>
                    <div id="cart-total-minimized" class="mt-0.5 truncate text-[13px] font-semibold text-[#2b1a43]">Rp 0
                    </div>
                </div>
            </div>
            <button id="btn-pay-minimized"
                class="h-[38px] min-w-[88px] rounded-full bg-[#5c2a94] px-4 text-[12px] font-semibold text-white disabled:cursor-not-allowed disabled:opacity-50">
                Bayar
            </button>
            <button id="btn-expand-checkout-floating" type="button" aria-label="Perbesar panel checkout"
                class="h-[38px] min-w-[38px] rounded-full border border-[#d8c9f0] bg-white text-[#5c2a94] px-[8px] justify-center items-center">
                <img src="{{ asset('assets/icons/landing/up.svg') }}" alt="" class="h-[20px]">
            </button>
        </div>
    </div>

    <button id="btn-open-guide-floating" type="button" aria-label="Buka panduan pembelian"
        class="fixed bottom-16 right-4 z-40 inline-flex h-[42px] w-[42px] items-center justify-center gap-2 rounded-full bg-[#5c2a94] text-[12px] font-semibold text-white shadow-[0_10px_24px_rgba(92,42,148,0.35)]">
        <img src="{{ asset('assets/icons/landing/warning.svg') }}" alt="" class="h-[18px] brightness-0 invert">
    </button>

    <div id="modal-guide" class="fixed inset-0 z-50 hidden items-end justify-center bg-black/40 p-3">
        <div class="bg-white w-full max-w-[500px] rounded-[20px] p-[20px] shadow-[0_24px_60px_rgba(0,0,0,0.25)]">
            <div class="flex items-center justify-between gap-3">
                <div class="text-[18px] font-semibold text-[#2b1a43]">Panduan Pembelian</div>
                <button id="btn-close-guide" type="button" aria-label="Tutup panduan pembelian"
                    class="h-[30px] w-[30px] flex rounded-full border border-[#d2c6e6] text-[#5c2a94] items-center justify-center">
                    <img src="{{ asset('assets/icons/landing/close.svg') }}" alt="">
                </button>
            </div>
            <ol class="mt-[14px] space-y-[8px] text-[13px] text-[#4a3a66] list-decimal list-inside">
                <li>Pilih produk dan atur jumlah dengan tombol `+` atau `-`.</li>
                <li>Pastikan total pesanan sudah sesuai di panel Detail Pesanan.</li>
                <li>Tekan tombol Bayar Sekarang untuk menampilkan QRIS.</li>
                <li>Scan QRIS dari aplikasi pembayaran, lalu tunggu status terverifikasi.</li>
                <li>Jika gagal atau butuh bantuan, hubungi Call Center.</li>
            </ol>
            <div class="mt-[14px] rounded-[12px] bg-[#f8f3ff] p-3 text-[13px] text-[#4a3a66]">
                <div class="font-semibold text-[#3C1C5E]">Call Center</div>
                <div class="flex mt-2 items-center gap-2">
                    <img src="{{ asset('assets/icons/landing/phone.svg') }}" alt="" class="">
                    <p class="font-semibold text-[#5c2a94] items-center">{{ $callPhoneDisplay }}</p>
                </div>
                <div class="flex mt-2 items-center gap-2">
                    <img src="{{ asset('assets/icons/landing/whatsapp.svg') }}" alt="" class="">
                    <p class="font-semibold text-[#5c2a94] items-center">{{ $callWaDisplay }}</p>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-fail" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-[24px]">
        <div
            class="bg-white w-full max-w-[420px] rounded-[20px] p-[24px] text-center shadow-[0_24px_60px_rgba(0,0,0,0.25)]">
            <div class="mx-auto h-[72px] w-[72px] rounded-full bg-[#ffe6e6] flex items-center justify-center">
                <img src="{{ asset('assets/icons/landing/exclamation-mark.png') }}" alt="fail">
            </div>
            <div class="mt-[12px] text-[18px] font-semibold text-[#2b1a43]">Pembayaran Gagal</div>
            <div id="fail-message" class="mt-[6px] text-[12px] text-[#6b5a84]">Coba ulangi pemesanan.</div>
            <button id="btn-close-fail"
                class="mt-[18px] w-full h-[40px] rounded-full bg-[#5c2a94] text-white font-semibold">Tutup</button>
        </div>
    </div>

    <div id="modal-loading" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-[24px]">
        <div
            class="bg-white w-full max-w-[360px] rounded-[18px] p-[20px] text-center shadow-[0_24px_60px_rgba(0,0,0,0.25)]">
            <div class="mx-auto h-[48px] w-[48px] rounded-full border-4 border-[#e9dcf9] border-t-[#5c2a94] animate-spin">
            </div>
            <div class="mt-[12px] text-[14px] font-semibold text-[#2b1a43]">Memproses pembayaran...</div>
            <div class="mt-[4px] text-[12px] text-[#6b5a84]">Mohon tunggu sebentar.</div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        const products = @json($products);

        // Initializations
        const rupiah = (value) => new Intl.NumberFormat('id-ID').format(value);

        const cartKey = 'vm_demo_cart';
        const cart = JSON.parse(localStorage.getItem(cartKey) || '{}');

        const productsCarousel = document.getElementById('products-carousel');
        const cartLines = document.getElementById('cart-lines');
        const cartTotal = document.getElementById('cart-total');
        const cartTotalFloating = document.getElementById('cart-total-floating');
        const cartTotalMinimized = document.getElementById('cart-total-minimized');
        const cartItemsCount = document.getElementById('cart-items-count');
        const cartItemsPreview = document.getElementById('cart-items-preview');
        const checkoutFloat = document.getElementById('checkout-float');
        const checkoutExpanded = document.getElementById('checkout-expanded');
        const checkoutMinimized = document.getElementById('checkout-minimized');
        const btnMinimizeCheckout = document.getElementById('btn-minimize-checkout-floating');
        const btnExpandCheckout = document.getElementById('btn-expand-checkout-floating');
        const btnGuideFloating = document.getElementById('btn-open-guide-floating');
        const payButtons = [...document.querySelectorAll('#btn-pay, #btn-pay-floating, #btn-pay-minimized')];
        const clearCartButtons = [...document.querySelectorAll('#btn-clear-cart, #btn-clear-cart-floating')];
        const modalFail = document.getElementById('modal-fail');
        const btnCloseFail = document.getElementById('btn-close-fail');
        const modalLoading = document.getElementById('modal-loading');
        const modalGuide = document.getElementById('modal-guide');
        const btnOpenGuide = document.querySelector('#btn-open-guide, #btn-open-guide-floating');
        const btnCloseGuide = document.getElementById('btn-close-guide');
        const failMessage = document.getElementById('fail-message');
        const paymentPageTemplate = @json(route('landing.payment', ['saleId' => '__SALE_ID__']));
        const paymentContextKey = 'vm_payment_context';
        let isCheckoutMinimized = true;

        const defaultQrisImage = '{{ asset('assets/images/transaction/QR_code.svg') }}';

        // Cart
        const saveCart = () => {
            localStorage.setItem(cartKey, JSON.stringify(cart));
        };

        // Show Products
        const renderProducts = () => {
            if (!productsCarousel) return;
            const tracks = [...productsCarousel.querySelectorAll('[data-carousel-track]')];
            if (!tracks.length) return;
            const rows = Array.from({
                length: tracks.length
            }, () => []);
            products.forEach((product, index) => {
                rows[index % rows.length].push(product);
            });

            rows.forEach((rowItems, rowIndex) => {
                const track = tracks[rowIndex];
                if (!track) return;
                track.innerHTML = '';
                const shouldLoop = rowItems.length >= 4;
                track.dataset.loop = shouldLoop ? '1' : '0';
                rowItems.forEach((product) => {
                    const qty = cart[product.id]?.qty || 0;
                    const isOutOfStock = product.stock <= 0;
                    const isSelected = qty > 0;
                    const isMaxed = qty >= product.stock;
                    const card = document.createElement('div');
                    card.className =
                        `bg-white rounded-[16px] p-[10px] w-[140px] my-2 transition duration-200 ${
                        isSelected ? 'border-2 border-[#5c2a94] shadow-[0_10px_24px_rgba(92,42,148,0.18)]' : 'border border-[#e1d7f0] shadow-[0_6px_18px_rgba(60,34,97,0.08)]'
                    } ${isOutOfStock ? 'opacity-70 grayscale' : 'hover:-translate-y-1 hover:shadow-[0_14px_28px_rgba(92,42,148,0.16)]'}`;
                    card.innerHTML = `
                    <div class="bg-cover h-[120px] w-full rounded-[14px] bg-gradient-to-b from-[#f4efff] via-[#f9f6ff] to-white border border-[#ece4f7] flex items-center justify-center relative overflow-hidden"
                        style="background-image: url('${product.image}');">
                        ${isOutOfStock ? '<div class="absolute inset-0 bg-white/70 flex items-center justify-center text-[12px] font-semibold text-[#c0392b]">HABIS</div>' : ''}
                    </div>
                    <div class="flex flex-col mt-[12px] h-[95px]">
                        <div class="text-[13px] font-semibold text-[#2b1a43] leading-tight">${product.name}</div>
                        <div class="text-[12px] font-semibold text-[#6d5a88] mt-[2px]">Rp ${rupiah(product.price)}</div>
                        {{-- <div class="text-[11px] ${isOutOfStock ? 'text-[#c0392b]' : 'text-[#6b5a84]'} mt-[2px]">
                            ${isOutOfStock ? 'Stok habis' : `Stok ${product.stock}`}
                        </div> --}}
                        <div class="mt-auto w-full">
                            <a href="${product.detail_url}" class="btn-buy flex h-[30px] w-full items-center justify-center rounded-full bg-[#5c2a94] text-[12px] font-semibold text-white">
                                Beli
                            </a>
                        </div>
                        {{-- <div class="mt-[10px] flex items-center justify-between" data-product="${product.id}">
                            <button class="btn-min h-[28px] w-[28px] rounded-full border border-[#ceb9ee] text-[#5c2a94]">-</button>
                            <div class="qty text-[12px] font-semibold text-[#2b1a43]">${qty}</div>
                                <button class="btn-plus h-[28px] w-[28px] rounded-full bg-[#5c2a94] text-white ${(isOutOfStock || isMaxed) ? 'opacity-40 cursor-not-allowed' : ''}" ${(isOutOfStock || isMaxed) ? 'disabled' : ''}>+</button>
                        </div> --}}
                    </div>
                `;
                    track.appendChild(card);
                });

                if (shouldLoop) {
                    const baseCards = [...track.children];
                    baseCards.forEach((card) => {
                        track.appendChild(card.cloneNode(true));
                    });
                } else {
                    const viewport = track.closest('.carousel-viewport');
                    if (viewport) {
                        viewport.scrollLeft = 0;
                    }
                }
            });
        };

        // Show Products in Cart
        const renderCart = () => {
            let total = 0;
            let totalQty = 0;
            const previewItems = [];
            if (cartLines) {
                cartLines.innerHTML = '';
            }
            Object.keys(cart).forEach((id) => {
                const item = cart[id];
                if (!item || item.qty <= 0) return;
                const line = document.createElement('div');
                const lineTotal = item.qty * item.price;
                totalQty += Number(item.qty);
                total += lineTotal;
                previewItems.push(`${item.name} x${item.qty}`);
                line.className = 'grid grid-cols-[1fr_40px_80px]';
                line.innerHTML = `
                    <div>${item.name}</div>
                    <div class="text-center">${item.qty}</div>
                    <div class="text-right">${rupiah(lineTotal)},-</div>
                `;
                cartLines?.appendChild(line);
            });
            if (cartLines && !cartLines.children.length) {
                cartLines.innerHTML = '<div class="text-center text-[#6b5a84]">Keranjang kosong</div>';
            }
            if (cartTotal) {
                cartTotal.textContent = `Rp ${rupiah(total)}`;
            }
            if (cartTotalFloating) {
                cartTotalFloating.textContent = `Rp ${rupiah(total)}`;
            }
            if (cartTotalMinimized) {
                cartTotalMinimized.textContent = `Rp ${rupiah(total)}`;
            }
            if (cartItemsCount) {
                cartItemsCount.textContent = `${totalQty} item`;
            }
            if (cartItemsPreview) {
                const firstTwo = previewItems.slice(0, 2);
                const remain = Math.max(0, previewItems.length - firstTwo.length);
                const previewText = firstTwo.join(' • ');
                cartItemsPreview.textContent = remain > 0 ? `${previewText} • +${remain} lainnya` : previewText;
            }
            const hasCheckout = total > 0;
            payButtons.forEach((button) => {
                button.toggleAttribute('disabled', !hasCheckout);
                button.classList.toggle('opacity-50', !hasCheckout);
                button.classList.toggle('cursor-not-allowed', !hasCheckout);
            });
            checkoutFloat?.classList.toggle('hidden', !hasCheckout);
            clearCartButtons.forEach((button) => {
                button.classList.toggle('hidden', !hasCheckout);
            });
            btnGuideFloating?.classList.toggle('hidden', hasCheckout);

            if (!hasCheckout) {
                isCheckoutMinimized = true;
            }
            checkoutExpanded?.classList.toggle('hidden', isCheckoutMinimized);
            checkoutMinimized?.classList.toggle('hidden', !isCheckoutMinimized);
        };

        // Add or Remove Product Quantity
        const updateQty = (productId, delta) => {
            document.dispatchEvent(new Event('vm:pause-autoscroll'));
            const product = products.find((item) => item.id === productId);
            if (!product) return;
            const current = cart[productId]?.qty || 0;
            const next = Math.max(0, Math.min(product.stock, current + delta));
            if (next === 0) {
                delete cart[productId];
            } else {
                cart[productId] = {
                    name: product.name,
                    price: product.price,
                    qty: next
                };
            }
            saveCart();
            renderProducts();
            renderCart();
        };

        const getCheckoutPayload = () => {
            const items = Object.entries(cart)
                .filter(([, item]) => item && item.qty > 0)
                .map(([displayId, item]) => ({
                    product_display_id: Number(displayId),
                    qty: Number(item.qty),
                }));

            return {
                items,
            };
        };

        const requestCheckout = async () => {
            const payload = getCheckoutPayload();
            const response = await fetch('/api/transaction/checkout', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload),
            });

            const data = await response.json();
            if (!response.ok) {
                throw new Error(data?.error || data?.message || 'Checkout gagal.');
            }

            return data;
        };

        const openModal = (modal) => {
            modal?.classList.remove('hidden');
            modal?.classList.add('flex');
        };

        const closeModal = (modal) => {
            modal?.classList.add('hidden');
            modal?.classList.remove('flex');
        };

        productsCarousel?.addEventListener('click', (event) => {
            const target = event.target;
            const wrapper = target?.closest('[data-product]');
            const productId = wrapper?.getAttribute('data-product');
            if (!productId) return;
            if (target.classList.contains('btn-plus')) {
                updateQty(productId, 1);
            }
            if (target.classList.contains('btn-min')) {
                updateQty(productId, -1);
            }
        });

        const onCheckout = async () => {
            if (!Object.keys(cart).length) return;

            openModal(modalLoading);
            try {
                const result = await requestCheckout();
                const saleId = String(result?.data?.id || '');
                if (!saleId) {
                    throw new Error('Checkout berhasil tetapi ID transaksi tidak ditemukan.');
                }
                const qrUrl = result?.payment?.qr_url || result?.payment?.qr_string || defaultQrisImage;
                sessionStorage.setItem(paymentContextKey, JSON.stringify({
                    saleId,
                    qrUrl,
                }));

                closeModal(modalLoading);
                window.location.href = paymentPageTemplate.replace('__SALE_ID__', encodeURIComponent(saleId));
            } catch (error) {
                closeModal(modalLoading);
                if (failMessage) {
                    failMessage.textContent = error.message || 'Checkout gagal. Coba ulangi.';
                }
                openModal(modalFail);
            }
        };

        payButtons.forEach((button) => {
            button.addEventListener('click', onCheckout);
        });

        btnCloseFail?.addEventListener('click', () => {
            closeModal(modalFail);
        });

        clearCartButtons.forEach((button) => {
            button.addEventListener('click', () => {
                Object.keys(cart).forEach((key) => delete cart[key]);
                saveCart();
                renderProducts();
                renderCart();
            });
        });

        btnMinimizeCheckout?.addEventListener('click', () => {
            isCheckoutMinimized = true;
            renderCart();
        });

        btnExpandCheckout?.addEventListener('click', () => {
            isCheckoutMinimized = false;
            renderCart();
        });

        btnOpenGuide?.addEventListener('click', () => {
            openModal(modalGuide);
        });

        btnCloseGuide?.addEventListener('click', () => {
            closeModal(modalGuide);
        });

        modalGuide?.addEventListener('click', (event) => {
            if (event.target === modalGuide) {
                closeModal(modalGuide);
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeModal(modalGuide);
            }
        });

        renderProducts();
        renderCart();
    </script>

    <script>
        // Carousel Auto Scroll & Drag to Scroll
        const viewports = document.querySelectorAll('.carousel-viewport');

        viewports.forEach((viewport) => {
            const track = viewport.querySelector('.carousel-track');
            if (!track) return;

            let isDown = false;
            let startX = 0;
            let startScrollLeft = 0;
            let isPaused = false;
            let resumeTimer = null;
            const speed = 0.6;
            let direction = 1;
            let lastScrollLeft = viewport.scrollLeft;

            const pauseNow = () => {
                isPaused = true;
                resumeLater();
            };

            const updateDirection = () => {
                const current = viewport.scrollLeft;
                if (current === lastScrollLeft) return;
                direction = current > lastScrollLeft ? 1 : -1;
                lastScrollLeft = current;
            };

            const resumeLater = () => {
                if (resumeTimer) clearTimeout(resumeTimer);
                resumeTimer = setTimeout(() => {
                    isPaused = false;
                }, 1500);
            };

            const loopScroll = () => {
                const isLooping = track.dataset.loop === '1';
                if (!isLooping) {
                    requestAnimationFrame(loopScroll);
                    return;
                }

                const resetThreshold = track.scrollWidth / 2;
                if (!isPaused) {
                    viewport.scrollLeft += speed * direction;
                }

                if (viewport.scrollLeft >= resetThreshold) {
                    viewport.scrollLeft -= resetThreshold;
                } else if (viewport.scrollLeft <= 0) {
                    viewport.scrollLeft += resetThreshold;
                }

                requestAnimationFrame(loopScroll);
            };

            requestAnimationFrame(loopScroll);

            viewport.addEventListener('pointerdown', (event) => {
                if (event.target?.closest('.btn-plus, .btn-min, .btn-buy')) {
                    return;
                }
                isDown = true;
                startX = event.pageX;
                startScrollLeft = viewport.scrollLeft;
                isPaused = true;
                track.classList.add('is-dragging');
                viewport.setPointerCapture(event.pointerId);
            });

            viewport.addEventListener('pointermove', (event) => {
                if (!isDown) return;
                const walk = startX - event.pageX;
                viewport.scrollLeft = startScrollLeft + walk;
                updateDirection();
            });

            const endDrag = () => {
                isDown = false;
                track.classList.remove('is-dragging');
                resumeLater();
            };

            viewport.addEventListener('pointerup', endDrag);
            viewport.addEventListener('pointerleave', endDrag);
            viewport.addEventListener('mouseenter', () => {
                isPaused = true;
            });
            viewport.addEventListener('mouseleave', () => {
                resumeLater();
            });
            viewport.addEventListener('wheel', () => {
                isPaused = true;
                updateDirection();
                resumeLater();
            }, {
                passive: true
            });

            viewport.addEventListener('scroll', () => {
                if (isPaused || isDown) {
                    updateDirection();
                }
            });

            document.addEventListener('vm:pause-autoscroll', pauseNow);
        });
    </script>
@endpush
