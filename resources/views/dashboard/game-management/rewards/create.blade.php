@extends('dashboard.layouts.app', [
    'title' => 'Tambah Data Hadiah',
])

@section('content')
    <section class="space-y-6 p-2">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard.game-management.rewards.index') }}">
                    <img src="{{ asset('assets/icons/dashboard/back.svg') }}" alt="Kembali">
                </a>
                <h1 class="text-[28px] font-semibold leading-none text-[#3C1C5E]">Tambah Data Hadiah</h1>
            </div>

            <p class="mt-3 text-[18px] text-[#4F3970]">
                Halaman ini untuk menambah hadiah untuk permainan.
            </p>
        </div>

        <article class="rounded-[26px] border border-[#ddd2ef] bg-white p-10 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
            <form id="createRewardForm" class="space-y-4">

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]">Kode Hadiah</label>
                        <input name="code" type="text" required
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none placeholder:text-[#b5a3ca] focus:border-[#6B3E93]"
                            placeholder="Masukkan kode hadiah">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]">Nama Hadiah</label>
                        <input name="name" type="text" required
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none placeholder:text-[#b5a3ca] focus:border-[#6B3E93]"
                            placeholder="Masukkan nama hadiah">
                    </div>

                </div>

                <div>
                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]">Deskripsi</label>
                    <textarea name="description"
                        class="w-full rounded-lg border border-[#B596D8] px-3 py-2 text-[14px] text-[#3C1C5E] outline-none placeholder:text-[#b5a3ca] focus:border-[#6B3E93]"
                        placeholder="Masukkan deskripsi hadiah (opsional)"></textarea>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]">Tipe Hadiah</label>
                        <select id="typeForm" name="type" required
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">
                            <option value="">Pilih tipe hadiah</option>
                            <option value="product">Produk</option>
                            <option value="none">Zonk / Tidak ada hadiah</option>
                        </select>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]">Status</label>
                        <select name="is_active"
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>

                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]">Produk</label>
                        <select id="productDisplayForm" name="product_display_id" disabled
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93] disabled:opacity-50 disabled:bg-gray-200 disabled:cursor-not-allowed">
                            <option value="">Pilih produk</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">
                                    {{ $product->product->product_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]">Stok</label>
                        <input id="stockForm" name="stock" type="number" disabled
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93] disabled:opacity-50 disabled:bg-gray-200 disabled:cursor-not-allowed"
                            placeholder="Masukkan jumlah stok">
                    </div>

                </div>

                <div class="grid grid-cols-1 gap-4 pt-8 md:grid-cols-2">
                    <a href="{{ route('dashboard.game-management.rewards.index') }}"
                        class="flex h-10 items-center justify-center rounded-lg border border-[#5A2F7E] bg-white text-[15px] font-semibold text-[#4B1F74] transition hover:bg-[#f8f4ff]">
                        Batal
                    </a>

                    <button id="submitCreateReward" type="submit"
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
            const form = document.getElementById('createRewardForm');
            const submitButton = document.getElementById('submitCreateReward');

            if (!form || !submitButton) return;

            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                const formData = new FormData(form);

                submitButton.disabled = true;
                submitButton.textContent = 'Menyimpan...';

                try {
                    const response = await fetch('/api/reward/store', {
                        method: 'POST',
                        headers: {
                            Accept: 'application/json',
                        },
                        body: formData,
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        const errors = data?.errors ? Object.values(data.errors).flat() : [];
                        const message = errors.length > 0 ? errors[0] : (data?.message ||
                            'Gagal menambah hadiah.');
                        throw new Error(message);
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data?.message || 'Hadiah berhasil ditambahkan.',
                        timer: 1400,
                        showConfirmButton: false,
                    });

                    setTimeout(() => {
                        window.location.href =
                            "{{ route('dashboard.game-management.rewards.index') }}";
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

        document.addEventListener('DOMContentLoaded', () => {

            const type = document.getElementById('typeForm');
            const productDisplay = document.getElementById('productDisplayForm');
            const stock = document.getElementById('stockForm');

            type.addEventListener('change', () => {

                const isProduct = type.value === "product";

                productDisplay.disabled = !isProduct;
                stock.disabled = !isProduct;

                productDisplay.required = isProduct;
                stock.required = isProduct;

                if (!isProduct) {
                    productDisplay.value = "";
                    stock.value = "";
                }

            });

        });
    </script>
@endpush
