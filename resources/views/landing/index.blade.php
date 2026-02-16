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
            class="relative w-full px-6 sm:px-6 lg:px-[72px] pt-6 sm:pt-6 lg:pt-6 pb-[25px] lg:pb-[30px]"
            data-carousel="slide">
            <!-- Carousel wrapper -->
            <div class="relative overflow-hidden rounded-base h-[220px]">
                @foreach ($ads as $ad)
                    <div class="hidden duration-700 ease-in-out bg-cover shadow-[0_24px_60px_rgba(89,42,155,0.18)]"
                        data-carousel-item>
                        <img src="{{ asset('/image/' . $ad->image_url) }}"
                            class="absolute block -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2 w-full h-[220px] sm:h-[220px] lg:h-[360px]"
                            alt="...">
                    </div>
                @endforeach
            </div>
        </div>

        <div class="px-6 sm:px-6 lg:px-[72px]">
            <div class="flex gap-[18px] lg:gap-[32px] items-start">
                <div id="products-carousel" class="flex-1 min-w-0 space-y-[16px]">
                    @for ($i = 0; $i < 3; $i++)
                        <div class="carousel-viewport w-full">
                            <div class="carousel-track" data-carousel-track></div>
                        </div>
                    @endfor
                </div>

                <div
                    class="bg-white rounded-[15px] border border-[#eee5f9] shadow-[0_12px_30px_rgba(60,34,97,0.12)] p-[24px] lg:sticky lg:top-[24px] w-[340px] shrink-0">
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
                </div>
            </div>
        </div>
    </div>

    <div id="modal-pay" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-[24px]">
        <div class="bg-white w-full max-w-[420px] rounded-[20px] p-[24px] shadow-[0_24px_60px_rgba(0,0,0,0.25)]">
            <div class="text-[18px] font-semibold text-[#2b1a43] text-center">Pembayaran QRIS</div>
            <div
                class="mt-[16px] mx-auto h-[220px] w-[220px] rounded-[16px] border border-[#e9dcf9] bg-gradient-to-br from-[#f6f0ff] to-white flex items-center justify-center">
                <img id="qris-image" src="{{ asset('assets/images/transaction/QR_code.svg') }}" alt="qr-code">
            </div>
            <div id="payment-status-note" class="mt-[12px] text-center text-[12px] text-[#6b5a84]">Scan untuk membayar
            </div>
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
                <img src="{{ asset('assets/icons/landing/check-mark.png') }}" class="w-[40px] h-auto" alt="success">
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
        const btnPay = document.getElementById('btn-pay');
        const modalPay = document.getElementById('modal-pay');
        const btnCancel = document.getElementById('btn-cancel-pay');
        const btnSuccess = document.getElementById('btn-success');
        const modalSuccess = document.getElementById('modal-success');
        const btnCloseSuccess = document.getElementById('btn-close-success');
        const modalFail = document.getElementById('modal-fail');
        const btnCloseFail = document.getElementById('btn-close-fail');
        const modalLoading = document.getElementById('modal-loading');
        const failMessage = document.getElementById('fail-message');
        const qrisImage = document.getElementById('qris-image');
        const paymentStatusNote = document.getElementById('payment-status-note');

        const defaultQrisImage = '{{ asset('assets/images/transaction/QR_code.svg') }}';
        let currentSale = null;
        let paymentStatusTimer = null;
        let isStatusSyncing = false;

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
                    track.appendChild(card);
                });

                const baseCards = [...track.children];
                baseCards.forEach((card) => {
                    track.appendChild(card.cloneNode(true));
                });
            });
        };

        // Show Products in Cart
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

        const syncPaymentStatus = async (saleId) => {
            const response = await fetch(`/api/transaction/status/${saleId}`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                },
            });

            const data = await response.json();
            if (!response.ok) {
                throw new Error(data?.error || data?.message || 'Gagal sinkron status pembayaran.');
            }

            return data;
        };

        const cancelPayment = async (saleId) => {
            const response = await fetch(`/api/transaction/cancel/${saleId}`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                },
            });

            const data = await response.json();
            if (!response.ok) {
                throw new Error(data?.error || data?.message || 'Gagal membatalkan pembayaran.');
            }

            return data;
        };

        const applyDispenseResult = (sale) => {
            const successByDisplayId = {};
            (sale?.salesLines || []).forEach((line) => {
                if (line.status !== 'success') return;
                const key = String(line.product_display_id);
                successByDisplayId[key] = (successByDisplayId[key] || 0) + 1;
            });

            Object.entries(successByDisplayId).forEach(([displayId, successQty]) => {
                const product = products.find((item) => String(item.id) === displayId);
                if (product) {
                    product.stock = Math.max(0, Number(product.stock || 0) - Number(successQty));
                }

                if (!cart[displayId]) return;
                const newQty = Number(cart[displayId].qty || 0) - Number(successQty);
                if (newQty <= 0) {
                    delete cart[displayId];
                } else {
                    cart[displayId].qty = newQty;
                }
            });

            saveCart();
            renderProducts();
            renderCart();
        };

        const openModal = (modal) => {
            modal?.classList.remove('hidden');
            modal?.classList.add('flex');
        };

        const closeModal = (modal) => {
            modal?.classList.add('hidden');
            modal?.classList.remove('flex');
        };

        const stopPaymentStatusPolling = () => {
            if (paymentStatusTimer) {
                clearInterval(paymentStatusTimer);
                paymentStatusTimer = null;
            }
        };

        const resetPaymentState = () => {
            currentSale = null;
            stopPaymentStatusPolling();
            isStatusSyncing = false;
            if (qrisImage) {
                qrisImage.src = defaultQrisImage;
            }
        };

        const handleSaleStatus = (sale) => {
            if (!sale) return false;

            if (sale.status === 'paid') {
                stopPaymentStatusPolling();
                closeModal(modalPay);
                applyDispenseResult(sale);
                openModal(modalSuccess);
                resetPaymentState();
                return true;
            }

            if (sale.status === 'failed' || sale.status === 'expired') {
                stopPaymentStatusPolling();
                closeModal(modalPay);
                if (failMessage) {
                    failMessage.textContent = 'Pembayaran tidak berhasil. Silakan coba lagi.';
                }
                openModal(modalFail);
                resetPaymentState();
                return true;
            }

            return false;
        };

        const checkCurrentSaleStatus = async () => {
            if (!currentSale?.id || isStatusSyncing) return;
            isStatusSyncing = true;

            try {
                const result = await syncPaymentStatus(currentSale.id);
                const sale = result?.data;

                if (!handleSaleStatus(sale) && paymentStatusNote) {
                    paymentStatusNote.textContent = 'Menunggu pembayaran...';
                }
            } catch (error) {
                stopPaymentStatusPolling();
                closeModal(modalPay);
                if (failMessage) {
                    failMessage.textContent = error.message || 'Gagal mengecek status pembayaran.';
                }
                openModal(modalFail);
                resetPaymentState();
            } finally {
                isStatusSyncing = false;
            }
        };

        const startPaymentStatusPolling = () => {
            stopPaymentStatusPolling();
            paymentStatusTimer = setInterval(checkCurrentSaleStatus, 3000);
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

        btnPay?.addEventListener('click', async () => {
            if (!Object.keys(cart).length) return;

            openModal(modalLoading);
            try {
                const result = await requestCheckout();
                currentSale = result?.data || null;

                const qrUrl = result?.payment?.qr_url || result?.payment?.qr_string || defaultQrisImage;
                if (qrisImage) {
                    qrisImage.src = qrUrl;
                }
                if (paymentStatusNote) {
                    paymentStatusNote.textContent = 'Scan untuk membayar';
                }

                closeModal(modalLoading);
                openModal(modalPay);
                startPaymentStatusPolling();
            } catch (error) {
                closeModal(modalLoading);
                if (failMessage) {
                    failMessage.textContent = error.message || 'Checkout gagal. Coba ulangi.';
                }
                resetPaymentState();
                openModal(modalFail);
            }
        });

        btnCancel?.addEventListener('click', async () => {
            closeModal(modalPay);
            stopPaymentStatusPolling();

            if (!currentSale?.id) {
                resetPaymentState();
                return;
            }

            openModal(modalLoading);
            try {
                await cancelPayment(currentSale.id);
                closeModal(modalLoading);
                if (failMessage) {
                    failMessage.textContent = 'Pembayaran dibatalkan.';
                }
                openModal(modalFail);
            } catch (error) {
                closeModal(modalLoading);
                if (failMessage) {
                    failMessage.textContent = error.message || 'Gagal membatalkan pembayaran.';
                }
                openModal(modalFail);
            } finally {
                resetPaymentState();
            }
        });

        btnSuccess?.addEventListener('click', async () => {
            if (!currentSale?.id) return;

            closeModal(modalPay);
            openModal(modalLoading);
            try {
                const result = await syncPaymentStatus(currentSale.id);
                const sale = result?.data;

                closeModal(modalLoading);
                if (!handleSaleStatus(sale)) {
                    if (paymentStatusNote) {
                        paymentStatusNote.textContent = 'Pembayaran belum terdeteksi. Coba lagi sebentar.';
                    }
                    openModal(modalPay);
                    startPaymentStatusPolling();
                }
            } catch (error) {
                closeModal(modalLoading);
                if (failMessage) {
                    failMessage.textContent = error.message || 'Gagal mengecek status pembayaran.';
                }
                resetPaymentState();
                openModal(modalFail);
            }
        });

        btnCloseSuccess?.addEventListener('click', () => {
            closeModal(modalSuccess);
            resetPaymentState();
        });

        btnCloseFail?.addEventListener('click', () => {
            closeModal(modalFail);
            if (!currentSale?.id) {
                resetPaymentState();
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
                if (event.target?.closest('.btn-plus, .btn-min')) {
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
