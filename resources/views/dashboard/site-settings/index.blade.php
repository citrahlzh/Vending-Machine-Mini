@extends('dashboard.layouts.app', [
    'title' => 'Setelan Situs & Mesin',
])

@section('content')
    <section class="space-y-6 p-2">

        <div>
            <h1 class="text-[28px] font-semibold text-[#5E1C3D]">Setelan Situs & Mesin</h1>
            <p class="mt-3 text-[18px] text-[#703967]">
                Halaman ini untuk mengatur data situs, kontak, dan identitas mesin vending.
            </p>
        </div>

        <article class="rounded-2xl border border-[#efd2ea] bg-white p-8 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">

            <form id="settingsForm">
                @php
                    $machineData = [
                        'name' => old('machine.name', $machine?->name ?? ''),
                        'code' => old('machine.code', $machine?->code ?? ''),
                        'serial_number' => old('machine.serial_number', $machine?->serial_number ?? ''),
                        'location' => old('machine.location', $machine?->location ?? ''),
                        'operator_name' => old('machine.operator_name', $machine?->operator_name ?? ''),
                        'category' => old('machine.category', $machine?->category ?? ''),
                        'size' => old('machine.size', $machine?->size ?? ''),
                        'is_android' => old('machine.is_android', ($machine?->is_android ?? true) ? '1' : '0'),
                        'status' => old('machine.status', $machine?->status ?? 'active'),
                        'condition_status' => old('machine.condition_status', $machine?->condition_status ?? 'good'),
                        'photo_url' => $machine?->photo_url,
                    ];
                @endphp

                <div class="space-y-8 border-b border-[#f1e3ef] pb-8">
                    <div>
                        <h2 class="mb-4 text-lg font-semibold text-[#5E1C3D]">Identitas Mesin</h2>
                        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                            <div class="lg:col-span-2">
                                <label class="mb-1.5 block text-[15px] font-semibold text-[#5E1C3D]">Nama Mesin</label>
                                <input type="text" name="machine[name]" value="{{ $machineData['name'] }}"
                                    class="h-10 w-full rounded-lg border border-[#d896c4] px-3 text-[14px] text-[#5E1C3D] outline-none focus:border-[#933e77]">
                            </div>

                            <div>
                                <label class="mb-1.5 block text-[15px] font-semibold text-[#5E1C3D]">Kode Mesin</label>
                                <input id="machineCodeInput" type="text" name="machine[code]" value="{{ $machineData['code'] }}"
                                    maxlength="3" inputmode="text" autocomplete="off"
                                    class="h-10 w-full rounded-lg border border-[#d896c4] px-3 text-[14px] uppercase tracking-[0.2em] text-[#5E1C3D] outline-none focus:border-[#933e77]">
                                <p class="mt-1 text-[12px] text-[#8a6380]">Harus 3 huruf kapital, contoh: `ABC`.</p>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-[15px] font-semibold text-[#5E1C3D]">Nomor Seri</label>
                                <input type="text" name="machine[serial_number]" value="{{ $machineData['serial_number'] }}"
                                    class="h-10 w-full rounded-lg border border-[#d896c4] px-3 text-[14px] text-[#5E1C3D] outline-none focus:border-[#933e77]">
                            </div>

                            <div>
                                <label class="mb-1.5 block text-[15px] font-semibold text-[#5E1C3D]">Lokasi Mesin</label>
                                <input type="text" name="machine[location]" value="{{ $machineData['location'] }}"
                                    class="h-10 w-full rounded-lg border border-[#d896c4] px-3 text-[14px] text-[#5E1C3D] outline-none focus:border-[#933e77]">
                            </div>

                            <div>
                                <label class="mb-1.5 block text-[15px] font-semibold text-[#5E1C3D]">PIC / Operator</label>
                                <input type="text" name="machine[operator_name]" value="{{ $machineData['operator_name'] }}"
                                    class="h-10 w-full rounded-lg border border-[#d896c4] px-3 text-[14px] text-[#5E1C3D] outline-none focus:border-[#933e77]">
                            </div>

                            <div>
                                <label class="mb-1.5 block text-[15px] font-semibold text-[#5E1C3D]">Kategori Mesin</label>
                                <input type="text" name="machine[category]" value="{{ $machineData['category'] }}"
                                    class="h-10 w-full rounded-lg border border-[#d896c4] px-3 text-[14px] text-[#5E1C3D] outline-none focus:border-[#933e77]">
                            </div>

                            <div>
                                <label class="mb-1.5 block text-[15px] font-semibold text-[#5E1C3D]">Ukuran</label>
                                <input type="text" name="machine[size]" value="{{ $machineData['size'] }}"
                                    placeholder="Contoh: 180x90x75 cm"
                                    class="h-10 w-full rounded-lg border border-[#d896c4] px-3 text-[14px] text-[#5E1C3D] outline-none focus:border-[#933e77]">
                            </div>

                            <div>
                                <label class="mb-1.5 block text-[15px] font-semibold text-[#5E1C3D]">Sistem</label>
                                <select name="machine[is_android]"
                                    class="h-10 w-full rounded-lg border border-[#d896c4] px-3 text-[14px] text-[#5E1C3D] outline-none focus:border-[#933e77]">
                                    <option value="1" {{ (string) $machineData['is_android'] === '1' ? 'selected' : '' }}>Android</option>
                                    <option value="0" {{ (string) $machineData['is_android'] === '0' ? 'selected' : '' }}>Bukan Android</option>
                                </select>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-[15px] font-semibold text-[#5E1C3D]">Status</label>
                                <select name="machine[status]"
                                    class="h-10 w-full rounded-lg border border-[#d896c4] px-3 text-[14px] text-[#5E1C3D] outline-none focus:border-[#933e77]">
                                    <option value="active" {{ $machineData['status'] === 'active' ? 'selected' : '' }}>Aktif</option>
                                    <option value="inactive" {{ $machineData['status'] === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                                </select>
                            </div>

                            <div>
                                <label class="mb-1.5 block text-[15px] font-semibold text-[#5E1C3D]">Kondisi</label>
                                <select name="machine[condition_status]"
                                    class="h-10 w-full rounded-lg border border-[#d896c4] px-3 text-[14px] text-[#5E1C3D] outline-none focus:border-[#933e77]">
                                    <option value="good" {{ $machineData['condition_status'] === 'good' ? 'selected' : '' }}>Baik</option>
                                    <option value="maintenance" {{ $machineData['condition_status'] === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                    <option value="damaged" {{ $machineData['condition_status'] === 'damaged' ? 'selected' : '' }}>Rusak</option>
                                </select>
                            </div>

                            <div class="lg:col-span-2">
                                <label class="mb-1.5 block text-[15px] font-semibold text-[#5E1C3D]">Foto Barang / Vending Machine</label>
                                <div class="flex items-center gap-3">
                                    <img id="machine-photo-preview"
                                        src="{{ $machineData['photo_url'] ? asset('image/' . ltrim($machineData['photo_url'], '/')) : '' }}"
                                        class="h-16 w-16 rounded-lg object-cover {{ $machineData['photo_url'] ? '' : 'hidden' }}">
                                    <div class="flex w-full items-center rounded-lg border border-[#d896c4]">
                                        <label class="cursor-pointer rounded-l-lg bg-[#efe6fb] px-3 py-2 text-sm text-[#741f58]">
                                            Choose File
                                            <input id="machinePhotoInput" type="file" name="machine[photo_url]" class="hidden"
                                                accept=".jpg,.jpeg,.png,.gif,.svg,.webp">
                                        </label>
                                        <span id="machine-photo-name" class="px-3 text-sm text-[#6B7280]">
                                            {{ $machineData['photo_url'] ? basename($machineData['photo_url']) : 'No file chosen' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="settingsContainer" class="space-y-8 pt-8"></div>
                <div class="grid grid-cols-1 gap-4 pt-8 md:grid-cols-2">
                    <button id="cancelBtn" type="button" disabled
                        class="mt-6 h-10 rounded-lg border border-[#d896c4] px-6 text-[#5E1C3D] disabled:opacity-50 disabled:cursor-not-allowed">
                        Batal
                    </button>
                    <button id="saveBtn" type="submit" disabled
                        class="mt-6 h-10 rounded-lg bg-[#802A76] px-6 text-[15px] font-semibold text-white transition hover:bg-[#741f58] disabled:bg-[#BFA7D8] disabled:cursor-not-allowed">
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
        let originalMachine = @json($machineData);

        const machinePhotoPreview = () => document.getElementById('machine-photo-preview');
        const machinePhotoName = () => document.getElementById('machine-photo-name');
        const machinePhotoInput = () => document.getElementById('machinePhotoInput');
        const machineCodeInput = () => document.getElementById('machineCodeInput');

        function normalizeMachineCode(value) {
            return String(value || '')
                .toUpperCase()
                .replace(/[^A-Z]/g, '')
                .slice(0, 3);
        }

        function machinePreviewUrl(path) {
            if (!path) return '';
            return `/image/${String(path).replace(/^\/+/, '')}`;
        }

        function enforceMachineCode() {
            const input = machineCodeInput();
            if (!input) return;
            input.value = normalizeMachineCode(input.value);
        }

        async function loadSettings() {
            const response = await fetch('/api/site-setting/list');
            const data = await response.json();
            originalSettings = JSON.parse(JSON.stringify(data));

            const container = document.getElementById('settingsContainer');
            container.innerHTML = '';

            Object.keys(data).forEach(group => {
                let groupHTML = `
            <div>
                <h2 class="text-lg font-semibold text-[#5E1C3D] mb-4 capitalize">
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
                            `/image/${setting.value}` :
                            '';

                        groupHTML += `
    <div>
        <label class="mb-1.5 block text-[15px] font-semibold text-[#5E1C3D]">
            ${setting.label}
        </label>

        <div class="flex items-center gap-3">

            <img
    id="preview-${setting.key}"
    src="${setting.value ? `/image/${setting.value}` : ''}"
    class="h-12 w-12 rounded object-cover"
>

            <div class="flex w-full items-center rounded-lg border border-[#d896c4]">

                <label
                    class="rounded-l-lg cursor-pointer bg-[#efe6fb] px-3 py-2 text-sm text-[#741f58]">
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
                    <label class="mb-1.5 block text-[15px] font-semibold text-[#5E1C3D]">
                        ${setting.label}
                    </label>
                    <input
                        type="${setting.type}"
                        name="settings[${setting.key}]"
                        value="${setting.value ?? ''}"
                        class="h-10 w-full rounded-lg border border-[#d896c4] px-3 text-[14px]
                        text-[#5E1C3D] outline-none placeholder:text-[#caa3c0]
                        focus:border-[#933e77]"
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

        function previewMachinePhoto(input) {
            const file = input.files[0];
            const preview = machinePhotoPreview();
            const label = machinePhotoName();

            if (label) {
                label.textContent = file ? file.name : (originalMachine.photo_url ? originalMachine.photo_url.split('/').pop() :
                    'No file chosen');
            }

            if (!preview) return;

            if (!file) {
                preview.src = machinePreviewUrl(originalMachine.photo_url);
                preview.classList.toggle('hidden', !originalMachine.photo_url);
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }

        function readMachineForm() {
            return {
                name: document.querySelector('[name="machine[name]"]')?.value ?? '',
                code: normalizeMachineCode(document.querySelector('[name="machine[code]"]')?.value ?? ''),
                serial_number: document.querySelector('[name="machine[serial_number]"]')?.value ?? '',
                location: document.querySelector('[name="machine[location]"]')?.value ?? '',
                operator_name: document.querySelector('[name="machine[operator_name]"]')?.value ?? '',
                category: document.querySelector('[name="machine[category]"]')?.value ?? '',
                size: document.querySelector('[name="machine[size]"]')?.value ?? '',
                is_android: document.querySelector('[name="machine[is_android]"]')?.value ?? '1',
                status: document.querySelector('[name="machine[status]"]')?.value ?? 'active',
                condition_status: document.querySelector('[name="machine[condition_status]"]')?.value ?? 'good',
            };
        }

        function checkChanges() {
            const formData = new FormData(document.getElementById('settingsForm'));
            const current = {};

            for (let [key, value] of formData.entries()) {
                const cleanKey = key.replace('settings[', '').replace(']', '');
                current[cleanKey] = value;
            }

            let changed = false;
            const currentMachine = readMachineForm();

            Object.keys(currentMachine).forEach((key) => {
                if (String(currentMachine[key] ?? '') !== String(originalMachine[key] ?? '')) {
                    changed = true;
                }
            });

            const photoInput = machinePhotoInput();
            if (photoInput && photoInput.files.length > 0) {
                changed = true;
            }

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
            Object.keys(originalMachine).forEach((key) => {
                const input = document.querySelector(`[name="machine[${key}]"]`);

                if (!input) return;

                if (input.type === 'file') {
                    input.value = '';
                    return;
                }

                input.value = originalMachine[key] ?? '';
            });

            const photoInput = machinePhotoInput();
            if (photoInput) {
                photoInput.value = '';
            }

            const preview = machinePhotoPreview();
            if (preview) {
                preview.src = machinePreviewUrl(originalMachine.photo_url);
                preview.classList.toggle('hidden', !originalMachine.photo_url);
            }

            const photoLabel = machinePhotoName();
            if (photoLabel) {
                photoLabel.textContent = originalMachine.photo_url ? originalMachine.photo_url.split('/').pop() : 'No file chosen';
            }

            enforceMachineCode();

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
                            preview.src = setting.value ? `/image/${setting.value}` : '';
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
        enforceMachineCode();

        document.getElementById('settingsForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            enforceMachineCode();
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
                    confirmButtonColor: '#802A76'
                });

                return;
            }

            const result = await response.json();

            await Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: result.message,
                confirmButtonColor: '#802A76'
            });

            window.location.reload();
        });

        document.getElementById('settingsForm').addEventListener('input', function(e) {
            if (e.target.name && e.target.name.includes('settings[')) {
                checkChanges();
            }
        });

        document.getElementById('settingsForm').addEventListener('input', function(e) {
            if (e.target.name === 'machine[code]') {
                enforceMachineCode();
                checkChanges();
                return;
            }

            if (e.target.name && e.target.name.includes('machine[')) {
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
                confirmButtonColor: '#802A76',
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
                if (e.target.name === 'machine[photo_url]') {
                    previewMachinePhoto(e.target);
                }
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
