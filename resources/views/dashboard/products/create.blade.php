@extends('dashboard.layouts.app', [
    'title' => 'Tambah Data Produk',
])

@section('content')
    <section class="space-y-6 p-2">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard.products.index') }}">
                    <img src="{{ asset('assets/icons/dashboard/back.svg') }}" alt="Kembali">
                </a>
                <h1 class="text-[28px] font-semibold leading-none text-[#3C1C5E]">Tambah Data Produk</h1>
            </div>

            <p class="mt-3 text-[18px] text-[#4F3970]">
                Halaman ini untuk menambah produk.
            </p>
        </div>

        <article class="rounded-[26px] border border-[#ddd2ef] bg-white p-10 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
            <form id="createProductForm" class="space-y-4" enctype="multipart/form-data">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]" for="product_name">Nama
                            Produk</label>
                        <input id="product_name" name="product_name" type="text" required
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none placeholder:text-[#b5a3ca] focus:border-[#6B3E93]"
                            placeholder="Masukkan nama produk Anda disini">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]" for="image_url">Foto Produk
                            <span class="font-normal">(JPG, PNG, JPEG, atau SVG)</span></label>
                        <input id="image_url" name="image_url" type="file" accept=".jpg,.jpeg,.png,.svg" required
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 py-2 text-[14px] text-[#3C1C5E] file:mr-3 file:rounded file:border-0 file:bg-[#efe6fb] file:px-3 file:py-1 file:text-[#4B1F74] focus:border-[#6B3E93]">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]" for="brand_id">Merek</label>
                        <select id="brand_id" name="brand_id" required
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">
                            <option value="">Pilih merek</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->brand_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]" for="category_id">Kategori</label>
                        <select id="category_id" name="category_id" required
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">
                            <option value="">Pilih kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->category_name }}</option>
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
                                <option value="{{ $type->id }}">{{ $type->packaging_type }}</option>
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
                                <option value="{{ $size->id }}">{{ $size->size }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 pt-8 md:grid-cols-2">
                    <a href="{{ route('dashboard.products.index') }}"
                        class="flex h-10 items-center justify-center rounded-lg border border-[#5A2F7E] bg-white text-[15px] font-semibold text-[#4B1F74] transition hover:bg-[#f8f4ff]">
                        Batal
                    </a>
                    <button id="submitCreateProduct" type="submit"
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
            const form = document.getElementById('createProductForm');
            const submitButton = document.getElementById('submitCreateProduct');
            if (!form || !submitButton) return;

            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                const formData = new FormData(form);
                submitButton.disabled = true;
                submitButton.textContent = 'Menyimpan...';

                try {
                    const response = await fetch('/api/product/store', {
                        method: 'POST',
                        headers: {
                            Accept: 'application/json',
                        },
                        body: formData,
                    });

                    const data = await response.json();
                    if (!response.ok) {
                        const errors = data?.errors ? Object.values(data.errors).flat() : [];
                        const message = errors.length > 0 ? errors[0] : (data?.message || 'Gagal menambah produk.');
                        throw new Error(message);
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data?.message || 'Produk berhasil ditambahkan.',
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
