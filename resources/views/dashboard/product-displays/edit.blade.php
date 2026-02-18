@extends('dashboard.layouts.app', [
    'title' => 'Ubah Data Stok dan Slot',
])

@section('content')
    <section class="space-y-6 p-2">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard.product-displays.index') }}">
                    <img src="{{ asset('assets/icons/dashboard/back.svg') }}" alt="Kembali">
                </a>
                <h1 class="text-[28px] font-semibold leading-none text-[#3C1C5E]">Ubah Data Stok dan Slot</h1>
            </div>

            <p class="mt-3 text-[18px] text-[#4F3970]">
                Halaman ini untuk mengubah data penataan produk, stok dan juga slot.
            </p>
        </div>

        <article class="rounded-[26px] border border-[#ddd2ef] bg-white p-10 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
            <form id="editProductDisplayForm" class="space-y-4">
                <input id="productDisplayId" type="hidden" value="{{ $productDisplay->id }}">

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]" for="product_id">Produk</label>
                        <select id="product_id" name="product_id" required
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">
                            <option value="">Pilih produk</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}"
                                    {{ $productDisplay->product_id == $product->id ? 'selected' : '' }}>
                                    {{ $product->product_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]" for="price_id">Harga</label>
                        <select id="price_id" name="price_id" required
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">
                            <option value="">Pilih harga</option>
                            @foreach ($prices as $price)
                                <option value="{{ $price->id }}"
                                    data-product-id="{{ $price->product_id }}"
                                    {{ $productDisplay->price_id == $price->id ? 'selected' : '' }}>
                                    {{ $price->product?->product_name ?? 'Produk' }} -
                                    Rp{{ number_format((int) $price->price, 0, ',', '.') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]" for="cell_id">Sel</label>
                        <select id="cell_id" name="cell_id" required
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">
                            <option value="">Pilih sel</option>
                            @foreach ($cells as $cell)
                                <option value="{{ $cell->id }}"
                                    {{ $productDisplay->cell_id == $cell->id ? 'selected' : '' }}>
                                    {{ $cell->code }} (stok {{ $cell->qty_current }}/{{ $cell->capacity }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]" for="status">Status</label>
                        <select id="status" name="status" required
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">
                            <option value="active" {{ $productDisplay->status === 'active' ? 'selected' : '' }}>Aktif
                            </option>
                            <option value="inactive" {{ $productDisplay->status === 'inactive' ? 'selected' : '' }}>Tidak
                                Aktif</option>
                            <option value="discontinued"
                                {{ $productDisplay->status === 'discontinued' ? 'selected' : '' }}>Dihentikan</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 pt-8 md:grid-cols-2">
                    <a href="{{ route('dashboard.product-displays.index') }}"
                        class="flex h-10 items-center justify-center rounded-lg border border-[#5A2F7E] bg-white text-[15px] font-semibold text-[#4B1F74] transition hover:bg-[#f8f4ff]">
                        Batal
                    </a>
                    <button id="submitEditProductDisplay" type="submit"
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
            const form = document.getElementById('editProductDisplayForm');
            const submitButton = document.getElementById('submitEditProductDisplay');
            const productDisplayId = document.getElementById('productDisplayId')?.value;
            const productIdInput = document.getElementById('product_id');
            const priceIdInput = document.getElementById('price_id');
            if (!form || !submitButton || !productDisplayId) return;

            const syncPriceOptions = () => {
                if (!productIdInput || !priceIdInput) return;

                const selectedProductId = productIdInput.value;
                const options = [...priceIdInput.options].slice(1);
                let hasMatch = false;

                options.forEach((option) => {
                    const isMatch = !!selectedProductId && option.dataset.productId === selectedProductId;
                    option.hidden = !isMatch;
                    option.disabled = !isMatch;
                    if (isMatch) hasMatch = true;
                });

                if (!hasMatch) {
                    priceIdInput.value = '';
                    return;
                }

                const selectedOption = priceIdInput.selectedOptions[0];
                if (!selectedOption || selectedOption.dataset.productId !== selectedProductId) {
                    priceIdInput.value = '';
                }
            };

            productIdInput?.addEventListener('change', syncPriceOptions);
            syncPriceOptions();

            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                const payload = {
                    product_id: Number(document.getElementById('product_id').value),
                    price_id: Number(document.getElementById('price_id').value),
                    cell_id: Number(document.getElementById('cell_id').value),
                    status: document.getElementById('status').value,
                };

                if (!payload.product_id || !payload.price_id || !payload.cell_id || !payload.status) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Data belum lengkap',
                        text: 'Mohon isi data dengan benar.',
                    });
                    return;
                }

                submitButton.disabled = true;
                submitButton.textContent = 'Menyimpan...';

                try {
                    const response = await fetch(`/api/product-display/update/${productDisplayId}`, {
                        method: 'POST',
                        headers: {
                            Accept: 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(payload),
                    });

                    const data = await response.json().catch(() => ({}));
                    if (!response.ok) {
                        const errors = data?.errors ? Object.values(data.errors).flat() : [];
                        const message = errors.length > 0 ? errors[0] : (data?.message ||
                            'Gagal memperbarui data stok dan slot.');
                        throw new Error(message);
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data?.message || 'Data stok dan slot berhasil diperbarui.',
                        timer: 1400,
                        showConfirmButton: false,
                    });

                    setTimeout(() => {
                        window.location.href = "{{ route('dashboard.product-displays.index') }}";
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
