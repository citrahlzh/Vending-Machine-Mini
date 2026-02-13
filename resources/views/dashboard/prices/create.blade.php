@extends('dashboard.layouts.app', [
    'title' => 'Tambah Data Harga',
])

@section('content')
    <section class="space-y-6 p-2">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard.prices.index') }}">
                    <img src="{{ asset('assets/icons/dashboard/back.svg') }}" alt="Kembali">
                </a>
                <h1 class="text-[28px] font-semibold leading-none text-[#3C1C5E]">Tambah Data Harga</h1>
            </div>

            <p class="mt-3 text-[18px] text-[#4F3970]">
                Halaman ini untuk menambah produk.
            </p>
        </div>

        <article class="rounded-[26px] border border-[#ddd2ef] bg-white p-10 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
            <form id="createPriceForm" class="space-y-4">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]" for="product_id">Produk</label>
                        <select id="product_id" name="product_id" required
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">
                            <option value="">Pilih produk</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]" for="price">Harga</label>
                        <input id="price" name="price" type="number" min="0" required
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none placeholder:text-[#b5a3ca] focus:border-[#6B3E93]"
                            placeholder="Rp0,00">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]" for="start_date">Tanggal
                            Mulai</label>
                        <input id="start_date" name="start_date" type="date" required
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]" for="end_date">Tanggal
                            Selesai</label>
                        <input id="end_date" name="end_date" type="date" required
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 pt-8 md:grid-cols-2">
                    <a href="{{ route('dashboard.prices.index') }}"
                        class="flex h-10 items-center justify-center rounded-lg border border-[#5A2F7E] bg-white text-[15px] font-semibold text-[#4B1F74] transition hover:bg-[#f8f4ff]">
                        Batal
                    </a>
                    <button id="submitCreatePrice" type="submit"
                        class="h-10 rounded-lg bg-[#5A2F7E] text-[15px] font-semibold text-white transition hover:bg-[#4B1F74]">
                        Simpan
                    </button>
                </div>
            </form>
        </article>
    </section>
@endsection

@push('script')
    <script>
        (() => {
            const form = document.getElementById('createPriceForm');
            const submitButton = document.getElementById('submitCreatePrice');
            if (!form || !submitButton) return;

            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                const payload = {
                    product_id: Number(document.getElementById('product_id').value),
                    price: Number(document.getElementById('price').value),
                    start_date: document.getElementById('start_date').value,
                    end_date: document.getElementById('end_date').value,
                    is_active: true,
                };

                submitButton.disabled = true;
                submitButton.textContent = 'Menyimpan...';

                try {
                    const response = await fetch('/api/price/store', {
                        method: 'POST',
                        headers: {
                            Accept: 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(payload),
                    });

                    const data = await response.json();
                    if (!response.ok) {
                        const errors = data?.errors ? Object.values(data.errors).flat() : [];
                        const message = errors.length > 0 ? errors[0] : (data?.message || 'Gagal menambah harga.');
                        throw new Error(message);
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data?.message || 'Data harga berhasil ditambahkan.',
                        timer: 1400,
                        showConfirmButton: false,
                    });

                    setTimeout(() => {
                        window.location.href = "{{ route('dashboard.prices.index') }}";
                    }, 1400);
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: error.message || 'Terjadi kesalahan saat menyimpan data.',
                    });
                } finally {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Simpan';
                }
            });
        })();
    </script>
@endpush
