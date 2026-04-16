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

        .animated-gradient {
            background: linear-gradient(270deg, #802A76, #D354C5, #802A76, #D354C5);
            background-size: 100% 100%;
            animation: gradientAnimation 5s ease infinite;
        }
    </style>

    @stack('style')
</head>

<body class="flex flex-col">
    <main class="flex-1 relative">
        @stack('overlay')
        <div class="min-h-[calc(100vh-64px)] flex px-6 py-12 sm:px-10 lg:px-[72px] lg:py-16 bg-cover items-center justify-center"
            style="background-image: url({{ asset('assets/images/landing/background.svg') }})">
            @yield('content')
        </div>
    </main>

    <footer>
        @include('games.partials.footer')
    </footer>

    @include('games.partials.script')

    @stack('script')
</body>

</html>
