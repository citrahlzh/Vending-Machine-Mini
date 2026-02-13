@extends('dashboard.layouts.app', [
    'title' => 'Iklan',
])

@section('content')
    <section class="space-y-6 p-2">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard.master-data.index') }}">
                    <img src="{{ asset('assets/icons/dashboard/back.svg') }}" alt="Kembali">
                </a>
                <h1 class="text-[28px] font-semibold leading-none text-[#3C1C5E]">Iklan</h1>
            </div>

            <p class="mt-3 text-[18px] text-[#4F3970]">
                Halaman ini untuk menampilkan daftar iklan pada Vending Machine.
            </p>
        </div>

        <article class="rounded-2xl border border-[#ddd2ef] bg-white p-8 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
            <div id="adsAddAction" class="hidden">
                <button id="openCreateAdModal" type="button"
                    class="rounded-lg bg-[#5A2F7E] px-5 py-2 text-[14px] font-semibold text-white transition hover:bg-[#4B1F74]">
                    Tambah Data
                </button>
            </div>

            <div class="overflow-x-auto">
                <table id="adsTable" class="dashboard-datatable display w-full">
                    <thead>
                        <tr>
                            <th class="text-center">No</th>
                            <th>Judul</th>
                            <th class="text-center">Foto Spanduk</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ads as $ad)
                            <tr data-ad-id="{{ $ad->id }}" data-ad-title="{{ e($ad->title) }}"
                                data-ad-status="{{ $ad->status }}" data-ad-image="{{ asset('storage/' . $ad->image_url) }}"
                                data-ad-image-name="{{ basename($ad->image_url) }}">
                                <td class="text-center font-semibold">{{ $loop->iteration }}</td>
                                <td>{{ $ad->title }}</td>
                                <td class="text-center">
                                    <button type="button"
                                        class="open-show-ad-modal inline-flex items-center justify-center rounded-md border border-[#d8c9eb] px-3 py-1 text-[13px] text-[#3C1C5E] hover:bg-[#f5f0fb]">
                                        Lihat Gambar
                                    </button>
                                </td>
                                <td class="text-center">
                                    @php
                                        $statusClass =
                                            $ad->status === 'active'
                                                ? 'bg-[#d7f2e1] text-[#17914f]'
                                                : 'bg-[#fde0e1] text-[#de1c24]';
                                    @endphp
                                    <span
                                        class="inline-flex rounded-full px-4 py-1 text-[12px] font-medium {{ $statusClass }}">
                                        {{ $ad->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button type="button" class="open-show-ad-modal">
                                            <img src="{{ asset('assets/icons/dashboard/show.svg') }}" alt="Lihat">
                                        </button>
                                        <button type="button" class="open-edit-ad-modal">
                                            <img src="{{ asset('assets/icons/dashboard/edit.svg') }}" alt="Edit">
                                        </button>
                                        <button type="button" class="open-delete-ad-modal">
                                            <img src="{{ asset('assets/icons/dashboard/delete.svg') }}" alt="Hapus">
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </article>
    </section>

    <div id="createAdModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 backdrop-blur-[1px]"
        style="background-color: rgba(31, 17, 48, 0.48);">
        <div class="w-full overflow-hidden border border-[#ddd2ef] bg-white p-7 shadow-[0_12px_28px_rgba(60,28,94,0.2)]"
            style="max-width: 560px; border-radius: 22px;">
            <h2 class="text-center text-[24px] font-semibold text-[#3C1C5E]">Tambah Data Iklan</h2>

            <form id="createAdForm" class="mt-6 space-y-3.5" enctype="multipart/form-data">
                <div>
                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]" for="adTitle">Judul</label>
                    <input id="adTitle" name="title" type="text" required
                        class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none placeholder:text-[#b5a3ca] focus:border-[#6B3E93]"
                        placeholder="Masukkan Judul Iklan">
                </div>

                <div>
                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]" for="adStatus">Status</label>
                    <select id="adStatus" name="status" required
                        class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">
                        <option value="">Pilih Status</option>
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                </div>

                <div>
                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]" for="adImage">Foto Spanduk</label>
                    <label id="adDropzone" for="adImage"
                        class="flex min-h-[150px] cursor-pointer flex-col items-center justify-center rounded-lg border border-[#B596D8] px-6 text-center transition">
                        <img id="adDropzoneIcon" src="{{ asset('assets/icons/dashboard/image-upload.svg') }}" alt=""
                            class="h-7 w-7 opacity-70">
                        <p id="adDropzonePrompt" class="mt-3 text-[15px] text-[#6B4E90]">Klik untuk mengunggah atau seret dan lepas</p>
                        <p id="adDropzoneHint" class="mt-2 text-[13px] text-[#6B4E90]">SVG, PNG, JPG atau GIF (MAX. 2MB)</p>
                        <p id="adImageName"
                            class="mt-4 hidden block w-full max-w-full overflow-hidden text-ellipsis whitespace-nowrap px-2 text-[20px] font-medium leading-none text-[#3C1C5E]">
                        </p>
                    </label>
                    <input id="adImage" name="image_url" type="file" accept=".svg,.png,.jpg,.jpeg,.gif" class="hidden"
                        required>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-3">
                    <button id="cancelCreateAd" type="button"
                        class="h-10 rounded-lg border border-[#5A2F7E] bg-white text-[15px] font-semibold text-[#4B1F74] transition hover:bg-[#f8f4ff]">
                        Batal
                    </button>
                    <button id="submitCreateAd" type="submit"
                        class="h-10 rounded-lg bg-[#5A2F7E] text-[15px] font-semibold text-white transition hover:bg-[#4B1F74]">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="showAdModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 backdrop-blur-[1px]"
        style="background-color: rgba(31, 17, 48, 0.48);">
        <div class="w-full overflow-hidden border border-[#ddd2ef] bg-white p-7 shadow-[0_12px_28px_rgba(60,28,94,0.2)]"
            style="max-width: 560px; border-radius: 22px;">
            <h2 class="text-center text-[24px] font-semibold text-[#3C1C5E]">Detail Iklan</h2>

            <div class="mt-6 rounded-xl border border-[#ddd2ef] bg-[#f8f4ff] p-6">
                <p id="showAdTitle" class="text-center text-[20px] font-semibold text-[#3C1C5E]"></p>
                <img id="showAdImage" src="" alt="Foto Spanduk"
                    class="mt-4 h-[140px] w-full rounded-lg border border-[#d8c9eb] object-cover">
            </div>

            <div class="mt-5">
                <button id="closeShowAdModal" type="button"
                    class="h-10 w-full rounded-lg border border-[#5A2F7E] bg-white text-[15px] font-semibold text-[#4B1F74] transition hover:bg-[#f8f4ff]">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <div id="editAdModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 backdrop-blur-[1px]"
        style="background-color: rgba(31, 17, 48, 0.48);">
        <div class="w-full overflow-hidden border border-[#ddd2ef] bg-white p-7 shadow-[0_12px_28px_rgba(60,28,94,0.2)]"
            style="max-width: 560px; border-radius: 22px;">
            <h2 class="text-center text-[24px] font-semibold text-[#3C1C5E]">Edit Data Iklan</h2>

            <form id="editAdForm" class="mt-6 space-y-3.5" enctype="multipart/form-data">
                <input id="editAdId" type="hidden">

                <div>
                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]" for="editAdTitle">Judul</label>
                    <input id="editAdTitle" name="title" type="text" required
                        class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none placeholder:text-[#b5a3ca] focus:border-[#6B3E93]">
                </div>

                <div>
                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]" for="editAdStatus">Status</label>
                    <select id="editAdStatus" name="status" required
                        class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px] text-[#3C1C5E] outline-none focus:border-[#6B3E93]">
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                </div>

                <div>
                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]" for="editAdImage">Foto Spanduk</label>
                    <label id="editAdDropzone" for="editAdImage"
                        class="flex min-h-[150px] cursor-pointer flex-col items-center justify-center rounded-lg border border-[#B596D8] px-6 text-center transition">
                        <img id="editAdDropzoneIcon" src="{{ asset('assets/icons/dashboard/image-upload.svg') }}" alt=""
                            class="h-7 w-7 opacity-70">
                        <p id="editAdDropzonePrompt" class="mt-3 text-[15px] text-[#6B4E90]">Klik untuk mengunggah atau seret dan lepas</p>
                        <p id="editAdDropzoneHint" class="mt-2 text-[13px] text-[#6B4E90]">SVG, PNG, JPG atau GIF (MAX. 2MB)</p>
                        <p id="editAdImageName"
                            class="mt-4 hidden block w-full max-w-full overflow-hidden text-ellipsis whitespace-nowrap px-2 text-[20px] font-medium leading-none text-[#3C1C5E]">
                        </p>
                    </label>
                    <input id="editAdImage" name="image_url" type="file" accept=".svg,.png,.jpg,.jpeg,.gif" class="hidden">
                </div>

                <div class="grid grid-cols-2 gap-4 pt-3">
                    <button id="cancelEditAd" type="button"
                        class="h-10 rounded-lg border border-[#5A2F7E] bg-white text-[15px] font-semibold text-[#4B1F74] transition hover:bg-[#f8f4ff]">
                        Batal
                    </button>
                    <button id="submitEditAd" type="submit"
                        class="h-10 rounded-lg bg-[#5A2F7E] text-[15px] font-semibold text-white transition hover:bg-[#4B1F74]">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection


