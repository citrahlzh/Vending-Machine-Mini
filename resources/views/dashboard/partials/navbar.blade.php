<header class="border-b border-[#ede8f6] bg-white">
    <div class="flex items-center justify-between pl-8 pr-9 py-4">
        <div class="flex items-center gap-4">
            <button id="sidebarOpenBtn" type="button"
                class="rounded-md p-2 text-[#5A2F7E] md:hidden" aria-label="Buka sidebar">
                <i class='bx bx-menu text-[22px]'></i>
            </button>
            <span class="h-[14px] w-[14px] rounded-full bg-[#50BE41]"></span>
            <p class="hidden text-[15px] font-regular leading-none text-[#4B2A6A] md:block">
                Mesin sedang aktif beroperasi
            </p>
        </div>

        <div class="flex items-center gap-3">
            <div class="relative">
                <button id="notificationButton" type="button"
                    class="relative text-[#5E377E] transition hover:text-[#4B2A6A]" aria-label="Notifikasi"
                    aria-haspopup="true" aria-expanded="false">
                    <img src="{{ asset('assets/icons/dashboard/notification.svg') }}" alt="Notifikasi" class="h-8 w-8">
                    <span id="notificationBadge"
                        class="absolute -right-1 -top-1 hidden min-w-[18px] rounded-full bg-[#d12f2f] px-1.5 text-center text-[11px] font-semibold text-white">
                        0
                    </span>
                </button>

                <div id="notificationPanel"
                    class="absolute right-0 top-[46px] z-30 hidden w-[360px] rounded-lg border border-[#e8e2f3] bg-white p-3 shadow-[0_10px_20px_rgba(60,28,94,0.12)]">
                    <div class="mb-2 flex items-center justify-between">
                        <p class="text-[15px] font-semibold text-[#3C1C5E]">Notifikasi</p>
                        <button id="markAllNotificationsRead" type="button"
                            class="text-[12px] font-medium text-[#5A2F7E] hover:underline">
                            Tandai semua dibaca
                        </button>
                    </div>

                    <div id="notificationList" class="max-h-[340px] space-y-2 overflow-y-auto pr-1">
                        <p class="py-4 text-center text-[13px] text-[#7a6a94]">Memuat notifikasi...</p>
                    </div>
                </div>
            </div>

            <div class="relative">
                <button id="profileMenuButton" type="button" aria-haspopup="true" aria-expanded="false"
                    class="overflow-hidden rounded-full border border-[#d9d3e7]">
                    <img src="{{ asset('assets/images/dashboard/profile.jpg') }}" alt="Profil"
                        class="h-[38px] w-[38px] rounded-full object-cover">
                </button>

                <div id="profileMenu"
                    class="absolute right-0 top-[46px] z-30 hidden w-36 rounded-lg border border-[#e8e2f3] bg-white p-2 shadow-[0_10px_20px_rgba(60,28,94,0.12)]">
                    <form id="logoutForm" method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="flex w-full items-center gap-2 rounded-md px-3 py-2 text-left text-[14px] font-medium text-[#c43434] hover:bg-[#fdefef]">
                            <i class='bx bx-log-out text-[18px]'></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    (() => {
        const profileButton = document.getElementById('profileMenuButton');
        const profileMenu = document.getElementById('profileMenu');
        const notificationButton = document.getElementById('notificationButton');
        const notificationPanel = document.getElementById('notificationPanel');
        const notificationBadge = document.getElementById('notificationBadge');
        const notificationList = document.getElementById('notificationList');
        const markAllReadButton = document.getElementById('markAllNotificationsRead');
        const logoutForm = document.getElementById('logoutForm');

        if (!profileButton || !profileMenu || !notificationButton || !notificationPanel || !notificationBadge || !
            notificationList || !markAllReadButton) return;

        const csrfToken = '{{ csrf_token() }}';

        const closeProfileMenu = () => {
            profileMenu.classList.add('hidden');
            profileButton.setAttribute('aria-expanded', 'false');
        };

        const closeNotificationPanel = () => {
            notificationPanel.classList.add('hidden');
            notificationButton.setAttribute('aria-expanded', 'false');
        };

        const updateBadge = (count) => {
            const safeCount = Number.isFinite(count) ? count : 0;
            if (safeCount > 0) {
                notificationBadge.textContent = safeCount > 99 ? '99+' : String(safeCount);
                notificationBadge.classList.remove('hidden');
                return;
            }

            notificationBadge.classList.add('hidden');
        };

        const escapeHtml = (value) => String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');

        const fetchUnreadCount = async () => {
            try {
                const response = await fetch('{{ route('dashboard.notifications.unread-count') }}', {
                    headers: {
                        Accept: 'application/json',
                    },
                });

                const payload = await response.json();
                if (!response.ok) return;

                updateBadge(Number(payload?.data?.count ?? 0));
            } catch (error) {
                // Ignore count fetch errors to keep navbar usable.
            }
        };

        const buildItemHtml = (item) => {
            const statusStyle = item.read_at ?
                'border-[#efe9f9] bg-white' :
                'border-[#e2d5f5] bg-[#f7f2ff]';
            const typeDotColor = item.type === 'success' ? 'bg-[#28a745]' : (item.type === 'warning' ? 'bg-[#e3a315]' :
                'bg-[#5A2F7E]');
            const actionUrl = typeof item.action_url === 'string' && (item.action_url.startsWith('/') || item
                .action_url.startsWith('http://') || item.action_url.startsWith('https://')) ? item.action_url : '';
            const actionButton = item.read_at ?
                '' :
                `<button type="button" data-notification-id="${item.id}" class="mark-notification-read text-[11px] font-semibold text-[#5A2F7E] hover:underline">Tandai dibaca</button>`;
            const actionLink = actionUrl ?
                `<a href="${escapeHtml(actionUrl)}" class="text-[11px] font-semibold text-[#5A2F7E] hover:underline">Lihat detail</a>` :
                '';

            return `
                <article class="rounded-md border p-2 ${statusStyle}">
                    <div class="mb-1 flex items-center justify-between gap-2">
                        <div class="flex min-w-0 items-center gap-2">
                            <span class="h-2 w-2 shrink-0 rounded-full ${typeDotColor}"></span>
                            <p class="truncate text-[13px] font-semibold text-[#3C1C5E]">${escapeHtml(item.title)}</p>
                        </div>
                        <p class="shrink-0 text-[10px] text-[#7f6f99]">${escapeHtml(item.created_at_human ?? '-')}</p>
                    </div>
                    <p class="text-[12px] leading-5 text-[#594779]">${escapeHtml(item.message)}</p>
                    <div class="mt-2 flex items-center gap-3">
                        ${actionButton}
                        ${actionLink}
                    </div>
                </article>
            `;
        };

        const fetchNotifications = async () => {
            try {
                const response = await fetch('{{ route('dashboard.notifications.list') }}?limit=10', {
                    headers: {
                        Accept: 'application/json',
                    },
                });
                const payload = await response.json();

                if (!response.ok) {
                    throw new Error(payload?.message || 'Gagal memuat notifikasi.');
                }

                const items = payload?.data || [];
                if (items.length === 0) {
                    notificationList.innerHTML =
                        '<p class="py-4 text-center text-[13px] text-[#7a6a94]">Belum ada notifikasi.</p>';
                    return;
                }

                notificationList.innerHTML = items.map(buildItemHtml).join('');
            } catch (error) {
                notificationList.innerHTML =
                    '<p class="py-4 text-center text-[13px] text-[#b43e3e]">Tidak bisa memuat notifikasi.</p>';
            }
        };

        const markNotificationRead = async (id) => {
            await fetch(`{{ url('/dashboard/notifications/read') }}/${id}`, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            });
        };

        profileButton.addEventListener('click', (event) => {
            event.stopPropagation();
            closeNotificationPanel();
            profileMenu.classList.toggle('hidden');
            profileButton.setAttribute('aria-expanded', profileMenu.classList.contains('hidden') ? 'false' : 'true');
        });

        notificationButton.addEventListener('click', async (event) => {
            event.stopPropagation();
            closeProfileMenu();
            notificationPanel.classList.toggle('hidden');
            notificationButton.setAttribute('aria-expanded', notificationPanel.classList.contains('hidden') ? 'false' :
                'true');

            if (!notificationPanel.classList.contains('hidden')) {
                await Promise.all([fetchNotifications(), fetchUnreadCount()]);
            }
        });

        markAllReadButton.addEventListener('click', async () => {
            try {
                await fetch('{{ route('dashboard.notifications.read-all') }}', {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                });
            } finally {
                await Promise.all([fetchNotifications(), fetchUnreadCount()]);
            }
        });

        notificationList.addEventListener('click', async (event) => {
            const target = event.target.closest('.mark-notification-read');
            if (!target) return;

            const id = target.getAttribute('data-notification-id');
            if (!id) return;

            try {
                await markNotificationRead(id);
            } finally {
                await Promise.all([fetchNotifications(), fetchUnreadCount()]);
            }
        });

        document.addEventListener('click', (event) => {
            if (!profileMenu.contains(event.target) && !profileButton.contains(event.target)) {
                closeProfileMenu();
            }

            if (!notificationPanel.contains(event.target) && !notificationButton.contains(event.target)) {
                closeNotificationPanel();
            }
        });

        if (logoutForm) {
            logoutForm.addEventListener('submit', async (event) => {
                event.preventDefault();

                if (!window.Swal) {
                    logoutForm.submit();
                    return;
                }

                const result = await Swal.fire({
                    icon: 'warning',
                    title: 'Logout sekarang?',
                    text: 'Sesi kamu akan diakhiri dan perlu login kembali.',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, logout',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#5A2F7E',
                    cancelButtonColor: '#9b90b0',
                });

                if (result.isConfirmed) {
                    logoutForm.submit();
                }
            });
        }

        fetchUnreadCount();
    })();
</script>

<script>
    (() => {
        const sidebar = document.getElementById('dashboardSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const openBtn = document.getElementById('sidebarOpenBtn');
        const closeBtn = document.getElementById('sidebarCloseBtn');

        if (!sidebar || !overlay || !openBtn || !closeBtn) return;

        const openSidebar = () => {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        };

        const closeSidebar = () => {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        };

        openBtn.addEventListener('click', openSidebar);
        closeBtn.addEventListener('click', closeSidebar);
        overlay.addEventListener('click', closeSidebar);
    })();
</script>
