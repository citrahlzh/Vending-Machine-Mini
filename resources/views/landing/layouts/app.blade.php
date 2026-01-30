<!DOCTYPE html>
<html lang="en">
<head>
    @include('landing.partials.header')

    @stack('style')
</head>
<body>
    @yield('content')

    <footer>
        @include('landing.partials.footer')
    </footer>

    @include('landing.partials.script')

    @stack('script')
</body>
</html>
