@vite("resources/js/app.js")

<script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>

<script src="{{ asset("assets/js/jquery.js") }}"></script>
<script src="{{ asset("assets/js/jquery.datatables.min.js") }}"></script>
@include("dashboard.partials.datatables-script")

{{-- Sweetalert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    (() => {
        const originalFetch = window.fetch.bind(window);
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        window.fetch = (input, init = {}) => {
            const requestUrl = typeof input === 'string' ? input : (input?.url || '');
            const url = new URL(requestUrl, window.location.origin);
            const method = String(init?.method || (typeof input !== 'string' ? input?.method : 'GET') || 'GET')
                .toUpperCase();

            if (url.origin !== window.location.origin || method === 'GET' || method === 'HEAD' || method === 'OPTIONS') {
                return originalFetch(input, init);
            }

            const headers = new Headers(init.headers || (typeof input !== 'string' ? input?.headers : undefined));
            if (csrfToken && !headers.has('X-CSRF-TOKEN')) {
                headers.set('X-CSRF-TOKEN', csrfToken);
            }
            if (!headers.has('X-Requested-With')) {
                headers.set('X-Requested-With', 'XMLHttpRequest');
            }

            return originalFetch(input, {
                ...init,
                headers,
                credentials: init.credentials || 'same-origin',
            });
        };
    })();

    let successMessage = @json(session('success'));
    let errorMessage = @json(session('error'));

    if (successMessage) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: successMessage,
            showConfirmButton: false,
            timer: 3000
        });
    }

    if (errorMessage) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: errorMessage,
            showConfirmButton: false,
            timer: 3000
        });
    }

    (() => {
        const isCancelControl = (element) => {
            if (!element) return false;
            if (element.closest('.swal2-container')) return false;

            const id = (element.id || '').toLowerCase();
            const className = typeof element.className === 'string' ? element.className.toLowerCase() : '';
            const text = (element.textContent || element.value || '').trim().toLowerCase();
            const dataAction = String(element.getAttribute('data-action') || '').toLowerCase();

            return id.includes('cancel') ||
                className.includes('cancel') ||
                dataAction === 'cancel' ||
                text === 'batal';
        };

        document.addEventListener('click', async (event) => {
            const target = event.target.closest('button, input[type="button"], input[type="submit"], a');
            if (!target || target.disabled) return;
            if (!isCancelControl(target)) return;

            if (target.dataset.cancelConfirmed === '1') {
                delete target.dataset.cancelConfirmed;
                return;
            }

            event.preventDefault();
            event.stopImmediatePropagation();

            const result = await Swal.fire({
                icon: 'warning',
                title: 'Batalkan aksi?',
                text: 'Perubahan yang belum disimpan bisa hilang.',
                showCancelButton: true,
                confirmButtonText: 'Ya, batalkan',
                cancelButtonText: 'Tidak',
                confirmButtonColor: '#5A2F7E',
                cancelButtonColor: '#9b90b0',
            });

            if (!result.isConfirmed) return;

            target.dataset.cancelConfirmed = '1';

            if (target.tagName === 'A') {
                window.location.href = target.href;
                return;
            }

            target.click();
        }, true);
    })();
</script>
