<!-- Clean White Topbar (WebSystem Superadmin) -->
<header class="h-16 bg-white border-b border-slate-200/80 sticky top-0 z-30 px-4 sm:px-6 lg:px-8 flex items-center justify-between shrink-0 shadow-2xs">
    <!-- Left Section: Hamburger Toggle -->
    <div class="flex items-center">
        <!-- Responsive Hamburger Button for All Screen Sizes -->
        <button 
            @click="sidebarOpen = !sidebarOpen" 
            type="button"
            class="p-2 sm:p-2.5 rounded-xl text-slate-600 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 border border-slate-200/90 focus:outline-none focus:ring-2 focus:ring-blue-500/20 transition-all cursor-pointer shadow-2xs"
            title="Toggle Sidebar"
            aria-label="Toggle Sidebar"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>

    <!-- Right Section: Action Icons & User Mini Profile -->
    <div class="flex items-center gap-2 sm:gap-3">
        <!-- Grid Icon -->
        <button 
            type="button" 
            class="p-2 sm:p-2.5 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 border border-transparent hover:border-slate-200 transition-all cursor-pointer" 
            aria-label="Quick apps"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
            </svg>
        </button>

        <!-- Notification Bell with Dot -->
        <button 
            type="button" 
            class="relative p-2 sm:p-2.5 rounded-xl text-slate-500 hover:text-slate-800 hover:bg-slate-100 border border-transparent hover:border-slate-200 transition-all cursor-pointer" 
            aria-label="Notifications"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <span class="absolute top-2 right-2 w-2 h-2 rounded-full bg-orange-500 ring-2 ring-white"></span>
        </button>

        <!-- User Quick Info -->
        <div class="hidden sm:flex items-center gap-2.5 pl-3 border-l border-slate-200">
            <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center font-bold text-white text-xs ring-2 ring-blue-500/20">
                {{ strtoupper(substr(Auth::user()?->name ?? 'S', 0, 1)) }}
            </div>
            <div class="text-left leading-tight hidden md:block">
                <p class="text-xs font-bold text-slate-800">{{ Auth::user()?->name ?? 'Super Admin' }}</p>
                <p class="text-[10px] font-medium text-slate-400">{{ Auth::user()?->role ?? 'Superadmin' }}</p>
            </div>
        </div>
            </div>
</header>
    