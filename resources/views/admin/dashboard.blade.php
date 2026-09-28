@extends('superadmin.layout')

@section('title', 'Admin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Flash Error Notification if redirected -->
    @if(session('error'))
        <div 
            x-data="{ show: true }" 
            x-init="setTimeout(() => show = false, 5000)" 
            x-show="show" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-400"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-semibold flex items-center justify-between shadow-2xs"
        >
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
            <button @click="show = false" class="text-rose-500 hover:text-rose-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    <!-- Admin Welcome Header Banner -->
    <div class="relative overflow-hidden rounded-2xl bg-white border border-slate-200/80 p-6 sm:p-8 shadow-sm">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5 mb-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/80">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600 mr-1.5 animate-pulse"></span>
                        Admin Workspace
                    </span>
                    <span class="text-xs font-semibold text-slate-400">
                        Assigned Sections & Components Portal
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Hi, Welcome {{ Auth::user()?->name ?? 'Admin' }}!
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Below are the website sections and configured components assigned to your account.
                </p>
            </div>

            <!-- Quick Stats -->
            <div class="flex items-center gap-3">
                <div class="inline-flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-700">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>Assigned Sections: <strong class="text-slate-900 text-sm font-extrabold">{{ $assignedSections->count() }}</strong></span>
                </div>
                <div class="inline-flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-blue-50/80 border border-blue-200 text-xs font-bold text-blue-800">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/>
                    </svg>
                    <span>Total Active Components: <strong class="text-blue-900 text-sm font-extrabold">{{ $assignedSections->sum(fn($s) => $s->components->count()) }}</strong></span>
                </div>
            </div>
        </div>

        <!-- Subtle Background Glow Accent -->
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-gradient-to-br from-blue-100 to-indigo-50 rounded-full blur-3xl opacity-60 pointer-events-none"></div>
    </div>

    <!-- Assigned Sections Section Header -->
    <div class="flex items-center justify-between pt-2">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Your Assigned Website Sections</h2>
            <p class="text-xs text-slate-500">Only the sections assigned to your account are accessible here.</p>
        </div>
        <span class="text-xs font-semibold px-3 py-1 rounded-full bg-slate-100 text-slate-600 border border-slate-200">
            Showing {{ $assignedSections->count() }} Section(s)
        </span>
    </div>

    <!-- Sections Grid -->
    @if($assignedSections->isNotEmpty())
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($assignedSections as $section)
                <div class="group bg-white rounded-2xl border border-slate-200/80 hover:border-blue-400 p-6 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between">
                    <div>
                        <!-- Section Header Badge & Icon -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-extrabold text-sm group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                {{ strtoupper(substr($section->section_name, 0, 2)) }}
                            </div>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $section->components->count() > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $section->components->count() > 0 ? 'bg-emerald-500' : 'bg-slate-400' }} mr-1.5"></span>
                                {{ $section->components->count() }} Components
                            </span>
                        </div>

                        <!-- Section Title & Slug -->
                        <h3 class="text-lg font-bold text-slate-900 group-hover:text-blue-600 transition-colors">
                            {{ $section->section_name }}
                        </h3>
                        <p class="text-xs font-mono text-slate-400 mt-0.5">
                            /{{ $section->section_slug }}
                        </p>
                        @if($section->description)
                            <p class="text-xs text-slate-500 mt-2 line-clamp-2">
                                {{ $section->description }}
                            </p>
                        @endif

                        <!-- Assigned Components List / Tags -->
                        <div class="mt-4 pt-4 border-t border-slate-100">
                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Configured Components:</p>
                            @if($section->components->isNotEmpty())
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($section->components as $comp)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-50 text-slate-700 border border-slate-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                            {{ $comp->component_name }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-xs text-slate-400 italic">
                                    <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    No components added in this section yet
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- View Section Action Button -->
                    <div class="mt-6 pt-4 border-t border-slate-100">
                        <a 
                            href="{{ route('admin.section.view', $section->id) }}" 
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs text-white bg-slate-900 hover:bg-blue-600 transition-colors shadow-xs"
                        >
                            <span>Open Section</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State When No Sections Assigned -->
        <div class="rounded-2xl border-2 border-dashed border-slate-200 bg-white p-12 text-center">
            <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-800">No Sections Assigned Yet</h3>
            <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1 leading-relaxed">
                Super Admin has not assigned any active website sections to your account. Please get in touch with the Super Admin to receive access.
            </p>
        </div>
    @endif

</div>
@endsection
