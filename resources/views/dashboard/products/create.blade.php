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
                <h1 class="text-[28px] font-semibold leading-none text-[#5E1C3D]">Tambah Data Produk</h1>
            </div>

            <p class="mt-3 text-[18px] text-[#703967]">
                Halaman ini untuk menambah produk.
            </p>
        </div>

        <article class="rounded-[26px] border border-[#efd2ea] bg-white p-10 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
            <form id="createProductForm" class="space-y-4" enctype="multipart/form-data">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#5E1C3D]" for="product_name">Nama
                            Produk</label>
                        <input id="product_name" name="product_name" type="text" required
                            class="h-10 w-full rounded-lg border border-[#d896c4] px-3 text-[14px] text-[#5E1C3D] outline-none placeholder:text-[#caa3c0] focus:border-[#933e77]"
                            placeholder="Masukkan nama produk Anda disini">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#5E1C3D]" for="image_url">Foto Produk
                            <span class="font-normal">(JPG, PNG, JPEG, atau SVG)</span></label>
                        <input id="image_url" name="image_url" type="file" accept=".jpg,.jpeg,.png,.svg" required
                            class="h-10 w-full rounded-lg border border-[#d896c4] px-3 py-2 text-[14px] text-[#5E1C3D] file:mr-3 file:rounded file:border-0 file:bg-[#efe6fb] file:px-3 file:py-1 file:text-[#741f58] focus:border-[#933e77]">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#5E1C3D]" for="brand_id">Merek</label>
                        <select id="brand_id" name="brand_id" required
                            class="h-10 w-full rounded-lg border border-[#d896c4] px-3 text-[14px] text-[#5E1C3D] outline-none focus:border-[#933e77]">
                            <option value="">Pilih merek</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->brand_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#5E1C3D]" for="category_id">Kategori</label>
                        <select id="category_id" name="category_id" required
                            class="h-10 w-full rounded-lg border border-[#d896c4] px-3 text-[14px] text-[#5E1C3D] outline-none focus:border-[#933e77]">
                            <option value="">Pilih kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#5E1C3D]" for="packaging_type_id">Jenis
                            Kemasan</label>
                        <select id="packaging_type_id" name="packaging_type_id"
                            class="h-10 w-full rounded-lg border border-[#d896c4] px-3 text-[14px] text-[#5E1C3D] outline-none focus:border-[#933e77]">
                            <option value="">Pilih jenis kemasan</option>
                            @foreach ($packagingTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->packaging_type }}</option>
                            @endforeach
                        </select>
                        <input id="packaging_type_new" name="packaging_type_new" type="text"
                            class="mt-2 h-10 w-full rounded-lg border border-[#d896c4] px-3 text-[14px] text-[#5E1C3D] outline-none placeholder:text-[#caa3c0] focus:border-[#933e77]"
                            placeholder="Atau tambah jenis kemasan baru">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#5E1C3D]" for="packaging_size_id">Ukuran
                            Kemasan</label>
                        <select id="packaging_size_id" name="packaging_size_id"
                            class="h-10 w-full rounded-lg border border-[#d896c4] px-3 text-[14px] text-[#5E1C3D] outline-none focus:border-[#933e77]">
                            <option value="">Pilih ukuran kemasan</option>
                            @foreach ($packagingSizes as $size)
                                <option value="{{ $size->id }}">{{ $size->size }}</option>
                            @endforeach
                        </select>
                        <input id="packaging_size_new" name="packaging_size_new" type="text"
                            class="mt-2 h-10 w-full rounded-lg border border-[#d896c4] px-3 text-[14px] text-[#5E1C3D] outline-none placeholder:text-[#caa3c0] focus:border-[#933e77]"
                            placeholder="Atau tambah ukuran kemasan baru">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 pt-8 md:grid-cols-2">
                    <a href="{{ route('dashboard.products.index') }}"
                        class="flex h-10 items-center justify-center rounded-lg border border-[#802A76] bg-white text-[15px] font-semibold text-[#741f58] transition hover:bg-[#f8f4ff]">
                        Batal
                    </a>
                    <button id="submitCreateProduct" type="submit"
                        class="h-10 rounded-lg bg-[#802A76] text-[15px] font-semibold text-white transition hover:bg-[#741f58]">
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
            const packagingTypeSelect = document.getElementById('packaging_type_id');
            const packagingTypeInput = document.getElementById('packaging_type_new');
            const packagingSizeSelect = document.getElementById('packaging_size_id');
            const packagingSizeInput = document.getElementById('packaging_size_new');
            if (!form || !submitButton) return;

            if (packagingTypeInput && packagingTypeSelect) {
                packagingTypeInput.addEventListener('input', () => {
                    if (packagingTypeInput.value.trim().length > 0) {
                        packagingTypeSelect.value = '';
                    }
                });

                packagingTypeSelect.addEventListener('change', () => {
                    if (packagingTypeSelect.value) {
                        packagingTypeInput.value = '';
                    }
                });
            }

            if (packagingSizeInput && packagingSizeSelect) {
                packagingSizeInput.addEventListener('input', () => {
                    if (packagingSizeInput.value.trim().length > 0) {
                        packagingSizeSelect.value = '';
                    }
                });

                packagingSizeSelect.addEventListener('change', () => {
                    if (packagingSizeSelect.value) {
                        packagingSizeInput.value = '';
                    }
                });
            }

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
