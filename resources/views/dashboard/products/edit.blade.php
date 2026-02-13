@extends('dashboard.layouts.app', [
    'title' => 'Ubah Data Produk',
])

@section('content')
    <section class="space-y-6 p-2">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard.products.index') }}">
                    <img src="{{ asset('assets/icons/dashboard/back.svg') }}" alt="Kembali">
                </a>
                <h1 class="text-[28px] font-semibold leading-none text-[#3C1C5E]">Ubah Data Produk</h1>
            </div>

            <p class="mt-3 text-[18px] text-[#4F3970]">
                Halaman ini untuk mengubah produk.
            </p>
        </div>

        <article class="rounded-[26px] border border-[#ddd2ef] bg-white p-10 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
            <form id="editProductForm" class="space-y-4" enctype="multipart/form-data">
                <input id="productId" type="hidden" value="{{ $product->id }}">

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]" for="product_name">Nama
                            Produk</label>
                        <input id="product_name" name="product_name" type="text" required
                            value="{{ $product->product_name }}"
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none placeholder:text-[#b5a3ca] focus:border-[#6B3E93]"
                            placeholder="Masukkan nama produk Anda disini">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]" for="image_url">Foto Produk
                            <span class="font-normal">(JPG, PNG, JPEG, atau SVG)</span></label>
                        <input id="image_url" name="image_url" type="file" accept=".jpg,.jpeg,.png,.svg"
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 py-2 text-[14px] text-[#3C1C5E] file:mr-3 file:rounded file:border-0 file:bg-[#efe6fb] file:px-3 file:py-1 file:text-[#4B1F74] focus:border-[#6B3E93]">
                        <p class="mt-1 text-[12px] text-[#7a6798]">Kosongkan jika tidak ingin mengganti foto.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]" for="brand_id">Merek</label>
                        <select id="brand_id" name="brand_id" required
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">
                            <option value="">Pilih merek</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}" {{ $product->brand_id == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->brand_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]" for="category_id">Kategori</label>
                        <select id="category_id" name="category_id" required
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">
                            <option value="">Pilih kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->category_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]" for="packaging_type_id">Jenis
                            Kemasan</label>
                        <select id="packaging_type_id" name="packaging_type_id" required
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">
                            <option value="">Pilih jenis kemasan</option>
                            @foreach ($packagingTypes as $type)
                                <option value="{{ $type->id }}"
                                    {{ $product->packaging_type_id == $type->id ? 'selected' : '' }}>
                                    {{ $type->packaging_type }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]" for="packaging_size_id">Ukuran
                            Kemasan</label>
                        <select id="packaging_size_id" name="packaging_size_id" required
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">
                            <option value="">Pilih ukuran kemasan</option>
                            @foreach ($packagingSizes as $size)
                                <option value="{{ $size->id }}"
                                    {{ $product->packaging_size_id == $size->id ? 'selected' : '' }}>
                                    {{ $size->size }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 pt-8 md:grid-cols-2">
                    <a href="{{ route('dashboard.products.index') }}"
                        class="flex h-10 items-center justify-center rounded-lg border border-[#5A2F7E] bg-white text-[15px] font-semibold text-[#4B1F74] transition hover:bg-[#f8f4ff]">
                        Batal
                    </a>
                    <button id="submitEditProduct" type="submit"
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
            const form = document.getElementById('editProductForm');
            const submitButton = document.getElementById('submitEditProduct');
            const productId = document.getElementById('productId')?.value;
            if (!form || !submitButton || !productId) return;

            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                const formData = new FormData(form);
                const fileInput = document.getElementById('image_url');
                if (fileInput && fileInput.files && fileInput.files.length === 0) {
                    formData.delete('image_url');
                }

                submitButton.disabled = true;
                submitButton.textContent = 'Menyimpan...';

                try {
                    const response = await fetch(`/api/product/update/${productId}`, {
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
                            'Gagal memperbarui produk.');
                        throw new Error(message);
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data?.message || 'Produk berhasil diperbarui.',
                        timer: 1400,
                        showConfirmButton: false,
                    });

                    setTimeout(() => {
                        window.location.href = "{{ route('dashboard.products.index') }}";
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
