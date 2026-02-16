<!DOCTYPE html>
<html lang="en">
    <head>
        {{-- Header --}}
        @include("dashboard.partials.header")
        @include("dashboard.partials.datatables-style")

        {{-- Additional Style --}}
        @stack("style")
    </head>

    <body>
        <div class="flex h-screen w-full">
            <div id="sidebarOverlay" class="fixed inset-0 z-30 hidden bg-black/40 md:hidden"></div>

            {{-- Sidebar --}}
            @include("dashboard.partials.sidebar")

            <div class="flex min-w-0 flex-1 flex-col bg-[#F7F3FF]">
                {{-- Navbar --}}
                @include('dashboard.partials.navbar')

                {{-- Main Content --}}
                <main class="min-h-0 flex-1 overflow-y-auto p-7 transition duration-500 ease-in-out">
                    @yield("content")
                </main>
            </div>
        </div>

        {{-- Script --}}
        @include("dashboard.partials.script")

        {{-- Additional Script --}}
        @stack("script")
    </body>
</html>
