<!DOCTYPE html>
<html lang="en" class="games-scrollbar-hidden">
<head>
    @include('games.partials.header')

    <style>
        html.games-scrollbar-hidden,
        html.games-scrollbar-hidden body {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        html.games-scrollbar-hidden::-webkit-scrollbar,
        html.games-scrollbar-hidden body::-webkit-scrollbar {
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
        @include('games.partials.footer')
    </footer>

    @include('games.partials.script')

    @stack('script')
</body>
</html>