@push('script')
    <script>
        $(function() {
            initDashboardDataTable({
                selector: '#adsTable',
                pageLength: 10,
                actionContainerSelector: '#adsAddAction',
            });
        });

        (() => {
            const modal = document.getElementById('createAdModal');
            const openButton = document.getElementById('openCreateAdModal');
            const cancelButton = document.getElementById('cancelCreateAd');
            const form = document.getElementById('createAdForm');
            const fileInput = document.getElementById('adImage');
            const fileName = document.getElementById('adImageName');
            const dropzoneIcon = document.getElementById('adDropzoneIcon');
            const dropzonePrompt = document.getElementById('adDropzonePrompt');
            const dropzoneHint = document.getElementById('adDropzoneHint');
            const submitButton = document.getElementById('submitCreateAd');
            const dropzone = document.getElementById('adDropzone');

            if (!modal || !openButton || !cancelButton || !form || !fileInput || !fileName || !dropzoneIcon || !
                dropzonePrompt || !dropzoneHint || !submitButton || !dropzone) return;

            const allowedMime = ['image/svg+xml', 'image/png', 'image/jpeg', 'image/gif'];
            const maxSize = 2 * 1024 * 1024;
            const defaultDropzoneIcon = "{{ asset('assets/icons/dashboard/image-upload.svg') }}";
            const selectedDropzoneIcon = "{{ asset('assets/icons/dashboard/image.svg') }}";

            const setDropzoneState = (hasFile) => {
                dropzoneIcon.src = hasFile ? selectedDropzoneIcon : defaultDropzoneIcon;
                fileName.classList.toggle('hidden', !hasFile);
                dropzonePrompt.classList.toggle('hidden', hasFile);
                dropzoneHint.classList.toggle('hidden', hasFile);
                dropzone.classList.toggle('bg-[#f3eefb]', hasFile);
                dropzone.classList.toggle('border-[#6B3E93]', hasFile);
                dropzone.classList.toggle('border-[#B596D8]', !hasFile);
                dropzoneIcon.classList.toggle('h-7', !hasFile);
                dropzoneIcon.classList.toggle('w-7', !hasFile);
                dropzoneIcon.classList.toggle('h-12', hasFile);
                dropzoneIcon.classList.toggle('w-12', hasFile);
                dropzoneIcon.classList.toggle('opacity-70', !hasFile);
                dropzoneIcon.classList.toggle('opacity-100', hasFile);
            };

            const setSelectedFile = (file) => {
                if (!file) return false;

                if (!allowedMime.includes(file.type)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Format tidak didukung',
                        text: 'Gunakan file SVG, PNG, JPG, atau GIF.',
                    });
                    fileInput.value = '';
                    setDropzoneState(false);
                    return false;
                }

                if (file.size > maxSize) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Ukuran terlalu besar',
                        text: 'Maksimal ukuran file adalah 2MB.',
                    });
                    fileInput.value = '';
                    setDropzoneState(false);
                    return false;
                }

                const dt = new DataTransfer();
                dt.items.add(file);
                fileInput.files = dt.files;

                fileName.textContent = file.name;
                fileName.setAttribute('title', file.name);
                setDropzoneState(true);
                return true;
            };

            const openModal = () => {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            };
            const closeModal = () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                form.reset();
                fileName.textContent = '';
                fileName.removeAttribute('title');
                setDropzoneState(false);
            };

            openButton.addEventListener('click', openModal);
            cancelButton.addEventListener('click', closeModal);
            modal.addEventListener('click', (event) => {
                if (event.target === modal) closeModal();
            });

            fileInput.addEventListener('change', () => {
                const file = fileInput.files && fileInput.files[0];
                if (!file) {
                    fileName.textContent = '';
                    fileName.removeAttribute('title');
                    setDropzoneState(false);
                    return;
                }
                setSelectedFile(file);
            });

            ['dragenter', 'dragover'].forEach((eventName) => {
                dropzone.addEventListener(eventName, (event) => {
                    event.preventDefault();
                    event.stopPropagation();
                    dropzone.classList.add('border-[#6B3E93]', 'bg-[#f8f4ff]');
                });
            });

            ['dragleave', 'drop'].forEach((eventName) => {
                dropzone.addEventListener(eventName, (event) => {
                    event.preventDefault();
                    event.stopPropagation();
                    dropzone.classList.remove('border-[#6B3E93]', 'bg-[#f8f4ff]');
                });
            });

            dropzone.addEventListener('drop', (event) => {
                const file = event.dataTransfer?.files?.[0];
                if (!file) return;
                setSelectedFile(file);
            });

            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                const formData = new FormData(form);
                submitButton.disabled = true;
                submitButton.textContent = 'Menyimpan...';

                try {
                    const response = await fetch('/api/ad/store', {
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
                            'Gagal menyimpan data iklan.');
                        throw new Error(message);
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data?.message || 'Data iklan berhasil disimpan.',
                        timer: 1500,
                        showConfirmButton: false,
                    });

                    setTimeout(() => window.location.reload(), 1500);
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

        (() => {
            const showModal = document.getElementById('showAdModal');
            const showTitle = document.getElementById('showAdTitle');
            const showImage = document.getElementById('showAdImage');
            const closeShowButton = document.getElementById('closeShowAdModal');

            const editModal = document.getElementById('editAdModal');
            const editForm = document.getElementById('editAdForm');
            const editAdId = document.getElementById('editAdId');
            const editTitle = document.getElementById('editAdTitle');
            const editStatus = document.getElementById('editAdStatus');
            const editFileInput = document.getElementById('editAdImage');
            const editFileName = document.getElementById('editAdImageName');
            const editDropzone = document.getElementById('editAdDropzone');
            const editDropzoneIcon = document.getElementById('editAdDropzoneIcon');
            const editDropzonePrompt = document.getElementById('editAdDropzonePrompt');
            const editDropzoneHint = document.getElementById('editAdDropzoneHint');
            const cancelEditButton = document.getElementById('cancelEditAd');
            const submitEditButton = document.getElementById('submitEditAd');

            if (!showModal || !showTitle || !showImage || !closeShowButton || !editModal || !editForm || !editAdId ||
                !editTitle || !editStatus || !editFileInput || !editFileName || !editDropzone || !editDropzoneIcon ||
                !editDropzonePrompt || !editDropzoneHint || !cancelEditButton || !submitEditButton) return;

            const defaultDropzoneIcon = "{{ asset('assets/icons/dashboard/image-upload.svg') }}";
            const selectedDropzoneIcon = "{{ asset('assets/icons/dashboard/image.svg') }}";
            const allowedMime = ['image/svg+xml', 'image/png', 'image/jpeg', 'image/gif'];
            const maxSize = 2 * 1024 * 1024;

            const setEditDropzoneState = (hasFile) => {
                editDropzoneIcon.src = hasFile ? selectedDropzoneIcon : defaultDropzoneIcon;
                editFileName.classList.toggle('hidden', !hasFile);
                editDropzonePrompt.classList.toggle('hidden', hasFile);
                editDropzoneHint.classList.toggle('hidden', hasFile);
                editDropzone.classList.toggle('bg-[#f3eefb]', hasFile);
                editDropzone.classList.toggle('border-[#6B3E93]', hasFile);
                editDropzone.classList.toggle('border-[#B596D8]', !hasFile);
                editDropzoneIcon.classList.toggle('h-7', !hasFile);
                editDropzoneIcon.classList.toggle('w-7', !hasFile);
                editDropzoneIcon.classList.toggle('h-12', hasFile);
                editDropzoneIcon.classList.toggle('w-12', hasFile);
                editDropzoneIcon.classList.toggle('opacity-70', !hasFile);
                editDropzoneIcon.classList.toggle('opacity-100', hasFile);
            };

            const setSelectedEditFile = (file) => {
                if (!file) return false;

                if (!allowedMime.includes(file.type)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Format tidak didukung',
                        text: 'Gunakan file SVG, PNG, JPG, atau GIF.',
                    });
                    editFileInput.value = '';
                    setEditDropzoneState(false);
                    return false;
                }

                if (file.size > maxSize) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Ukuran terlalu besar',
                        text: 'Maksimal ukuran file adalah 2MB.',
                    });
                    editFileInput.value = '';
                    setEditDropzoneState(false);
                    return false;
                }

                const dt = new DataTransfer();
                dt.items.add(file);
                editFileInput.files = dt.files;

                editFileName.textContent = file.name;
                editFileName.setAttribute('title', file.name);
                setEditDropzoneState(true);
                return true;
            };

            const openShowModal = (ad) => {
                showTitle.textContent = ad.title || '-';
                showImage.src = ad.image || '';
                showImage.alt = ad.title || 'Foto Spanduk';
                showModal.classList.remove('hidden');
                showModal.classList.add('flex');
            };

            const closeShowModal = () => {
                showModal.classList.add('hidden');
                showModal.classList.remove('flex');
                showTitle.textContent = '';
                showImage.src = '';
            };

            const openEditModal = (ad) => {
                editAdId.value = ad.id || '';
                editTitle.value = ad.title || '';
                editStatus.value = ad.status || 'inactive';
                editFileInput.value = '';
                editFileName.textContent = ad.imageName || '';
                if (ad.imageName) {
                    editFileName.setAttribute('title', ad.imageName);
                } else {
                    editFileName.removeAttribute('title');
                }
                setEditDropzoneState(Boolean(ad.imageName));
                editModal.classList.remove('hidden');
                editModal.classList.add('flex');
            };

            const closeEditModal = () => {
                editModal.classList.add('hidden');
                editModal.classList.remove('flex');
                editForm.reset();
                editFileInput.value = '';
                editFileName.textContent = '';
                editFileName.removeAttribute('title');
                setEditDropzoneState(false);
            };

            document.querySelectorAll('.open-show-ad-modal').forEach((button) => {
                button.addEventListener('click', () => {
                    const row = button.closest('tr');
                    if (!row) return;
                    openShowModal({
                        id: row.dataset.adId,
                        title: row.dataset.adTitle,
                        status: row.dataset.adStatus,
                        image: row.dataset.adImage,
                        imageName: row.dataset.adImageName,
                    });
                });
            });

            document.querySelectorAll('.open-edit-ad-modal').forEach((button) => {
                button.addEventListener('click', () => {
                    const row = button.closest('tr');
                    if (!row) return;
                    openEditModal({
                        id: row.dataset.adId,
                        title: row.dataset.adTitle,
                        status: row.dataset.adStatus,
                        image: row.dataset.adImage,
                        imageName: row.dataset.adImageName,
                    });
                });
            });

            closeShowButton.addEventListener('click', closeShowModal);
            showModal.addEventListener('click', (event) => {
                if (event.target === showModal) closeShowModal();
            });

            cancelEditButton.addEventListener('click', closeEditModal);
            editModal.addEventListener('click', (event) => {
                if (event.target === editModal) closeEditModal();
            });

            editFileInput.addEventListener('change', () => {
                const file = editFileInput.files && editFileInput.files[0];
                if (!file) {
                    editFileName.textContent = '';
                    editFileName.removeAttribute('title');
                    setEditDropzoneState(false);
                    return;
                }
                setSelectedEditFile(file);
            });

            ['dragenter', 'dragover'].forEach((eventName) => {
                editDropzone.addEventListener(eventName, (event) => {
                    event.preventDefault();
                    event.stopPropagation();
                    editDropzone.classList.add('border-[#6B3E93]', 'bg-[#f8f4ff]');
                });
            });

            ['dragleave', 'drop'].forEach((eventName) => {
                editDropzone.addEventListener(eventName, (event) => {
                    event.preventDefault();
                    event.stopPropagation();
                    editDropzone.classList.remove('border-[#6B3E93]', 'bg-[#f8f4ff]');
                });
            });

            editDropzone.addEventListener('drop', (event) => {
                const file = event.dataTransfer?.files?.[0];
                if (!file) return;
                setSelectedEditFile(file);
            });

            editForm.addEventListener('submit', async (event) => {
                event.preventDefault();

                const id = editAdId.value;
                if (!id) return;

                const formData = new FormData();
                formData.append('title', editTitle.value);
                formData.append('status', editStatus.value);
                if (editFileInput.files && editFileInput.files[0]) {
                    formData.append('image_url', editFileInput.files[0]);
                }

                submitEditButton.disabled = true;
                submitEditButton.textContent = 'Menyimpan...';

                try {
                    const response = await fetch(`/api/ad/update/${id}`, {
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
                            'Gagal memperbarui data iklan.');
                        throw new Error(message);
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data?.message || 'Data iklan berhasil diperbarui.',
                        timer: 1500,
                        showConfirmButton: false,
                    });

                    setTimeout(() => window.location.reload(), 1500);
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: error.message || 'Terjadi kesalahan saat memperbarui data.',
                    });
                } finally {
                    submitEditButton.disabled = false;
                    submitEditButton.textContent = 'Simpan';
                }
            });

            document.addEventListener('click', async (event) => {
                const deleteButton = event.target.closest('.open-delete-ad-modal');
                if (!deleteButton) return;

                const row = deleteButton.closest('tr');
                if (!row) return;

                const adId = row.dataset.adId;
                const adTitle = row.dataset.adTitle || 'iklan ini';
                if (!adId) return;

                const result = await Swal.fire({
                    icon: 'warning',
                    title: 'Hapus data iklan?',
                    text: `Data "${adTitle}" akan dihapus permanen.`,
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33',
                });

                if (!result.isConfirmed) return;

                deleteButton.disabled = true;

                try {
                    const response = await fetch(`/api/ad/delete/${adId}`, {
                        method: 'DELETE',
                        headers: {
                            Accept: 'application/json',
                        },
                    });

                    const data = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        throw new Error(data?.message || 'Gagal menghapus data iklan.');
                    }

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: data?.message || 'Data iklan berhasil dihapus.',
                        timer: 1500,
                        showConfirmButton: false,
                    });

                    setTimeout(() => window.location.reload(), 1500);
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: error.message || 'Terjadi kesalahan saat menghapus data.',
                    });
                } finally {
                    deleteButton.disabled = false;
                }
            });
        })();
    </script>
@endpush

