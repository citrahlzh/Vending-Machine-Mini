@extends('landing.layouts.app', [
    'title' => 'Vending Machine',
])

@section('content')
    <div class="min-h-screen bg-gradient-to-b from-[#f7f3ff] via-white to-[#f3f0ff]">
        <div id="default-carousel"
            class="relative w-full px-6 sm:px-10 lg:px-[72px] pt-[40px] sm:pt-[45px] lg:pt-[56px] pb-[32px] lg:pb-[40px]"
            data-carousel="slide">
            <!-- Carousel wrapper -->
            <div class="relative overflow-hidden rounded-base h-[220px]">
                @for ($i = 0; $i < 5; $i++)
                    <div class="hidden duration-700 ease-in-out bg-cover shadow-[0_24px_60px_rgba(89,42,155,0.18)]"
                        data-carousel-item>
                        <img src="{{ asset('assets/images/ads/ads.jpg') }}"
                            class="absolute block -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2 w-full h-[220px] sm:h-[220x] lg:h-[360px]"
                            alt="...">
                    </div>
                @endfor
            </div>
        </div>

        <div class="px-6 sm:px-10 lg:px-[72px] pb-[60px] lg:pb-[80px]">
            <div class="flex grid-cols-1 lg:grid-cols-[1fr_360px] gap-[18px] lg:gap-[32px] items-start">
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-[16px] sm:gap-[16px]">
                    @for ($i = 0; $i < 12; $i++)
                        <div
                            class="bg-white rounded-[15px] p-[14px] border border-[#ede8f6] shadow-[0_10px_24px_rgba(41,18,72,0.08)]">
                            <div class="bg-cover h-[120px] w-[100px] rounded-[15px] bg-gradient-to-b from-[#f1ecff] to-[#fff] border border-[#efe6ff] flex items-center justify-center"
                                style="background-image: url('{{ asset('assets/images/products/fruittea_blackcurrant.png') }}');">
                                {{-- <img src="{{ asset('assets/images/products/fruittea_blackcurrant.png') }}" alt="" class="object-contain"> --}}
                            </div>
                            <div class="mt-[12px]">
                                <div class="text-[13px] font-semibold text-[#2b1a43]">Fruit Tea Blackcurrant</div>
                                <div class="text-[12px] font-semibold text-[#6d5a88] mt-[2px]">Rp 5.000</div>
                                <div class="mt-[10px] flex items-center justify-between">
                                    <button id="min-qty"
                                        class="h-[28px] w-[28px] rounded-full border border-[#ceb9ee] text-[#5c2a94]">-</button>
                                    <div id="qty" class="text-[12px] font-semibold text-[#2b1a43]">0</div>
                                    <button id="plus-qty"
                                        class="h-[28px] w-[28px] rounded-full bg-[#5c2a94] text-white">+</button>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>

                <div
                    class="bg-white rounded-[15px] border border-[#eee5f9] shadow-[0_12px_30px_rgba(60,34,97,0.12)] p-[24px] lg:sticky lg:top-[24px]">
                    <div class="text-[18px] font-semibold text-[#2a1a42] text-center">Detail Pesanan</div>
                    <div class="mt-[16px] border-t border-[#efe6ff] pt-[16px]">
                        <div
                            class="grid grid-cols-[1fr_40px_80px] text-[12px] font-semibold text-[#5b4a7a] pb-[10px] border-b border-[#f1ecff]">
                            <div>Produk</div>
                            <div class="text-center">Qty</div>
                            <div class="text-right">Harga</div>
                        </div>
                        <div class="mt-[10px] space-y-[8px] text-[12px] text-[#2b1a43]">
                            <div class="grid grid-cols-[1fr_40px_80px]">
                                <div>Fruit Tea Blackcurrant</div>
                                <div class="text-center">1</div>
                                <div class="text-right">5.000,-</div>
                            </div>
                            <div class="grid grid-cols-[1fr_40px_80px]">
                                <div>Fruit Tea Freeze</div>
                                <div class="text-center">2</div>
                                <div class="text-right">10.000,-</div>
                            </div>
                            <div class="grid grid-cols-[1fr_40px_80px]">
                                <div>Fruit Tea Apel</div>
                                <div class="text-center">3</div>
                                <div class="text-right">15.000,-</div>
                            </div>
                        </div>
                        <div
                            class="mt-[16px] pt-[12px] border-t border-[#f1ecff] flex items-center justify-between text-[13px] font-semibold text-[#2b1a43]">
                            <div>Total</div>
                            <div>Rp 30.000,-</div>
                        </div>
                    </div>
                    <button id="btn-pay"
                        class="text-sm mt-[24px] w-full h-[44px] rounded-full bg-[#5c2a94] text-white font-semibold">
                        Bayar Sekarang
                    </button>
                </div>
            </div>
        </div>

        <div
            class="bg-gradient-to-r from-[#8b5ad6] via-[#a673e4] to-[#c78ae6] text-white px-6 sm:px-10 lg:px-[72px] py-[20px] lg:py-[24px]">
            <div class="flex items-center justify-between">
                <div>
                    <img src="{{ asset('assets/images/logo/logo_x9_white.webp') }}" alt="" class="w-10">
                </div>
                <div class="text-[16px] font-semibold">PT Manusia Solusi Terbaik</div>
            </div>
        </div>
    </div>

    <div id="modal-pay" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-[24px]">
        <div class="bg-white w-full max-w-[420px] rounded-[20px] p-[24px] shadow-[0_24px_60px_rgba(0,0,0,0.25)]">
            <div class="text-[18px] font-semibold text-[#2b1a43] text-center">Pembayaran QRIS</div>
            <div
                class="mt-[16px] mx-auto h-[220px] w-[220px] rounded-[16px] border border-[#e9dcf9] bg-gradient-to-br from-[#f6f0ff] to-white flex items-center justify-center">
                <div class="text-[12px] text-[#6b5a84]">QR Code</div>
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
            <div
                class="mx-auto h-[72px] w-[72px] rounded-full bg-[#e8dcff] flex items-center justify-center text-[#5c2a94] text-[24px] font-bold">
                OK</div>
            <div class="mt-[12px] text-[18px] font-semibold text-[#2b1a43]">Pembayaran Berhasil</div>
            <div class="mt-[6px] text-[12px] text-[#6b5a84]">Silakan ambil produk Anda.</div>
            <button id="btn-close-success"
                class="mt-[18px] w-full h-[40px] rounded-full bg-[#5c2a94] text-white font-semibold">Tutup</button>
        </div>
    </div>
@endsection

@push('script')
    <script>
        const btnPay = document.getElementById('btn-pay');
        const modalPay = document.getElementById('modal-pay');
        const btnCancel = document.getElementById('btn-cancel-pay');
        const btnSuccess = document.getElementById('btn-success');
        const modalSuccess = document.getElementById('modal-success');
        const btnCloseSuccess = document.getElementById('btn-close-success');

        btnPay?.addEventListener('click', () => {
            modalPay?.classList.remove('hidden');
            modalPay?.classList.add('flex');
        });

        btnCancel?.addEventListener('click', () => {
            modalPay?.classList.add('hidden');
            modalPay?.classList.remove('flex');
        });

        btnSuccess?.addEventListener('click', () => {
            modalPay?.classList.add('hidden');
            modalPay?.classList.remove('flex');
            modalSuccess?.classList.remove('hidden');
            modalSuccess?.classList.add('flex');
        });

        btnCloseSuccess?.addEventListener('click', () => {
            modalSuccess?.classList.add('hidden');
            modalSuccess?.classList.remove('flex');
        });
    </script>
@endpush
