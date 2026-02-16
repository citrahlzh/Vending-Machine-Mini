<!DOCTYPE html>
<html lang="en" class="landing-scrollbar-hidden">
<head>
    @include('landing.partials.header')

    <style>
        html.landing-scrollbar-hidden,
        html.landing-scrollbar-hidden body {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        html.landing-scrollbar-hidden::-webkit-scrollbar,
        html.landing-scrollbar-hidden body::-webkit-scrollbar {
            width: 0;
            height: 0;
            display: none;
        }
    </style>

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
