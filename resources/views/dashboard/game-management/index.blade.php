@extends('dashboard.layouts.app', [
    'title' => 'Dashboard',
])

@section('content')
    @php
        $menus = [
            [
                'label' => 'Daftar Permainan',
                'route' => 'dashboard.game-management.games.index',
                'icon' => 'bi-joystick',
                'roles' => ['admin', 'operator'],
            ],
            [
                'label' => 'Daftar Soal',
                'route' => 'dashboard.game-management.quests.index',
                'icon' => 'bi-question-square-fill',
                'roles' => ['admin', 'operator'],
            ],
            [
                'label' => 'Daftar Hadiah',
                'route' => 'dashboard.game-management.rewards.index',
                'icon' => 'bi-gift-fill',
                'roles' => ['admin', 'operator'],
            ],
            [
                'label' => 'Riwayat Permainan',
                'route' => 'dashboard.game-management.game-history.index',
                'icon' => 'bi-play-circle-fill',
                'roles' => ['admin', 'operator', 'staff'],
            ],
        ];
    @endphp

    <section class="space-y-8 p-2">
        <div>
            <h1 class="text-[28px] font-semibold leading-none text-[#5E1C3D]">Manajemen Permainan</h1>
            <p class="mt-2 text-[18px] text-[#703967]">Halaman ini untuk menampilkan data yang diperlukan untuk
                permainan.</p>
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
