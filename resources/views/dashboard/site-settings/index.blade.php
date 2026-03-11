@extends('dashboard.layouts.app', [
    'title' => 'Setelan Situs',
])

@section('content')
    <section class="space-y-6 p-2">

        <div>
            <h1 class="text-[28px] font-semibold text-[#3C1C5E]">Setelan Situs</h1>
            <p class="mt-3 text-[18px] text-[#4F3970]">
                Halaman ini untuk mengatur data pada situs.
            </p>
        </div>

        <article class="rounded-2xl border border-[#ddd2ef] bg-white p-8 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">

            <form id="settingsForm">

                <div id="settingsContainer" class="space-y-8"></div>
                <div class="grid grid-cols-1 gap-4 pt-8 md:grid-cols-2">
                    <button id="cancelBtn" type="button" disabled
                        class="mt-6 h-10 rounded-lg border border-[#B596D8] px-6 text-[#3C1C5E] disabled:opacity-50 disabled:cursor-not-allowed">
                        Batal
                    </button>
                    <button id="saveBtn" type="submit" disabled
                        class="mt-6 h-10 rounded-lg bg-[#5A2F7E] px-6 text-[15px] font-semibold text-white transition hover:bg-[#4B1F74] disabled:bg-[#BFA7D8] disabled:cursor-not-allowed">
                        Simpan
                    </button>
                </div>
            </form>

        </article>

    </section>
