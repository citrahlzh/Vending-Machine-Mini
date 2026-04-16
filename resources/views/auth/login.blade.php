@extends('auth.layouts.app', [
    'title' => 'Login Dashboard',
])

@section('content')
    <div
        class="w-full max-w-[550px] bg-white rounded-3xl shadow-[0_4px_4px_rgba(71,39,110,0.18)] border border-[#efe3fb] px-8 sm:px-12 py-12 relative">

        <h1 class="text-center text-2xl sm:text-2xl font-semibold text-[#000000]">Selamat Datang</h1>
        <p class="mt-2 text-center text-sm sm:text-[15px] leading-6 text-[#6b5a7a]">
            Masuk ke dashboard dan lakukan manajemen data secara menyeluruh untuk keperluan admin, konten, dan informasi di
            Vending Machine Anda.
        </p>

        @if ($errors->any())
            <div class="mt-6 rounded-lg border border-[#f2c7dd] bg-[#fbe9f2] px-3 py-2 text-sm text-[#8a1f52]">
                {{ $errors->first() }}
            </div>
        @endif

        <form class="mt-6" method="POST" action="{{ route('login.submit') }}">
            @csrf
            <div>
                <label class="block text-sm text-[#000000]" for="username">
                    Nama Pengguna <span class="text-red-500">*</span>
                </label>
                <div
                    class="relative">
                    <input class="w-full text-sm text-[#5E1C3D] placeholder:text-[#c7a4bf] mt-2 flex items-center gap-2 rounded-lg border border-[#d7c6e6] bg-white px-3 py-2.5 focus-within:border-[#8f3d87] focus-within:ring-2 focus-within:ring-[#8f3d87]/20"
                        id="username" name="username" type="text" placeholder="Masukkan Nama Pengguna"
                        value="{{ old('username') }}" required>
                    <div class="absolute right-3 top-1/2 -translate-y-1/2 w-auto object-contain bi bi-person-fill" style="color: #5f5f5f;"></div>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm text-[#000000]" for="password">
                    Kata Sandi <span class="text-red-500">*</span>
                </label>
                <div
                    class="relative">
                    <input class="w-full text-sm text-[#5E1C3D] placeholder:text-[#b7a4c7] mt-2 flex items-center gap-2 rounded-lg border border-[#d7c6e6] bg-white px-3 py-2.5 focus-within:border-[#8f3d87] focus-within:ring-2 focus-within:ring-[#8f3d87]/20"
                        id="password" name="password" type="password" placeholder="Masukkan kata sandi Anda" required>
                    <button id="togglePassword" type="button" class="absolute right-3 top-1/2 -translate-y-1/2">
                        <div id="passwordIcon" class="w-auto object-contain bi bi-eye-fill" style="color: #5f5f5f;"></div>
                    </button>
                </div>
            </div>

            <button
                class="mt-8 w-full rounded-lg bg-gradient-to-r from-[#802A76] to-[#814279] py-2.5 text-sm font-semibold text-white transition hover:brightness-105"
                type="submit">
                Masuk
            </button>
        </form>
    </div>
@endsection

@push('script')
    <script>
        (() => {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.getElementById('togglePassword');
            const passwordIcon = document.getElementById('passwordIcon');
            if (!passwordInput || !toggleButton || !passwordIcon) return;

            toggleButton.addEventListener('click', () => {
                const isHidden = passwordInput.type === 'password';
                passwordInput.type = isHidden ? 'text' : 'password';
                passwordIcon.className = isHidden
                    ? "w-auto object-contain bi bi-eye-slash-fill"
                    : "w-auto object-contain bi bi-eye-fill";
                passwordIcon.alt = isHidden ? 'Sembunyikan kata sandi' : 'Lihat kata sandi';
            });
        })();
    </script>
@endpush
