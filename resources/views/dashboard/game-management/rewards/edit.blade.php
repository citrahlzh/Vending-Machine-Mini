@extends('dashboard.layouts.app', [
    'title' => 'Edit Hadiah',
])

@section('content')
    <section class="space-y-6 p-2">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard.game-management.rewards.index') }}">
                    <img src="{{ asset('assets/icons/dashboard/back.svg') }}">
                </a>
                <h1 class="text-[28px] font-semibold text-[#3C1C5E]">
                    Ubah Hadiah
                </h1>
            </div>

            <p class="mt-2 text-[#4F3970]">
                Perbarui data hadiah permainan.
            </p>
        </div>


        <article class="rounded-2xl border border-[#ddd2ef] bg-white p-8">
            <form id="updateRewardForm" class="space-y-6">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]">Kode Hadiah</label>
                        <input name="code" type="text" value="{{ $reward->code }}"
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none placeholder:text-[#b5a3ca] focus:border-[#6B3E93]"
                            placeholder="Masukkan kode hadiah">
                    </div>

                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]">Nama Hadiah</label>
                        <input name="name" type="text" value="{{ $reward->name }}"
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none placeholder:text-[#b5a3ca] focus:border-[#6B3E93]"
                            placeholder="Masukkan nama hadiah">
                    </div>

                </div>

                <div>
                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]">Deskripsi</label>
                    <textarea name="description" value="{{ $reward->description }}"
                        class="w-full rounded-lg border border-[#B596D8] px-3 py-2 text-[14px] text-[#3C1C5E] outline-none placeholder:text-[#b5a3ca] focus:border-[#6B3E93]"
                        placeholder="Masukkan deskripsi hadiah (opsional)"></textarea>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]">Tipe Hadiah</label>
                        <select name="type" required
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">
                            <option value="">Pilih tipe hadiah</option>
                            <option value="product" {{ $reward->type == 'product' ? 'selected' : '' }}>Produk</option>
                            <option value="none" {{ $reward->type == 'none' ? 'selected' : '' }}>Zonk / Tidak ada hadiah
                            </option>
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
                        <select name="product_display_id"
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">

                            <option value="">Pilih produk</option>

                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" @selected(old('product_display_id', $reward->product_display_id) == $product->id)>
                                    {{ $product->product->product_name }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    <div>
                        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]">Stok</label>
                        <input name="stock" type="number" value="{{ $reward->stock }}"
                            class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]"
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
                </div>

            </form>
        </article>
    </section>
@endsection

@push('script')
    <script>
        document.getElementById('updateRewardForm')
            .addEventListener('submit', async function(e) {
                e.preventDefault()

                const formData = new FormData(this)

                try {
                    const response = await fetch('/api/reward/update/{{ $reward->id }}', {
                        method: 'POST',
                        body: formData
                    })

                    const data = await response.json()

                    if (!response.ok) {
                        throw new Error(data.message)
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data.message
                    })

                    setTimeout(() => {
                        window.location.href =
                            "{{ route('dashboard.game-management.rewards.index') }}";
                    }, 1400);

                } catch (err) {

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: err.message
                    })

                }

            })
    </script>
@endpush
