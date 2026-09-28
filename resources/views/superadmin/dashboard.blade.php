@extends('superadmin.layout')

@section('title', 'Superadmin Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Superadmin Welcome Banner -->
    <div class="relative overflow-hidden rounded-2xl bg-white border border-slate-200/80 p-6 sm:p-8 shadow-sm">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5 mb-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200/80">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600 mr-1.5 animate-pulse"></span>
                        {{ Auth::user()?->role ?? 'Superadmin' }}
                    </span>
                    <span class="text-xs font-semibold text-slate-400">
                        System Control Panel
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Hi, Welcome {{ Auth::user()?->name ?? 'Super Admin' }}!
                </h1>
                <p class="text-sm text-slate-500 mt-1">
                    Manage and monitor your WebSystem platform configuration, accounts, and application data.
                </p>
            </div>

            <!-- Quick Status Badge -->
            <div class="flex items-center gap-3">
                <div class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-600">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>System Status: Active</span>
                </div>
            </div>
        </div>

        <!-- Subtle Background Glow Accent -->
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-gradient-to-br from-blue-100 to-indigo-50 rounded-full blur-3xl opacity-60 pointer-events-none"></div>
    </div>
</div>
@endsection
