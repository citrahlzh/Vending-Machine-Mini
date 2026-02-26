@extends('landing.layouts.app', [
    'title' => 'Pembayaran - Vending Machine Mini',
])

@section('content')
    <div id="payment-screen"
        class="min-h-screen bg-gradient-to-b from-[#f7f3ff] via-white to-[#f3f0ff] px-4 py-4 sm:px-5 sm:py-5 lg:px-6">
        <div class="mx-auto flex min-h-[calc(100vh-2rem)] w-full max-w-[460px] items-center justify-center sm:min-h-[calc(100vh-2.5rem)]">
            <div class="w-full rounded-[20px] border border-[#e7dcf8] bg-white p-5 shadow-[0_12px_30px_rgba(60,34,97,0.1)] sm:p-6">
                <div class="text-center text-[22px] font-semibold text-[#2b1a43]">Pembayaran QRIS</div>
                <div id="payment-state" class="mt-1 text-center text-[13px] font-semibold text-[#6d5a88]">Menunggu pembayaran
                </div>

                <div class="mt-5 flex items-center justify-center">
                    <img id="qris-image" src="{{ asset('assets/images/transaction/QR_code.svg') }}" alt="qr-code"
                        class="h-[300px] w-[300px] max-w-full rounded-[16px] border border-[#e9dcf9] object-contain p-2 sm:h-[340px] sm:w-[340px]">
                </div>

                <div id="payment-status-note" class="mt-3 text-center text-[12px] text-[#6b5a84]">
                    Scan QRIS lalu tunggu verifikasi otomatis.
                </div>

                <div id="payment-error" class="mt-2 hidden rounded-[10px] bg-[#fff1f1] px-3 py-2 text-[12px] text-[#b83232]">
                </div>

                <div class="mt-5 grid grid-cols-1 gap-2">
                    <button id="btn-success" class="h-[42px] rounded-full bg-[#5c2a94] text-[13px] font-semibold text-white">
                        Saya Sudah Bayar
                    </button>
                    <button id="btn-cancel"
                        class="h-[42px] rounded-full border border-[#d6c7ee] text-[13px] font-semibold text-[#5c2a94]">
                        Batalkan Pembayaran
                    </button>
                    <a id="btn-back-home" href="{{ route('landing.index') }}"
                        class="hidden h-[42px] items-center justify-center rounded-full bg-[#5c2a94] text-[13px] font-semibold text-white">
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div id="final-screen"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-gradient-to-b from-[#f7f3ff] via-white to-[#f3f0ff] p-4">
        <div
            class="w-full max-w-[420px] rounded-[20px] border border-[#e7dcf8] bg-white p-6 text-center shadow-[0_24px_60px_rgba(0,0,0,0.2)]">
            <div id="final-icon-wrap" class="mx-auto flex h-[72px] w-[72px] items-center justify-center rounded-full">
                <img id="final-icon" src="{{ asset('assets/icons/landing/check-mark.png') }}" class="h-[40px] w-[40px]"
                    alt="status">
            </div>
            <div id="final-title" class="mt-3 text-[20px] font-semibold text-[#2b1a43]">Pembayaran Berhasil</div>
            <div id="final-message" class="mt-2 text-[13px] text-[#6b5a84]">Silakan ambil produk Anda.</div>
            <a href="{{ route('landing.index') }}"
                class="mt-5 inline-flex h-[42px] w-full items-center justify-center rounded-full bg-[#5c2a94] text-[13px] font-semibold text-white">
                Kembali ke Beranda
            </a>
        </div>
    </div>
@endsection

@push('script')
    <script>
        const saleId = @json((string) $saleId);
        const cartKey = 'vm_demo_cart';
        const paymentContextKey = 'vm_payment_context';
        const defaultQrisImage = '{{ asset('assets/images/transaction/QR_code.svg') }}';
        const successIcon = '{{ asset('assets/icons/landing/check-mark.png') }}';
        const failIcon = '{{ asset('assets/icons/landing/exclamation-mark.png') }}';

        const paymentScreen = document.getElementById('payment-screen');
        const finalScreen = document.getElementById('final-screen');
        const finalIconWrap = document.getElementById('final-icon-wrap');
        const finalIcon = document.getElementById('final-icon');
        const finalTitle = document.getElementById('final-title');
        const finalMessage = document.getElementById('final-message');
        const qrisImage = document.getElementById('qris-image');
        const paymentState = document.getElementById('payment-state');
        const paymentStatusNote = document.getElementById('payment-status-note');
        const paymentError = document.getElementById('payment-error');
        const btnSuccess = document.getElementById('btn-success');
        const btnCancel = document.getElementById('btn-cancel');
        const btnBackHome = document.getElementById('btn-back-home');

        let isStatusSyncing = false;
        let paymentStatusTimer = null;

        const clearPaymentContext = () => {
            sessionStorage.removeItem(paymentContextKey);
        };

        const showError = (message) => {
            if (!paymentError) return;
            paymentError.textContent = message;
            paymentError.classList.remove('hidden');
        };

        const hideError = () => {
            paymentError?.classList.add('hidden');
            if (paymentError) {
                paymentError.textContent = '';
            }
        };

        const setFinalState = (isSuccess, message) => {
            stopPaymentStatusPolling();
            paymentScreen?.classList.add('hidden');
            finalScreen?.classList.remove('hidden');
            finalScreen?.classList.add('flex');
            if (finalIcon) {
                finalIcon.src = isSuccess ? successIcon : failIcon;
            }
            if (finalIconWrap) {
                finalIconWrap.className =
                    `mx-auto flex h-[72px] w-[72px] items-center justify-center rounded-full ${isSuccess ? 'bg-[#dcffe7]' : 'bg-[#ffe6e6]'}`;
            }
            if (finalTitle) {
                finalTitle.textContent = isSuccess ? 'Pembayaran Berhasil' : 'Pembayaran Gagal';
            }
            if (finalMessage) {
                finalMessage.textContent = message;
            }
        };

        const syncPaymentStatus = async () => {
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

        const cancelPayment = async () => {
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

        const applyDispenseResultToCart = (sale) => {
            const cart = JSON.parse(localStorage.getItem(cartKey) || '{}');
            const successByDisplayId = {};
            const shouldApplyOptimistic = sale?.status === 'paid' && sale?.dispense_status === 'pending';

            (sale?.salesLines || []).forEach((line) => {
                if (!shouldApplyOptimistic && line.status !== 'success') return;
                const key = String(line.product_display_id);
                successByDisplayId[key] = (successByDisplayId[key] || 0) + 1;
            });

            Object.entries(successByDisplayId).forEach(([displayId, successQty]) => {
                if (!cart[displayId]) return;
                const newQty = Number(cart[displayId].qty || 0) - Number(successQty);
                if (newQty <= 0) {
                    delete cart[displayId];
                } else {
                    cart[displayId].qty = newQty;
                }
            });

            localStorage.setItem(cartKey, JSON.stringify(cart));
        };

        const handleSaleStatus = (sale) => {
            if (!sale) return false;

            if (sale.status === 'paid') {
                applyDispenseResultToCart(sale);
                clearPaymentContext();
                setFinalState(true, 'Silakan ambil produk Anda.');
                return true;
            }

            if (sale.status === 'failed' || sale.status === 'expired') {
                clearPaymentContext();
                setFinalState(false, 'Pembayaran tidak berhasil. Silakan coba lagi.');
                return true;
            }

            paymentStatusNote.textContent = 'Menunggu pembayaran...';
            return false;
        };

        const checkCurrentSaleStatus = async () => {
            if (isStatusSyncing) return;
            isStatusSyncing = true;
            try {
                hideError();
                const result = await syncPaymentStatus();
                handleSaleStatus(result?.data);
            } catch (error) {
                showError(error.message || 'Gagal mengecek status pembayaran.');
            } finally {
                isStatusSyncing = false;
            }
        };

        const startPaymentStatusPolling = () => {
            stopPaymentStatusPolling();
            paymentStatusTimer = setInterval(checkCurrentSaleStatus, 3000);
        };

        const stopPaymentStatusPolling = () => {
            if (paymentStatusTimer) {
                clearInterval(paymentStatusTimer);
                paymentStatusTimer = null;
            }
        };

        const hydratePaymentQr = () => {
            const contextRaw = sessionStorage.getItem(paymentContextKey);
            if (!contextRaw) {
                qrisImage.src = defaultQrisImage;
                return;
            }

            try {
                const context = JSON.parse(contextRaw);
                if (String(context?.saleId || '') !== String(saleId)) {
                    qrisImage.src = defaultQrisImage;
                    return;
                }
                qrisImage.src = context?.qrUrl || defaultQrisImage;
            } catch (_) {
                qrisImage.src = defaultQrisImage;
            }
        };

        btnSuccess?.addEventListener('click', async () => {
            await checkCurrentSaleStatus();
        });

        btnCancel?.addEventListener('click', async () => {
            try {
                hideError();
                await cancelPayment();
                clearPaymentContext();
                setFinalState(false, 'Pembayaran dibatalkan.');
            } catch (error) {
                showError(error.message || 'Gagal membatalkan pembayaran.');
            }
        });

        window.addEventListener('beforeunload', () => {
            stopPaymentStatusPolling();
        });

        hydratePaymentQr();
        checkCurrentSaleStatus();
        startPaymentStatusPolling();
    </script>
@endpush
