<!DOCTYPE html>
<html lang="en">
    <head>
        {{-- Header --}}
        @include("dashboard.partials.header")
        @include("dashboard.partials.datatables-style")

        {{-- Additional Style --}}
        @stack("style")
    </head>

    <body class="h-screen overflow-hidden">
        <div class="flex h-screen w-full overflow-hidden">
            <div id="sidebarOverlay" class="fixed inset-0 z-30 hidden bg-black/40 md:hidden"></div>

            {{-- Sidebar --}}
            @include("dashboard.partials.sidebar")

            <div class="flex min-w-0 flex-1 flex-col overflow-hidden bg-[#FAFAFA]">
                {{-- Navbar --}}
                @include('dashboard.partials.navbar')

                <div class="min-h-0 flex-1 overflow-x-hidden overflow-y-auto">
                    {{-- Main Content --}}
                    <main class="p-7 transition duration-500 ease-in-out">
                        @yield("content")
                    </main>

                    {{-- Footer --}}
                    <footer class="py-5">
                        @include('dashboard.partials.footer')
                    </footer>
                </div>
            </div>
        </div>

        {{-- Script --}}
        @include("dashboard.partials.script")

        {{-- Additional Script --}}
        @stack("script")
    </body>
</html>
