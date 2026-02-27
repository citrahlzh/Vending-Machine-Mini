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
            width: min(100vw, 600px);
            min-height: 100dvh;
            height: 100dvh;
            margin: 0 auto;
            background: #fff;
            overflow: hidden;
        }

        @media (min-width: 601px) {
            body {
                padding: 8px;
            }

            .landing-frame {
                border-radius: 20px;
                border: 1px solid #e7dcf8;
                box-shadow: 0 24px 60px rgba(60, 34, 97, 0.2);
            }
        }
    </style>

    @stack('style')
</head>
<body class="min-h-screen bg-[#f7f3ff]">
    <div class="landing-frame flex flex-col">
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-[#f7f3ff]">
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
