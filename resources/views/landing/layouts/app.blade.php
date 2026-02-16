<!DOCTYPE html>
<html lang="en">
<head>
    @include('landing.partials.header')

    @stack('style')
</head>
<body class="min-h-screen flex flex-col">
    <main class="flex-1">
        @yield('content')
    </main>

    <footer>
        @include('landing.partials.footer')
    </footer>

    @include('landing.partials.script')

    @stack('script')
</body>
</html>
