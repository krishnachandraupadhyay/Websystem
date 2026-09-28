<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-[#f3f6fb]">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'WebSystem') }} - Dashboard</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

        <!-- Tailwind CSS CDN -->
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
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
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
    </head>
    <body class="h-screen overflow-hidden bg-[#f3f6fb] text-slate-800 antialiased" x-data="{ sidebarOpen: window.innerWidth >= 1024 }">
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
                class="fixed inset-0 bg-slate-950/70 backdrop-blur-xs z-40 lg:hidden"
                style="display: none;"
            ></div>

            <!-- Dark Navy Left Sidebar -->
            <aside 
                class="fixed inset-y-0 left-0 z-50 bg-[#0d172e] text-slate-300 flex flex-col transition-all duration-300 ease-in-out lg:static shrink-0 h-screen overflow-hidden"
                :class="sidebarOpen ? 'w-64 translate-x-0 opacity-100 shadow-2xl lg:shadow-none' : 'w-0 -translate-x-full lg:translate-x-0 lg:w-0 opacity-0 pointer-events-none border-none'"
            >
                <div class="w-64 flex flex-col h-full shrink-0">
                    <!-- Brand Header -->
                    <div class="h-16 flex items-center justify-between px-5 border-b border-slate-800/80">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5">
                        <!-- Blue Shield Emblem -->
                        <div class="w-8 h-8 rounded-lg bg-[#0080ff] flex items-center justify-center text-white shadow-sm shadow-blue-500/30">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <span class="text-lg font-bold tracking-tight text-white">Web<span class="text-[#0088ff]">System</span></span>
                    </a>

                    <!-- Close button for mobile -->
                    <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Navigation Menu Links -->
                <nav class="flex-1 px-3 space-y-2 overflow-y-auto py-4 text-xs font-medium">
                    <!-- MAIN -->
                    <div>
                        <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">MAIN</p>
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-semibold transition-all {{ request()->routeIs('dashboard') ? 'bg-[#1b2642] text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                            <span>Dashboard</span>
                        </a>
                    </div>
                </nav>

                <!-- Sidebar Bottom Profile Section -->
                <div class="p-3 border-t border-slate-800/90 bg-[#091122]">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center font-bold text-white text-xs ring-2 ring-blue-400/30">
                                {{ strtoupper(substr(Auth::user()?->name ?? 'S', 0, 1)) }}
                            </div>
                            <div class="truncate">
                                <p class="text-xs font-bold text-white truncate">{{ Auth::user()?->name ?? 'Super Admin' }}</p>
                                <p class="text-[10px] text-slate-400 truncate">{{ Auth::user()?->role ?? 'Superadmin' }}</p>
                            </div>
                        </div>

                        <!-- Settings & Logout trigger -->
                        <div class="relative" x-data="{ openMenu: false }">
                            <button @click="openMenu = !openMenu" class="text-slate-400 hover:text-white p-1.5 rounded-md hover:bg-slate-800 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </button>

                            <div 
                                x-show="openMenu" 
                                @click.away="openMenu = false"
                                class="absolute bottom-full left-0 mb-2 w-48 rounded-xl bg-[#0f172a] border border-slate-700 shadow-xl py-1 text-xs z-50 divide-y divide-slate-800"
                                style="display: none;"
                            >
                                <div class="px-3 py-2 text-slate-300">
                                    <p class="font-bold text-white">{{ Auth::user()?->name ?? 'Admin' }}</p>
                                    <p class="text-[10px] text-slate-400 truncate">{{ Auth::user()?->email }}</p>
                                </div>
                                <div class="py-1">
                                    <a href="{{ route('profile.edit') }}" class="block px-3 py-1.5 text-slate-300 hover:text-white hover:bg-slate-800">
                                        Account Settings
                                    </a>
                                </div>
                                <div class="py-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-3 py-1.5 text-rose-400 hover:bg-rose-500/10">
                                            Sign Out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Content Area with Clean Topbar -->
            <div class="flex-1 flex flex-col min-w-0 bg-[#f3f6fb] h-screen overflow-y-auto">
                <!-- Clean White Topbar -->
                <header class="h-16 bg-white border-b border-slate-200/80 sticky top-0 z-30 px-4 sm:px-6 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <!-- Responsive Hamburger Button for All Screen Sizes -->
                        <button 
                            @click="sidebarOpen = !sidebarOpen" 
                            type="button"
                            class="p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-all cursor-pointer"
                            title="Toggle Sidebar"
                            aria-label="Toggle Sidebar"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>

                        <!-- Search Bar -->
                        <div class="relative w-64 sm:w-80 md:w-96">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                placeholder="Search patients, records..." 
                                class="w-full pl-9 pr-4 py-2 rounded-xl bg-[#f8fafc] border border-slate-200 text-xs sm:text-sm text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500 transition-all"
                            />
                        </div>
                    </div>

                    <!-- Right Action Icons -->
                    <div class="flex items-center gap-2 sm:gap-3">
                        <!-- Grid Icon -->
                        <button class="p-2 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                            </svg>
                        </button>

                        <!-- Notification Bell with Red Dot -->
                        <button class="relative p-2 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <!-- Red Notification Dot -->
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-[#f97316]"></span>
                        </button>
                    </div>
                </header>

                <!-- Page Header (if passed) -->
                @isset($header)
                    <div class="px-4 sm:px-6 pt-6">
                        {{ $header }}
                    </div>
                @endisset

                <!-- Main Content Slot -->
                <main class="flex-1 p-4 sm:p-6 lg:p-7">
                    {{ $slot }}
                </main>
            </div>
        </div>
    </body>
</html>