@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let originalSettings = {};

        async function loadSettings() {
            const response = await fetch('/api/site-setting/list');
            const data = await response.json();
            originalSettings = JSON.parse(JSON.stringify(data));

            const container = document.getElementById('settingsContainer');
            container.innerHTML = '';

            Object.keys(data).forEach(group => {
                let groupHTML = `
            <div>
                <h2 class="text-lg font-semibold text-[#3C1C5E] mb-4 capitalize">
                    ${group}
                </h2>
            <div class="space-y-4">
            `;
                data[group].forEach(setting => {
                    if (setting.type === "file") {

                        const fileName = setting.value ?
                            setting.value.split('/').pop() :
                            'No file chosen';

                        const imageUrl = setting.value ?
                            `/storage/${setting.value}` :
                            '';

                        groupHTML += `
    <div>
        <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]">
            ${setting.label}
        </label>

        <div class="flex items-center gap-3">

            <img
    id="preview-${setting.key}"
    src="${setting.value ? `/storage/${setting.value}` : ''}"
    class="h-12 w-12 rounded object-cover"
>

            <div class="flex w-full items-center rounded-lg border border-[#B596D8]">

                <label
                    class="rounded-l-lg cursor-pointer bg-[#efe6fb] px-3 py-2 text-sm text-[#4B1F74]">
                    Choose File
                    <input
                        type="file"
                        name="settings[${setting.key}]"
                        class="hidden"
                        onchange="previewImage(this, '${setting.key}'); updateFileName(this); checkChanges();"
                    >
                </label>

                <span class="px-3 text-sm text-[#6B7280]"
                    id="file-name-${setting.key}">
                    ${fileName}
                </span>

            </div>

        </div>
    </div>
    `;
                    } else {
                        groupHTML += `
                <div>
                    <label class="mb-1.5 block text-[15px] font-semibold text-[#3C1C5E]">
                        ${setting.label}
                    </label>
                    <input
                        type="${setting.type}"
                        name="settings[${setting.key}]"
                        value="${setting.value ?? ''}"
                        class="h-10 w-full rounded-lg border border-[#B596D8] px-3 text-[14px]
                        text-[#3C1C5E] outline-none placeholder:text-[#b5a3ca]
                        focus:border-[#6B3E93]"
                    >
                </div>
                `;
                    }
                });
                groupHTML += `
                </div>
            </div>
            `;
                container.innerHTML += groupHTML;
            });
        }

        function updateFileName(input) {

            const key = input.name.match(/\[(.*?)\]/)[1];
            const label = document.getElementById(`file-name-${key}`);

            if (input.files.length > 0) {
                label.textContent = input.files[0].name;
            }

        }

        function previewImage(input, key) {
            const file = input.files[0];
            if (!file) return;

            const reader = new FileReader();

            reader.onload = function(e) {
                const img = document.getElementById(`preview-${key}`);
                if (img) {
                    img.src = e.target.result;
                }
            }

            reader.readAsDataURL(file);
        }

        function checkChanges() {
            const formData = new FormData(document.getElementById('settingsForm'));
            const current = {};

            for (let [key, value] of formData.entries()) {
                const cleanKey = key.replace('settings[', '').replace(']', '');
                current[cleanKey] = value;
            }

            let changed = false;

            Object.keys(originalSettings).forEach(group => {
                originalSettings[group].forEach(setting => {
                    if (setting.type === "file") {

                        const input = document.querySelector(`[name="settings[${setting.key}]"]`);

                        if (input && input.files.length > 0) {
                            changed = true;
                        }

                    } else {

                        if (current[setting.key] != setting.value) {
                            changed = true;
                        }

                    }
                });
            });

            document.getElementById('saveBtn').disabled = !changed;
            document.getElementById('cancelBtn').disabled = !changed;
        }

        function resetForm() {

            Object.keys(originalSettings).forEach(group => {

                originalSettings[group].forEach(setting => {

                    const input = document.querySelector(
                        `[name="settings[${setting.key}]"]`
                    );

                    if (!input) return;

                    if (setting.type === "file") {

                        input.value = "";

                        const preview = document.getElementById(`preview-${setting.key}`);
                        if (preview) {
                            preview.src = `/storage/${setting.value}`;
                        }

                        const label = document.getElementById(`file-name-${setting.key}`);
                        if (label) {
                            label.textContent = setting.value ?
                                setting.value.split('/').pop() :
                                'No file chosen';
                        }


                    } else {

                        input.value = setting.value ?? "";

                    }

                });

            });

            document.getElementById('saveBtn').disabled = true;
            document.getElementById('cancelBtn').disabled = true;

        }

        loadSettings();

        document.getElementById('settingsForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const settings = {};
            for (let [key, value] of formData.entries()) {
                const cleanKey = key.replace('settings[', '').replace(']', '');
                settings[cleanKey] = value;
            }

            const response = await fetch('/api/site-setting/update', {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                },
                body: formData,
            });

            if (!response.ok) {

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Terjadi kesalahan saat menyimpan data.',
                    confirmButtonColor: '#5A2F7E'
                });

                return;
            }

            const result = await response.json();

            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: result.message,
                confirmButtonColor: '#5A2F7E'
            });

            document.getElementById('saveBtn').disabled = true;
            await loadSettings();
        });

        document.getElementById('settingsForm').addEventListener('input', function(e) {
            if (e.target.name && e.target.name.includes('settings[')) {
                checkChanges();
            }
        });

        document.getElementById('settingsForm').addEventListener('input', function(e) {
            if (e.target.type === "tel") {
                e.target.value = e.target.value.replace(/[^0-9+]/g, '');
            }
        });

        document.getElementById('cancelBtn').addEventListener('click', function() {
            Swal.fire({
                title: 'Batalkan perubahan?',
                text: 'Perubahan yang belum disimpan akan hilang.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#5A2F7E',
                cancelButtonText: 'Tidak',
                confirmButtonText: 'Ya'
            }).then((result) => {
                if (result.isConfirmed) {
                    resetForm();
                }
            });
        });

        document.addEventListener('change', function(e) {
            if (e.target.type === "file") {
                checkChanges();
            }
        });

        document.addEventListener('paste', function(e) {
            if (e.target.type === "tel") {
                const paste = (e.clipboardData || window.clipboardData).getData('text');
                if (!/^[0-9+]+$/.test(paste)) {
                    e.preventDefault();
                }
            }
        });
    </script>
@endpush
