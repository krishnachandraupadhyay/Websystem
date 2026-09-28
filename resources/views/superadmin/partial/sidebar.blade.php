<!-- Dark Navy Left Sidebar (WebSystem Superadmin) -->
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
            <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1" aria-label="Close sidebar">
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
            @if(Auth::user()?->role === 'Super Admin')
                <!-- SUPER ADMIN MANAGEMENT -->
                <div>
                    <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Admin</p>
                    <a href="{{ route('Superadmin.manageadmin') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-semibold transition-all {{ request()->routeIs('Superadmin.manageadmin*') ? 'bg-[#1b2642] text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <span>Admin</span>
                    </a>
                    <a href="{{ route('Superadmin.assignsection') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-semibold transition-all {{ request()->routeIs('Superadmin.assignsection*') ? 'bg-[#1b2642] text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <span>Assign Section</span>
                    </a>
                </div>
                <div>
                    <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Pages</p>
                    <a href="{{ route('Superadmin.addpages') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-semibold transition-all {{ request()->routeIs('Superadmin.addpages*') || request()->routeIs('addpage*') ? 'bg-[#1b2642] text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Add pages</span>
                    </a>
                </div>
                <div>
                    <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Sections</p>
                    <a href="{{ route('Superadmin.addsection') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-semibold transition-all {{ request()->routeIs('Superadmin.addsection*') ? 'bg-[#1b2642] text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                        <span>Add Section</span>
                    </a>
                    <a href="{{ route('Superadmin.managesection') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-semibold transition-all {{ request()->routeIs('Superadmin.managesection*') ? 'bg-[#1b2642] text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Manage Section</span>
                    </a>
                </div>
                <div>
                    <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">Component Master</p>
                    <a href="{{ route('Superadmin.addcomponent') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg font-semibold transition-all {{ request()->routeIs('Superadmin.addcomponent*') ? 'bg-[#1b2642] text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/>
                        </svg>
                        <span>Add Component</span>
                    </a>
                </div>
            @else
                <!-- ADMIN ASSIGNED SECTIONS ONLY -->
                <div>
                    <p class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">My Assigned Sections</p>
                    @php
                        $userAssignedSections = Auth::user() ? Auth::user()->sections()->wherePivot('status', 1)->get() : collect();
                    @endphp
                    @if($userAssignedSections->isNotEmpty())
                        <div class="space-y-1">
                            @foreach($userAssignedSections as $assignedSec)
                                @php
                                    $isActive = request()->is('admin/section/' . $assignedSec->id);
                                @endphp
                                <a 
                                    href="{{ route('admin.section.view', $assignedSec->id) }}" 
                                    class="flex items-center justify-between px-3 py-2.5 rounded-lg font-semibold transition-all {{ $isActive ? 'bg-[#1b2642] text-white shadow-xs' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}"
                                >
                                    <div class="flex items-center gap-2.5 truncate">
                                        <svg class="w-4 h-4 {{ $isActive ? 'text-blue-400' : 'text-slate-400' }} shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                        </svg>
                                        <span class="truncate">{{ $assignedSec->section_name }}</span>
                                    </div>
                                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded-full {{ $isActive ? 'bg-blue-500 text-white' : 'bg-slate-800 text-slate-400' }}">
                                        {{ $assignedSec->components()->wherePivot('status', 1)->count() }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="px-3 py-3 rounded-lg bg-slate-800/40 border border-slate-800/60 text-slate-400 text-[11px] leading-relaxed">
                            <div class="flex items-center gap-1.5 text-amber-400 font-bold mb-1">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <span>No sections</span>
                            </div>
                            No sections assigned to your account yet.
                        </div>
                    @endif
                </div>
            @endif
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
                    <button @click="openMenu = !openMenu" class="text-slate-400 hover:text-white p-1.5 rounded-md hover:bg-slate-800 transition-colors" aria-label="User menu">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </button>

                    <div 
                        x-show="openMenu" 
                        @click.away="openMenu = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute bottom-full right-0 mb-2 w-48 rounded-xl bg-[#0f172a] border border-slate-700 shadow-xl py-1 text-xs z-50 divide-y divide-slate-800"
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
    </div>
</aside>
