@extends('dashboard.layouts.app', [
    'title' => 'Detail Data Pengguna',
])

@section('content')
    <section class="space-y-6 p-2">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard.master-data.users.index') }}">
                    <img src="{{ asset('assets/icons/dashboard/back.svg') }}" alt="Kembali">
                </a>
                <h1 class="text-[28px] font-semibold leading-none text-[#3C1C5E]">Detail Data Pengguna</h1>
            </div>

            <p class="mt-3 text-[18px] text-[#4F3970]">
                Halaman ini untuk menampilkan data pengguna.
            </p>
        </div>

        <article class="rounded-[26px] border border-[#ddd2ef] bg-white p-10 shadow-[0_4px_10px_rgba(60,28,94,0.08)]">
            <div class="grid grid-cols-1 gap-x-20 gap-y-8 md:grid-cols-2">
                <div>
                    <p class="text-[13px] font-semibold text-[#7a6798]">Nama Lengkap</p>
                    <p class="mt-1 text-[18px] font-medium text-[#3C1C5E]">{{ $user->name }}</p>
                </div>
                <div>
                    <p class="text-[13px] font-semibold text-[#7a6798]">Nama Pengguna</p>
                    <p class="mt-1 text-[18px] font-medium text-[#3C1C5E]">{{ $user->username }}</p>
                </div>
                <div>
                    <p class="text-[13px] font-semibold text-[#7a6798]">Nomor Telepon</p>
                    <p class="mt-1 text-[18px] font-medium text-[#3C1C5E]">{{ $user->phone_number }}</p>
                </div>
                <div>
                    <p class="text-[13px] font-semibold text-[#7a6798]">Nomor Whatsapp</p>
                    <p class="mt-1 text-[18px] font-medium text-[#3C1C5E]">{{ $user->whatsapp_number ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-[13px] font-semibold text-[#7a6798]">Status</p>
                    <div class="mt-2">
                        @if ($user->is_active)
                            <span
                                class="inline-flex rounded-full bg-[#d7f2e1] px-4 py-1 text-[12px] font-semibold text-[#17914f]">
                                Aktif
                            </span>
                        @else
                            <span
                                class="inline-flex rounded-full bg-[#fde0e1] px-4 py-1 text-[12px] font-semibold text-[#de1c24]">
                                Tidak Aktif
                            </span>
                        @endif
                    </div>
                </div>
                <div>
                    <p class="text-[13px] font-semibold text-[#7a6798]">Kata Sandi</p>
                    <p class="mt-1 text-[18px] font-medium tracking-[2px] text-[#3C1C5E]">********</p>
                </div>
            </div>
        </article>
    </section>
@endsection
