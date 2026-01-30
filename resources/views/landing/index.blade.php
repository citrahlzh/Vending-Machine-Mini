@extends('landing.layouts.app', [
    'title' => 'Vending Machine',
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
    </style>
@endpush

@section('content')
    <div class="min-h-[calc(100vh-64px)] bg-gradient-to-b from-[#f7f3ff] via-white to-[#f3f0ff]">
        <div id="default-carousel"
            class="relative w-full px-6 sm:px-6 lg:px-[72px] pt-6 sm:pt-6 lg:pt-6 pb-[32px] lg:pb-[40px]"
            data-carousel="slide">
            <!-- Carousel wrapper -->
            <div class="relative overflow-hidden rounded-base h-[220px]">
                @for ($i = 0; $i < 5; $i++)
                    <div class="hidden duration-700 ease-in-out bg-cover shadow-[0_24px_60px_rgba(89,42,155,0.18)]"
                        data-carousel-item>
                        <img src="{{ asset('assets/images/ads/ads.jpg') }}"
                            class="absolute block -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2 w-full h-[220px] sm:h-[220px] lg:h-[360px]"
                            alt="...">
                    </div>
                @endfor
            </div>
        </div>

        <div class="px-6 sm:px-6 lg:px-[72px] pb-[60px] lg:pb-[80px]">
            <div class="flex gap-[18px] lg:gap-[32px] items-start">
                <div class="carousel-viewport w-full">
                    <div id="products-track" class="carousel-track"></div>
                </div>

                <div
                    class="bg-white rounded-[15px] border border-[#eee5f9] shadow-[0_12px_30px_rgba(60,34,97,0.12)] p-[24px] lg:sticky lg:top-[24px]] sm:w-[400px]">
                    <div class="text-[18px] font-semibold text-[#2a1a42] text-center">Detail Pesanan</div>
                    <div class="mt-[16px] border-t border-[#efe6ff] pt-[16px]">
                        <div
                            class="grid grid-cols-[1fr_40px_80px] text-[12px] font-semibold text-[#5b4a7a] pb-[10px] border-b border-[#f1ecff]">
                            <div>Produk</div>
                            <div class="text-center">Qty</div>
                            <div class="text-right">Harga</div>
                        </div>
                        <div id="cart-lines" class="mt-[10px] space-y-[8px] text-[12px] text-[#2b1a43]"></div>
                        <div
                            class="mt-[16px] pt-[12px] border-t border-[#f1ecff] flex items-center justify-between text-[13px] font-semibold text-[#2b1a43]">
                            <div>Total</div>
                            <div id="cart-total">Rp 0</div>
                        </div>
                    </div>
                    <button id="btn-pay"
                        class="text-sm mt-[24px] w-full h-[44px] rounded-full bg-[#5c2a94] text-white font-semibold">
                        Bayar Sekarang
                    </button>
                    <div class="mt-[20px] flex items-center justify-between text-[11px] text-[#6b5a84]">
                        <div>Simulasi gagal:</div>
                        <label class="flex items-center gap-2">
                            <input id="toggle-fail" type="checkbox" class="h-4 w-4">
                            <span>Aktifkan</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="modal-pay" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-[24px]">
        <div class="bg-white w-full max-w-[420px] rounded-[20px] p-[24px] shadow-[0_24px_60px_rgba(0,0,0,0.25)]">
            <div class="text-[18px] font-semibold text-[#2b1a43] text-center">Pembayaran QRIS</div>
            <div
                class="mt-[16px] mx-auto h-[220px] w-[220px] rounded-[16px] border border-[#e9dcf9] bg-gradient-to-br from-[#f6f0ff] to-white flex items-center justify-center">
                {{-- <div class="text-[12px] text-[#6b5a84]">QR Code</div> --}}
                <img src="{{ asset('assets/images/transaction/QR_code.svg') }}" alt="qr-code">
            </div>
            <div class="mt-[12px] text-center text-[12px] text-[#6b5a84]">Scan untuk membayar</div>
            <div class="mt-[20px] flex gap-[10px]">
                <button id="btn-cancel-pay"
                    class="w-full h-[40px] rounded-full border border-[#d6c7ee] text-[#5c2a94] font-semibold">Batal</button>
                <button id="btn-success" class="w-full h-[40px] rounded-full bg-[#5c2a94] text-white font-semibold">Sudah
                    Bayar</button>
            </div>
        </div>
    </div>

    <div id="modal-success" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-[24px]">
        <div
            class="bg-white w-full max-w-[420px] rounded-[20px] p-[24px] text-center shadow-[0_24px_60px_rgba(0,0,0,0.25)]">
            <div class="mx-auto h-[72px] w-[72px] rounded-full bg-[#dcffe7] flex items-center justify-center">
                <img src="{{ asset('assets/icons/check-mark.png') }}" class="w-[40px] h-auto" alt="success">
            </div>
            <div class="mt-[12px] text-[18px] font-semibold text-[#2b1a43]">Pembayaran Berhasil</div>
            <div class="mt-[6px] text-[12px] text-[#6b5a84]">Silakan ambil produk Anda.</div>
            <button id="btn-close-success"
                class="mt-[18px] w-full h-[40px] rounded-full bg-[#5c2a94] text-white font-semibold">Tutup</button>
        </div>
    </div>

    <div id="modal-fail" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-[24px]">
        <div
            class="bg-white w-full max-w-[420px] rounded-[20px] p-[24px] text-center shadow-[0_24px_60px_rgba(0,0,0,0.25)]">
            <div class="mx-auto h-[72px] w-[72px] rounded-full bg-[#ffe6e6] flex items-center justify-center">
                <img src="{{ asset('assets/icons/exclamation-mark.png') }}" alt="fail">
            </div>
            <div class="mt-[12px] text-[18px] font-semibold text-[#2b1a43]">Pembayaran Gagal</div>
            <div class="mt-[6px] text-[12px] text-[#6b5a84]">Coba ulangi pemesanan.</div>
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
        const products = [
            // Tea / Drinks
            {
                id: 'fruit-tea-blackcurrant',
                name: 'Fruit Tea Blackcurrant',
                price: 5000,
                stock: 12,
                image: '{{ asset('assets/images/products/fruittea_blackcurrant.png') }}'
            },
            {
                id: 'fruit-tea-freeze',
                name: 'Fruit Tea Freeze',
                price: 7000,
                stock: 9,
                image: '{{ asset('assets/images/products/fruittea_blackcurrant.png') }}'
            },
            {
                id: 'fruit-tea-apel',
                name: 'Fruit Tea Apel',
                price: 4500,
                stock: 7,
                image: '{{ asset('assets/images/products/fruittea_blackcurrant.png') }}'
            },
            {
                id: 'fruit-tea-lemon',
                name: 'Fruit Tea Lemon',
                price: 6000,
                stock: 10,
                image: '{{ asset('assets/images/products/fruittea_blackcurrant.png') }}'
            },
            {
                id: 'fruit-tea-peach',
                name: 'Fruit Tea Peach',
                price: 6500,
                stock: 8,
                image: '{{ asset('assets/images/products/fruittea_blackcurrant.png') }}'
            },
            {
                id: 'fruit-tea-lychee',
                name: 'Fruit Tea Lychee',
                price: 5500,
                stock: 11,
                image: '{{ asset('assets/images/products/fruittea_blackcurrant.png') }}'
            },
            {
                id: 'fruit-tea-mango',
                name: 'Fruit Tea Mango',
                price: 6000,
                stock: 0,
                image: '{{ asset('assets/images/products/fruittea_blackcurrant.png') }}'
            },
            {
                id: 'fruit-tea-strawberry',
                name: 'Fruit Tea Strawberry',
                price: 6500,
                stock: 6,
                image: '{{ asset('assets/images/products/fruittea_blackcurrant.png') }}'
            },

            // Coffee
            {
                id: 'coffee-latte',
                name: 'Coffee Latte',
                price: 8000,
                stock: 10,
                image: '{{ asset('assets/images/products/fruittea_blackcurrant.png') }}'
            },
            {
                id: 'coffee-americano',
                name: 'Coffee Americano',
                price: 7500,
                stock: 9,
                image: '{{ asset('assets/images/products/fruittea_blackcurrant.png') }}'
            },
            {
                id: 'coffee-mocha',
                name: 'Coffee Mocha',
                price: 9000,
                stock: 7,
                image: '{{ asset('assets/images/products/fruittea_blackcurrant.png') }}'
            },
            {
                id: 'coffee-cappuccino',
                name: 'Coffee Cappuccino',
                price: 9000,
                stock: 8,
                image: '{{ asset('assets/images/products/fruittea_blackcurrant.png') }}'
            },
            {
                id: 'coffee-vanilla',
                name: 'Coffee Vanilla',
                price: 8500,
                stock: 6,
                image: '{{ asset('assets/images/products/fruittea_blackcurrant.png') }}'
            },
            {
                id: 'coffee-hazelnut',
                name: 'Coffee Hazelnut',
                price: 8500,
                stock: 5,
                image: '{{ asset('assets/images/products/fruittea_blackcurrant.png') }}'
            },
            {
                id: 'coffee-caramel',
                name: 'Coffee Caramel',
                price: 9000,
                stock: 4,
                image: '{{ asset('assets/images/products/fruittea_blackcurrant.png') }}'
            },
            {
                id: 'coffee-black',
                name: 'Coffee Black',
                price: 7000,
                stock: 12,
                image: '{{ asset('assets/images/products/fruittea_blackcurrant.png') }}'
            },

            // Snacks
            {
                id: 'snack-chips-bbq',
                name: 'Chips BBQ',
                price: 6000,
                stock: 15,
                image: '{{ asset('assets/images/products/fruittea_blackcurrant.png') }}'
            },
            {
                id: 'snack-chips-seaweed',
                name: 'Chips Seaweed',
                price: 6000,
                stock: 13,
                image: '{{ asset('assets/images/products/fruittea_blackcurrant.png') }}'
            },
            {
                id: 'snack-wafer-choco',
                name: 'Wafer Choco',
                price: 5000,
                stock: 20,
                image: '{{ asset('assets/images/products/fruittea_blackcurrant.png') }}'
            },
            {
                id: 'snack-wafer-vanilla',
                name: 'Wafer Vanilla',
                price: 5000,
                stock: 18,
                image: '{{ asset('assets/images/products/fruittea_blackcurrant.png') }}'
            },
            {
                id: 'snack-biscuit-butter',
                name: 'Biscuit Butter',
                price: 4500,
                stock: 16,
                image: '{{ asset('assets/images/products/fruittea_blackcurrant.png') }}'
            },
            {
                id: 'snack-biscuit-choco',
                name: 'Biscuit Choco',
                price: 4500,
                stock: 0,
                image: '{{ asset('assets/images/products/fruittea_blackcurrant.png') }}'
            },
            {
                id: 'snack-nuts-honey',
                name: 'Nuts Honey',
                price: 7000,
                stock: 9,
                image: '{{ asset('assets/images/products/fruittea_blackcurrant.png') }}'
            },
            {
                id: 'snack-nuts-salted',
                name: 'Nuts Salted',
                price: 7000,
                stock: 8,
                image: '{{ asset('assets/images/products/fruittea_blackcurrant.png') }}'
            },
        ];

        const rupiah = (value) => new Intl.NumberFormat('id-ID').format(value);

        const cartKey = 'vm_demo_cart';
        const cart = JSON.parse(localStorage.getItem(cartKey) || '{}');

        const productsTrack = document.getElementById('products-track');
        const cartLines = document.getElementById('cart-lines');
        const cartTotal = document.getElementById('cart-total');
        const btnPay = document.getElementById('btn-pay');
        const toggleFail = document.getElementById('toggle-fail');
        const modalPay = document.getElementById('modal-pay');
        const btnCancel = document.getElementById('btn-cancel-pay');
        const btnSuccess = document.getElementById('btn-success');
        const modalSuccess = document.getElementById('modal-success');
        const btnCloseSuccess = document.getElementById('btn-close-success');
        const modalFail = document.getElementById('modal-fail');
        const btnCloseFail = document.getElementById('btn-close-fail');
        const modalLoading = document.getElementById('modal-loading');

        const saveCart = () => {
            localStorage.setItem(cartKey, JSON.stringify(cart));
        };

        const renderProducts = () => {
            if (!productsTrack) return;
            productsTrack.innerHTML = '';
            products.forEach((product) => {
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
                    <div class="mt-[12px]">
                        <div class="text-[13px] font-semibold text-[#2b1a43] leading-tight">${product.name}</div>
                        <div class="text-[12px] font-semibold text-[#6d5a88] mt-[2px]">Rp ${rupiah(product.price)}</div>
                        <div class="text-[11px] ${isOutOfStock ? 'text-[#c0392b]' : 'text-[#6b5a84]'} mt-[2px]">
                            ${isOutOfStock ? 'Stok habis' : `Stok ${product.stock}`}
                        </div>
                        <div class="mt-[10px] flex items-center justify-between" data-product="${product.id}">
                            <button class="btn-min h-[28px] w-[28px] rounded-full border border-[#ceb9ee] text-[#5c2a94]">-</button>
                            <div class="qty text-[12px] font-semibold text-[#2b1a43]">${qty}</div>
                                <button class="btn-plus h-[28px] w-[28px] rounded-full bg-[#5c2a94] text-white ${(isOutOfStock || isMaxed) ? 'opacity-40 cursor-not-allowed' : ''}" ${(isOutOfStock || isMaxed) ? 'disabled' : ''}>+</button>
                        </div>
                    </div>
                `;
                productsTrack.appendChild(card);
            });
        };

        const renderCart = () => {
            if (!cartLines || !cartTotal) return;
            cartLines.innerHTML = '';
            let total = 0;
            Object.keys(cart).forEach((id) => {
                const item = cart[id];
                if (!item || item.qty <= 0) return;
                const line = document.createElement('div');
                const lineTotal = item.qty * item.price;
                total += lineTotal;
                line.className = 'grid grid-cols-[1fr_40px_80px]';
                line.innerHTML = `
                    <div>${item.name}</div>
                    <div class="text-center">${item.qty}</div>
                    <div class="text-right">${rupiah(lineTotal)},-</div>
                `;
                cartLines.appendChild(line);
            });
            if (!cartLines.children.length) {
                cartLines.innerHTML = '<div class="text-center text-[#6b5a84]">Keranjang kosong</div>';
            }
            cartTotal.textContent = `Rp ${rupiah(total)}`;
            btnPay.disabled = total === 0;
            btnPay.classList.toggle('opacity-50', total === 0);
            btnPay.classList.toggle('cursor-not-allowed', total === 0);
        };

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

        const fakeCheckout = () =>
            new Promise((resolve) => {
                const shouldFail = toggleFail?.checked;
                setTimeout(() => {
                    if (shouldFail) {
                        resolve({
                            ok: false
                        });
                    } else {
                        resolve({
                            ok: Math.random() > 0.2
                        });
                    }
                }, 1200);
            });

        const openModal = (modal) => {
            modal?.classList.remove('hidden');
            modal?.classList.add('flex');
        };

        const closeModal = (modal) => {
            modal?.classList.add('hidden');
            modal?.classList.remove('flex');
        };

        productsTrack?.addEventListener('click', (event) => {
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

        btnPay?.addEventListener('click', () => {
            openModal(modalPay);
        });

        btnCancel?.addEventListener('click', () => {
            closeModal(modalPay);
        });

        btnSuccess?.addEventListener('click', async () => {
            closeModal(modalPay);
            openModal(modalLoading);
            const result = await fakeCheckout();
            closeModal(modalLoading);
            if (result.ok) {
                openModal(modalSuccess);
                Object.keys(cart).forEach((key) => delete cart[key]);
                saveCart();
                renderProducts();
                renderCart();
            } else {
                openModal(modalFail);
            }
        });

        btnCloseSuccess?.addEventListener('click', () => {
            closeModal(modalSuccess);
        });

        btnCloseFail?.addEventListener('click', () => {
            closeModal(modalFail);
        });

        renderProducts();
        renderCart();
    </script>

    <script>
        const viewports = document.querySelectorAll('.carousel-viewport');

        viewports.forEach((viewport) => {
            const track = viewport.querySelector('.carousel-track');
            if (!track) return;

            const cards = [...track.children];
            for (const card of cards) {
                track.appendChild(card.cloneNode(true));
            }

            const resetThreshold = track.scrollWidth / 2;
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
