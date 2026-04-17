@php
    $topMenus = [
        [
            'label' => 'Dashboard',
            'route' => 'dashboard.index',
            'patterns' => ['dashboard.index'],
            'icon' => 'bi-grid-1x2',
            'active_icon' => 'bi-grid-1x2-fill',
            'roles' => ['admin', 'staff', 'operator'],
        ],
        [
            'label' => 'Transaksi',
            'route' => 'dashboard.transactions.index',
            'patterns' => ['dashboard.transactions.*'],
            'icon' => 'bi-bag-check',
            'active_icon' => 'bi-bag-check-fill',
            'roles' => ['admin', 'staff'],
        ],
        [
            'label' => 'Etalase Produk',
            'route' => 'dashboard.product-displays.index',
            'patterns' => ['dashboard.product-displays.*'],
            'icon' => 'bi-inboxes',
            'active_icon' => 'bi-inboxes-fill',
            'roles' => ['admin', 'staff', 'operator'],
        ],
        [
            'label' => 'Harga',
            'route' => 'dashboard.prices.index',
            'patterns' => ['dashboard.prices.*'],
            'icon' => 'bi-tags',
            'active_icon' => 'bi-tags-fill',
            'roles' => ['admin', 'operator'],
        ],
        [
            'label' => 'Produk',
            'route' => 'dashboard.products.index',
            'patterns' => ['dashboard.products.*'],
            'icon' => 'bi-box-seam',
            'active_icon' => 'bi-box-seam-fill',
            'roles' => ['admin', 'operator'],
        ],
        [
            'label' => 'Laporan',
            'route' => 'dashboard.reports.index',
            'patterns' => ['dashboard.reports.*'],
            'icon' => 'bi-bar-chart',
            'active_icon' => 'bi-bar-chart-fill',
            'roles' => ['admin', 'staff'],
        ],
        [
            'label' => 'Manajemen Permainan',
            'route' => 'dashboard.game-management.index',
            'patterns' => ['dashboard.game-management.*'],
            'icon' => 'bi-dice-5',
            'active_icon' => 'bi-dice-5-fill',
            'roles' => ['admin', 'staff', 'operator'],
        ],
        [
            'label' => 'Master Data',
            'route' => 'dashboard.master-data.index',
            'patterns' => ['dashboard.master-data.*'],
            'icon' => 'bi-database',
            'active_icon' => 'bi-database-fill',
            'roles' => ['admin', 'operator'],
        ],
        [
            'label' => 'Setelan Situs',
            'route' => 'dashboard.site-setting.index',
            'patterns' => ['dashboard.site-setting.index'],
            'icon' => 'bi-gear',
            'active_icon' => 'bi-gear-fill',
            'roles' => ['admin'],
        ]
    ];
@endphp

<nav id="dashboardSidebar"
    class="fixed left-0 top-0 z-40 flex h-screen w-56 -translate-x-full flex-col overflow-y-auto border-r border-[#e9e2f3] bg-white px-7 py-8 shadow-[2px_0_12px_rgba(71,39,110,0.06)] transition-transform duration-300 md:static md:translate-x-0">
    <div class="mb-2 flex justify-end md:hidden">
        <button id="sidebarCloseBtn" type="button" class="rounded-md p-1 text-[#802A76]" aria-label="Tutup sidebar">
            <i class="bi bi-x text-[24px] leading-none"></i>
        </button>
    </div>

    <div class="flex flex-col justify-left ml-1">
        <img src="{{ asset('assets/images/logo/nexsell.svg') }}" alt="Logo NEXSELL" title="Vending Machine NEXSELL"
            class="mt-5 w-[40px]" />
        <p class="text-[25px] font-bold text-[#802A76]">NEXSELL</p>
    </div>

    @php
        $currentRole = auth()->user()?->role?->slug;
    @endphp

    <ul class="mt-8 space-y-1">
        @foreach ($topMenus as $menu)
            @php
                $isActive = request()->routeIs(...$menu['patterns']);
                $allowedRoles = $menu['roles'] ?? null;
                $isAllowed = !$allowedRoles || ($currentRole && in_array($currentRole, $allowedRoles, true));
            @endphp
            @if (!$isAllowed)
                @continue
            @endif
            <li>
                <a href="{{ route($menu['route']) }}"
                    class="{{ $isActive ? 'text-[#5E1C3D] font-semibold' : 'text-[#5E1C3D] font-regular hover:text-[#481536]' }} relative flex items-center gap-2 rounded-xl px-2 py-3 text-base leading-none transition">
                    @if ($isActive)
                        <span class="absolute -left-8 h-10 w-3 rounded-r-full bg-[#741f4b]"></span>
                    @endif
                    <i class="bi {{ $isActive ? $menu['active_icon'] : $menu['icon'] }} text-[20px] leading-none"></i>
                    <span class="text-[14px] leading-6">{{ $menu['label'] }}</span>
                </a>
            </li>
        @endforeach
    </ul>

    {{-- <ul class="mt-auto pt-10">
        @foreach ($bottomMenus as $menu)
            @php
                $isActive = request()->routeIs(...$menu['patterns']);
            @endphp
            <li>
                <a href="{{ route($menu['route']) }}"
                    class="{{ $isActive ? 'text-[#5E1C3D] font-semibold' : 'text-[#5E1C3D] font-reguler hover:text-[#2f1548]' }} relative flex items-center gap-4 rounded-xl px-4 py-3 text-base leading-none transition">
                    @if ($isActive)
                        <span class="absolute -left-8 h-10 w-3 rounded-r-full bg-[#741f58]"></span>
                    @endif
                    <img src="{{ asset('assets/icons/dashboard/' . ($isActive ? $menu['active_icon'] : $menu['icon'])) }}"
                        alt="{{ $menu['label'] }}" class="h-7 w-7 shrink-0">
                    <span class="text-[15px] leading-6">{{ $menu['label'] }}</span>
                </a>
            </li>
        @endforeach
    </ul> --}}
</nav>
