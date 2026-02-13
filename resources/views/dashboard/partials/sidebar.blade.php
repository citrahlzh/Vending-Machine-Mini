@php
    $topMenus = [
        [
            'label' => 'Dashboard',
            'route' => 'dashboard.index',
            'patterns' => ['dashboard.index'],
            'icon' => 'dashboard.svg',
            'active_icon' => 'dashboard-active.svg',
        ],
        [
            'label' => 'Transaksi',
            'route' => 'dashboard.transactions.index',
            'patterns' => ['dashboard.transactions.*'],
            'icon' => 'transaction.svg',
            'active_icon' => 'transaction-active.svg',
        ],
        [
            'label' => 'Stok & Slot',
            'route' => 'dashboard.product-displays.index',
            'patterns' => ['dashboard.product-displays.*'],
            'icon' => 'display.svg',
            'active_icon' => 'display-active.svg',
        ],
        [
            'label' => 'Harga',
            'route' => 'dashboard.prices.index',
            'patterns' => ['dashboard.prices.*'],
            'icon' => 'price.svg',
            'active_icon' => 'price-active.svg',
        ],
        [
            'label' => 'Produk',
            'route' => 'dashboard.products.index',
            'patterns' => ['dashboard.products.*'],
            'icon' => 'product.svg',
            'active_icon' => 'product-active.svg',
        ],
        [
            'label' => 'Laporan',
            'route' => 'dashboard.reports.index',
            'patterns' => ['dashboard.reports.*'],
            'icon' => 'report.svg',
            'active_icon' => 'report-active.svg',
        ],
    ];

    $bottomMenus = [
        [
            'label' => 'Master Data',
            'route' => 'dashboard.master-data.index',
            'patterns' => ['dashboard.master-data.*'],
            'icon' => 'master-data.svg',
            'active_icon' => 'master-data-active.svg',
        ],
    ];
@endphp

<nav class="flex h-screen w-56 flex-col border-r border-[#e9e2f3] bg-white px-7 py-8 shadow-[2px_0_12px_rgba(71,39,110,0.06)]">
    <div class="flex justify-center">
        <img src="{{ asset('assets/images/logo/logo_x9.webp') }}" alt="Logo XNINE" title="Vending Machine XNINE"
            class="mt-5 w-[70px]" />
    </div>

    <ul class="mt-12 space-y-1">
        @foreach ($topMenus as $menu)
            @php
                $isActive = request()->routeIs(...$menu['patterns']);
            @endphp
            <li>
                <a href="{{ route($menu['route']) }}"
                    class="{{ $isActive ? 'text-[#3C1C5E] font-semibold' : 'text-[#3C1C5E] font-regular hover:text-[#2f1548]' }} relative flex items-center gap-4 rounded-xl px-4 py-3 text-base leading-none transition">
                    @if ($isActive)
                        <span class="absolute -left-8 h-10 w-3 rounded-r-full bg-[#4B1F74]"></span>
                    @endif
                    <img src="{{ asset('assets/icons/dashboard/' . ($isActive ? $menu['active_icon'] : $menu['icon'])) }}"
                        alt="{{ $menu['label'] }}" class="h-7 w-7 shrink-0">
                    <span class="text-[15px] leading-6">{{ $menu['label'] }}</span>
                </a>
            </li>
        @endforeach
    </ul>

    <ul class="mt-auto pt-10">
        @foreach ($bottomMenus as $menu)
            @php
                $isActive = request()->routeIs(...$menu['patterns']);
            @endphp
            <li>
                <a href="{{ route($menu['route']) }}"
                    class="{{ $isActive ? 'text-[#3C1C5E] font-semibold' : 'text-[#3C1C5E] font-reguler hover:text-[#2f1548]' }} relative flex items-center gap-4 rounded-xl px-4 py-3 text-base leading-none transition">
                    @if ($isActive)
                        <span class="absolute -left-8 h-10 w-3 rounded-r-full bg-[#4B1F74]"></span>
                    @endif
                    <img src="{{ asset('assets/icons/dashboard/' . ($isActive ? $menu['active_icon'] : $menu['icon'])) }}"
                        alt="{{ $menu['label'] }}" class="h-7 w-7 shrink-0">
                    <span class="text-[15px] leading-6">{{ $menu['label'] }}</span>
                </a>
            </li>
        @endforeach
    </ul>
</nav>
