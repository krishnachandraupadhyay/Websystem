<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'WebSystem') }} - @yield('title', 'Dashboard')</title>

        <!-- Google Fonts: Plus Jakarta Sans -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

        <!-- Tailwind CSS CDN with Custom Theme Config -->
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            brand: {
                                50: '#eff6ff',
                                500: '#0080ff',
                                600: '#0070e0',
                                700: '#005bb5',
                            },
                            navy: {
                                800: '#1b2642',
                                900: '#0d172e',
                                950: '#091122',
                            }
                        },
                        fontFamily: {
                            sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        }
                    }
                }
            }
        </script>

        <!-- Alpine.js for Interactive Components -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- Vite Assets -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            [x-cloak] { display: none !important; }
            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
            }
            ::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }
            ::-webkit-scrollbar-track {
                background: #0d172e;
            }
            ::-webkit-scrollbar-thumb {
                background: #1e293b;
                border-radius: 9999px;
            }
            ::-webkit-scrollbar-thumb:hover {
                background: #334155;
            }
        </style>
        @stack('styles')
    </head>
    <body class="h-screen overflow-hidden bg-slate-100/70 text-slate-800 antialiased" x-data="{ sidebarOpen: window.innerWidth >= 1024 }">
        <div class="h-screen flex overflow-hidden">
            <!-- Mobile Sidebar Backdrop -->
            <div 
                x-show="sidebarOpen" 
                @click="sidebarOpen = false" 
                x-transition:enter="transition-opacity ease-linear duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-linear duration-300"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm z-40 lg:hidden"
                style="display: none;"
            ></div>

            <!-- Left Sidebar Partial -->
            @include('superadmin.partial.sidebar')

            <!-- Main Content Area with Topbar (Scrolls independently) -->
            <div class="flex-1 flex flex-col min-w-0 bg-slate-50 h-screen overflow-y-auto">
                <!-- Topbar Partial -->
                @include('superadmin.partial.topbar')

                <!-- Optional Page Header -->
                @hasSection('header')
                    <div class="px-4 sm:px-6 pt-6">
                        @yield('header')
                    </div>
                @elseif(isset($header))
                    <div class="px-4 sm:px-6 pt-6">
                        {{ $header }}
                    </div>
                @endif

                <!-- Main Content Area -->
                <main class="flex-1 p-4 sm:p-6 lg:p-8">
                    @hasSection('content')
                        @yield('content')
                    @else
                        {{ $slot ?? '' }}
                    @endif
                </main>
            </div>
        </div>
        @stack('scripts')
    </body>
</html>
