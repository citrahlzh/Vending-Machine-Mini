<script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
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
</script>
