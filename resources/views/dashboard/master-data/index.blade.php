@extends('dashboard.layouts.app', [
    'title' => 'Dashboard'
])

@section('content')
    @php
        $menus = [
            [
                'label' => 'Daftar Pengguna',
                'route' => 'dashboard.master-data.users.index',
                'icon' => 'users.svg',
            ],
            [
                'label' => 'Merek',
                'route' => 'dashboard.master-data.brands.index',
                'icon' => 'brand.svg',
            ],
            [
                'label' => 'Sel Produk',
                'route' => 'dashboard.master-data.cells.index',
                'icon' => 'cells.svg',
            ],
            [
                'label' => 'Kategori Produk',
                'route' => 'dashboard.master-data.categories.index',
                'icon' => 'categories.svg',
            ],
            [
                'label' => 'Jenis Kemasan',
                'route' => 'dashboard.master-data.packaging-types.index',
                'icon' => 'package.svg',
            ],
            [
                'label' => 'Ukuran Kemasan',
                'route' => 'dashboard.master-data.packaging-sizes.index',
                'icon' => 'sizes.svg',
            ],
            [
                'label' => 'Iklan',
                'route' => 'dashboard.master-data.ads.index',
                'icon' => 'ads.svg',
            ],
        ];
    @endphp

    <section class="space-y-8 p-2">
        <div>
            <h1 class="text-[28px] font-semibold leading-none text-[#3C1C5E]">Master Data</h1>
            <p class="mt-2 text-[18px] text-[#4F3970]">Halaman ini untuk menampilkan data yang diperlukan untuk keperluan pendataan.</p>
        </div>

        <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($menus as $menu)
                <a href="{{ route($menu['route']) }}"
                    class="group flex min-h-[135px] items-center gap-4 rounded-2xl border border-[#ddd2ef] bg-white px-5 py-4 transition hover:-translate-y-0.5">
                    <div class="flex h-[95px] w-[95px] shrink-0 items-center justify-center rounded-xl bg-[#D9C9EB]">
                        <img src="{{ asset('assets/icons/dashboard/' . $menu['icon']) }}" alt="{{ $menu['label'] }}"
                            class="h-15 w-15 text-[#4B1F74]">
                    </div>
                    <span class="text-[23px] font-medium leading-8 text-[#111111]">
                        {{ $menu['label'] }}
                    </span>
                </a>
            @endforeach
        </div>
    </section>
@endsection
