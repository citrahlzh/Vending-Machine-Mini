@extends('dashboard.layouts.app', [
    'title' => 'Dashboard'
])

@section('content')
    @php
        $menus = [
            [
                'label' => 'Daftar Pengguna',
                'route' => 'dashboard.master-data.users.index',
                'icon' => 'bi-people-fill',
                'roles' => ['admin'],
            ],
            // [
            //     'label' => 'Role Pengguna',
            //     'route' => 'dashboard.master-data.roles.index',
            //     'icon' => 'bi-person-badge-fill',
            // ],
            [
                'label' => 'Merek',
                'route' => 'dashboard.master-data.brands.index',
                'icon' => 'bi-upc-scan',
                'roles' => ['admin', 'operator'],
            ],
            [
                'label' => 'Sel Produk',
                'route' => 'dashboard.master-data.cells.index',
                'icon' => 'bi-inboxes-fill',
                'roles' => ['admin', 'operator'],
            ],
            [
                'label' => 'Kategori Produk',
                'route' => 'dashboard.master-data.categories.index',
                'icon' => 'bi-tags-fill',
                'roles' => ['admin', 'operator'],
            ],
            [
                'label' => 'Jenis Kemasan',
                'route' => 'dashboard.master-data.packaging-types.index',
                'icon' => 'bi-box-seam-fill',
                'roles' => ['admin', 'operator'],
            ],
            [
                'label' => 'Ukuran Kemasan',
                'route' => 'dashboard.master-data.packaging-sizes.index',
                'icon' => 'bi-rulers',
                'roles' => ['admin', 'operator'],
            ],
            [
                'label' => 'Iklan',
                'route' => 'dashboard.master-data.ads.index',
                'icon' => 'bi-badge-ad-fill',
                'roles' => ['admin', 'operator'],
            ],
        ];
    @endphp

    <section class="space-y-8 p-2">
        <div>
            <h1 class="text-[28px] font-semibold leading-none text-[#5E1C3D]">Master Data</h1>
            <p class="mt-2 text-[18px] text-[#703967]">Halaman ini untuk menampilkan data yang diperlukan untuk keperluan pendataan.</p>
        </div>

        <div class="grid gap-6 md:grid-cols-3 xl:grid-cols-4">
            @foreach ($menus as $menu)
                @if (in_array(auth()->user()->role, $menu['roles']))
                    <a href="{{ route($menu['route']) }}"
                        class="group flex min-h-[135px] items-center gap-4 rounded-2xl border border-[#efd2ea] bg-[#802A76] px-5 py-4 transition hover:-translate-y-0.5">
                        <div class="flex h-[95px] w-[95px] shrink-0 items-center justify-center rounded-xl bg-white/20">
                            <div class="bi {{ $menu['icon'] }} text-[45px] text-white"></div>
                        </div>
                        <span class="text-[20px] font-medium leading-8 text-white">
                            {{ $menu['label'] }}
                        </span>
                    </a>
                @endif
            @endforeach
        </div>
    </section>
@endsection
