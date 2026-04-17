<!DOCTYPE html>
<html lang="en">

<head>
    {{-- Header --}}
    @include('auth.partials.header')

    {{-- Additional Style --}}
    @stack('style')
</head>

<body class="bg-[#FFFFFF] flex flex-col items-center justify-center">

    <main class="min-h-screen flex flex-grow items-center justify-center px-4 py-10">
        {{-- Main Content --}}
        @yield('content')
    </main>

    <footer class="absolute bottom-0 items-center justify-center py-5">
        {{-- Footer --}}
        @include('auth.partials.footer')
    </footer>

    {{-- Script --}}
    @include('auth.partials.script')

    {{-- Additional Script --}}
    @stack('script')
</body>

</html>
