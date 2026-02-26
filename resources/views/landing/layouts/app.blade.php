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

        .landing-frame {
            width: 100%;
            max-width: 500px;
            min-height: 100vh;
            margin: 0 auto;
            background: #fff;
        }

        @media (min-width: 501px) {
            .landing-frame {
                min-height: 800px;
                overflow: hidden;
            }
        }
    </style>

    @stack('style')
</head>
<body class="min-h-screen bg-[#f7f3ff]">
    <div class="landing-frame flex flex-col">
        <main class="flex-1 overflow-x-hidden bg-[#f7f3ff]">
            @yield('content')
        </main>

        @if (request()->routeIs('landing.index'))
            <footer class="shrink-0">
                @include('landing.partials.footer')
            </footer>
        @endif
    </div>

    @include('landing.partials.script')

    @stack('script')
</body>
</html>
