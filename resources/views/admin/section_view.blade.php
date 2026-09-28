@extends('superadmin.layout')

@section('title', $section->section_name . ' - Section Content')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{ openModal: false, activeComponentId: null, activeComponentName: '' }">

    <!-- Top Breadcrumb & Navigation -->
    <div class="flex items-center justify-between">
        <a 
            href="{{ route('dashboard') }}" 
            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 hover:text-slate-900 transition-colors shadow-2xs"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            <span>Back to Dashboard</span>
        </a>

        <div class="flex items-center gap-2 text-xs font-semibold text-slate-400">
            <span>Admin</span>
            <span>/</span>
            <span>Sections</span>
            <span>/</span>
            <span class="text-slate-800 font-bold">{{ $section->section_name }}</span>
        </div>
    </div>

    <!-- Success Flash Notification (Auto-dismisses in 5s) -->
    @if(session('success'))
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
            class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center justify-between shadow-2xs"
        >
            <div class="flex items-center gap-2.5">
                <div class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <span>{{ session('success') }}</span>
            </div>
            <button @click="show = false" type="button" class="text-emerald-500 hover:text-emerald-700 p-1 rounded-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    <!-- Content Table Card -->
    <div class="bg-white rounded-2xl p-6 sm:p-7 border border-slate-200/80 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <h2 class="text-xl font-bold text-slate-800">{{ $section->section_name }}</h2>
                    <span class="text-xs font-mono px-2 py-0.5 rounded bg-slate-100 text-slate-500 font-semibold">/{{ $section->section_slug }}</span>
                </div>
                <p class="text-xs text-slate-400">Assigned components & live content saved directly in the database.</p>
            </div>
            <button 
                @click="openModal = true; activeComponentId = null; activeComponentName = ''" 
                type="button" 
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 transition-colors shadow-sm shadow-blue-500/20 cursor-pointer self-start sm:self-auto"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <span>Add / Edit Content</span>
            </button>
        </div>

        @if($section->components->isNotEmpty())
            <div class="overflow-x-auto rounded-xl border border-slate-200/80">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-[11px] font-bold tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">#</th>
                            <th class="px-5 py-3.5">Component</th>
                            <th class="px-5 py-3.5">Type</th>
                            <th class="px-5 py-3.5">Configured Content / Preview</th>
                            <th class="px-5 py-3.5 text-center">Status</th>
                            <th class="px-5 py-3.5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($section->components as $index => $comp)
                            @php
                                $nameAndSlug = strtolower($comp->component_name . ' ' . $comp->component_slug);
                                $existing = $contentData[$comp->id] ?? null;
                                $hasSubComps = $comp->effective_subcomponents->isNotEmpty();
                            @endphp

                            @if($hasSubComps)
                                {{-- 1. Parent Component Container Row (e.g. Card) --}}
                                <tr class="bg-indigo-50/30 hover:bg-indigo-50/60 transition-colors border-t-2 border-indigo-200">
                                    <td class="px-5 py-4 text-xs font-bold text-indigo-700">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-xs">
                                                {{ strtoupper(substr($comp->component_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <h4 class="font-bold text-slate-900 text-xs">
                                                        {{ $comp->component_name }}
                                                    </h4>
                                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-800">
                                                        Container
                                                    </span>
                                                </div>
                                                <p class="text-[10px] text-slate-400 font-mono">
                                                    /{{ $comp->component_slug }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                            Card ({{ $comp->effective_subcomponents->count() }} Components)
                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($comp->effective_subcomponents as $sc)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-white text-slate-700 border border-slate-200 shadow-2xs">
                                                    {{ $sc->component_name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Active
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <button 
                                            @click="openModal = true; activeComponentId = {{ $comp->id }}; activeComponentName = '{{ addslashes($comp->component_name) }}'" 
                                            type="button" 
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-indigo-700 bg-white hover:bg-indigo-50 border border-indigo-200 hover:border-indigo-300 transition-all cursor-pointer shadow-2xs text-xs font-bold"
                                            title="Edit Card Components"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            <span>Edit Card</span>
                                        </button>
                                    </td>
                                </tr>

                                {{-- 2. Nested Subcomponents Rows for Card --}}
                                @foreach($comp->effective_subcomponents as $subIndex => $subComp)
                                    @php
                                        $subNameAndSlug = strtolower($subComp->component_name . ' ' . $subComp->component_slug);
                                        $subExisting = $contentData[$comp->id . '_' . $subComp->id] ?? null;
                                    @endphp
                                    <tr class="bg-slate-50/40 hover:bg-slate-50 transition-colors border-l-4 border-l-indigo-500">
                                        <td class="px-5 py-3 text-[11px] font-semibold text-slate-400 pl-8">
                                            {{ $index + 1 }}.{{ $subIndex + 1 }}
                                        </td>
                                        <td class="px-5 py-3 pl-8">
                                            <div class="flex items-center gap-2">
                                                <span class="text-indigo-400 font-mono text-xs">↳</span>
                                                <div>
                                                    <h5 class="font-bold text-slate-800 text-xs">
                                                        {{ $subComp->component_name }}
                                                    </h5>
                                                    <p class="text-[10px] text-slate-400 font-mono">
                                                        /{{ $subComp->component_slug }}
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-3">
                                            @if(str_contains($subNameAndSlug, 'subheading'))
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                                    SubHeading
                                                </span>
                                            @elseif(str_contains($subNameAndSlug, 'heading'))
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                    Heading
                                                </span>
                                            @elseif(str_contains($subNameAndSlug, 'button'))
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                    Button
                                                </span>
                                            @elseif(str_contains($subNameAndSlug, 'paragraph') || str_contains($subNameAndSlug, 'textarea') || str_contains($subNameAndSlug, 'desc'))
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                    Paragraph
                                                </span>
                                            @elseif(str_contains($subNameAndSlug, 'image') || str_contains($subNameAndSlug, 'photo') || str_contains($subNameAndSlug, 'banner'))
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-teal-50 text-teal-700 border border-teal-200">
                                                    Image
                                                </span>
                                            @elseif(str_contains($subNameAndSlug, 'video'))
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                                    Video
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600">
                                                    Standard
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3">
                                            @if(str_contains($subNameAndSlug, 'heading'))
                                                @if($subExisting?->content_value)
                                                    <p class="text-xs font-bold text-slate-800">{{ $subExisting->content_value }}</p>
                                                @else
                                                    <span class="text-xs text-slate-400 italic">No text entered</span>
                                                @endif
                                            @elseif(str_contains($subNameAndSlug, 'button'))
                                                @if($subExisting?->content_value || $subExisting?->extra_value)
                                                    <div class="flex items-center gap-2">
                                                        <span class="px-2.5 py-0.5 rounded-lg bg-blue-600 text-white font-bold text-xs shadow-2xs">
                                                            {{ $subExisting->content_value ?: 'Button' }}
                                                        </span>
                                                        @if($subExisting->extra_value)
                                                            <span class="text-[11px] font-mono text-slate-400 truncate max-w-[160px]">
                                                                {{ $subExisting->extra_value }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-xs text-slate-400 italic">No button configured</span>
                                                @endif
                                            @elseif(str_contains($subNameAndSlug, 'paragraph') || str_contains($subNameAndSlug, 'textarea') || str_contains($subNameAndSlug, 'desc'))
                                                @if($subExisting?->content_value)
                                                    <div class="text-xs text-slate-700 leading-relaxed whitespace-pre-line break-words max-w-xl">
                                                        {{ $subExisting->content_value }}
                                                    </div>
                                                @else
                                                    <span class="text-xs text-slate-400 italic">No paragraph entered</span>
                                                @endif
                                            @elseif(str_contains($subNameAndSlug, 'image') || str_contains($subNameAndSlug, 'photo') || str_contains($subNameAndSlug, 'banner'))
                                                @if($subExisting?->file_path && file_exists(public_path($subExisting->file_path)))
                                                    <div class="flex items-center gap-2">
                                                        <img src="{{ asset($subExisting->file_path) }}" alt="Preview" class="w-8 h-8 object-cover rounded-lg border border-slate-200 shadow-2xs">
                                                        <span class="text-xs text-slate-700 font-medium truncate max-w-[130px]">{{ basename($subExisting->file_path) }}</span>
                                                    </div>
                                                @else
                                                    <span class="text-xs text-slate-400 italic">No image uploaded</span>
                                                @endif
                                            @elseif(str_contains($subNameAndSlug, 'video'))
                                                @if($subExisting?->file_path && file_exists(public_path($subExisting->file_path)))
                                                    <div class="flex items-center gap-2">
                                                        <video src="{{ asset($subExisting->file_path) }}" class="w-12 h-8 object-cover rounded-lg border border-slate-200 shadow-2xs"></video>
                                                        <span class="text-xs text-slate-700 font-medium truncate max-w-[130px]">{{ basename($subExisting->file_path) }}</span>
                                                    </div>
                                                @else
                                                    <span class="text-xs text-slate-400 italic">No video uploaded</span>
                                                @endif
                                            @else
                                                @if($subExisting?->content_value)
                                                    <span class="text-xs text-slate-800">{{ $subExisting->content_value }}</span>
                                                @else
                                                    <span class="text-xs text-slate-400 italic">No value</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td class="px-5 py-3 text-center">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                                                Active
                                            </span>
                                        </td>
                                        <td class="px-5 py-3 text-right">
                                            <button 
                                                @click="openModal = true; activeComponentId = {{ $comp->id }}; activeComponentName = '{{ addslashes($comp->component_name) }}'" 
                                                type="button" 
                                                class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-blue-600 hover:text-blue-700 bg-white hover:bg-blue-50 border border-slate-200 hover:border-blue-300 transition-all cursor-pointer shadow-2xs"
                                                title="Edit {{ $subComp->component_name }}"
                                            >
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                {{-- 3. Standard Component Row (no subcomponents) --}}
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <!-- Index -->
                                    <td class="px-5 py-4 text-xs font-semibold text-slate-400">
                                        {{ $index + 1 }}
                                    </td>

                                    <!-- Component Name & Slug -->
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs shrink-0">
                                                {{ strtoupper(substr($comp->component_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-slate-900 text-xs">
                                                    {{ $comp->component_name }}
                                                </h4>
                                                <p class="text-[10px] text-slate-400 font-mono">
                                                    /{{ $comp->component_slug }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Component Type Badge -->
                                    <td class="px-5 py-4">
                                        @if(str_contains($nameAndSlug, 'subheading'))
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                                SubHeading
                                            </span>
                                        @elseif(str_contains($nameAndSlug, 'heading'))
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                Heading
                                            </span>
                                        @elseif(str_contains($nameAndSlug, 'button'))
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                Button
                                            </span>
                                        @elseif(str_contains($nameAndSlug, 'paragraph') || str_contains($nameAndSlug, 'textarea') || str_contains($nameAndSlug, 'desc'))
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                Paragraph
                                            </span>
                                        @elseif(str_contains($nameAndSlug, 'image'))
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-teal-50 text-teal-700 border border-teal-200">
                                                Image
                                            </span>
                                        @elseif(str_contains($nameAndSlug, 'video'))
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                                Video
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 text-slate-600">
                                                Standard
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Configured Content / Preview -->
                                    <td class="px-5 py-4">
                                        {{-- Heading & SubHeading --}}
                                        @if(str_contains($nameAndSlug, 'heading'))
                                            @if($existing?->content_value)
                                                <p class="text-xs font-bold text-slate-800">{{ $existing->content_value }}</p>
                                            @else
                                                <span class="text-xs text-slate-400 italic">No text entered</span>
                                            @endif

                                        {{-- Button --}}
                                        @elseif(str_contains($nameAndSlug, 'button'))
                                            @if($existing?->content_value || $existing?->extra_value)
                                                <div class="flex items-center gap-2">
                                                    <span class="px-3 py-1 rounded-lg bg-blue-600 text-white font-bold text-xs shadow-2xs">
                                                        {{ $existing->content_value ?: 'Button' }}
                                                    </span>
                                                    @if($existing->extra_value)
                                                        <span class="text-[11px] font-mono text-slate-400 truncate max-w-[180px]">
                                                            {{ $existing->extra_value }}
                                                        </span>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-xs text-slate-400 italic">No button configured</span>
                                            @endif

                                        {{-- Paragraph --}}
                                        @elseif(str_contains($nameAndSlug, 'paragraph') || str_contains($nameAndSlug, 'textarea') || str_contains($nameAndSlug, 'desc'))
                                            @if($existing?->content_value)
                                                <div class="text-xs text-slate-700 leading-relaxed whitespace-pre-line break-words max-w-2xl">
                                                    {{ $existing->content_value }}
                                                </div>
                                            @else
                                                <span class="text-xs text-slate-400 italic">No paragraph entered</span>
                                            @endif

                                        {{-- Image --}}
                                        @elseif(str_contains($nameAndSlug, 'image'))
                                            @if($existing?->file_path && file_exists(public_path($existing->file_path)))
                                                <div class="flex items-center gap-2.5">
                                                    <img src="{{ asset($existing->file_path) }}" alt="Preview" class="w-10 h-10 object-cover rounded-lg border border-slate-200 shadow-2xs">
                                                    <span class="text-xs text-slate-700 font-medium truncate max-w-[140px]">{{ basename($existing->file_path) }}</span>
                                                </div>
                                            @else
                                                <span class="text-xs text-slate-400 italic">No image uploaded</span>
                                            @endif

                                        {{-- Video --}}
                                        @elseif(str_contains($nameAndSlug, 'video'))
                                            @if($existing?->file_path && file_exists(public_path($existing->file_path)))
                                                <div class="flex items-center gap-2.5">
                                                    <video src="{{ asset($existing->file_path) }}" class="w-14 h-9 object-cover rounded-lg border border-slate-200 shadow-2xs"></video>
                                                    <span class="text-xs text-slate-700 font-medium truncate max-w-[140px]">{{ basename($existing->file_path) }}</span>
                                                </div>
                                            @else
                                                <span class="text-xs text-slate-400 italic">No video uploaded</span>
                                            @endif

                                        {{-- Fallback --}}
                                        @else
                                            @if($existing?->content_value)
                                                <span class="text-xs text-slate-800">{{ $existing->content_value }}</span>
                                            @else
                                                <span class="text-xs text-slate-400 italic">No value</span>
                                            @endif
                                        @endif
                                    </td>

                                    <!-- Status -->
                                    <td class="px-5 py-4 text-center">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Active
                                        </span>
                                    </td>

                                    <!-- Action -->
                                    <td class="px-5 py-4 text-right">
                                        <button 
                                            @click="openModal = true; activeComponentId = {{ $comp->id }}; activeComponentName = '{{ addslashes($comp->component_name) }}'" 
                                            type="button" 
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-blue-600 hover:text-blue-700 bg-white hover:bg-blue-50 border border-slate-200 hover:border-blue-300 transition-all cursor-pointer shadow-2xs"
                                            title="Edit Content"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <!-- Empty state when no components assigned -->
            <div class="py-12 px-6 text-center rounded-xl border border-dashed border-slate-200">
                <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-slate-800">No active components assigned to this section</h3>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">Super Admin has not configured active components for "{{ $section->section_name }}".</p>
            </div>
        @endif
    </div>

    <!-- ======================================================== -->
    <!-- BOOTSTRAP-STYLED CONTENT MODAL (Opens on Add/Edit Content) -->
    <!-- ======================================================== -->
    <div 
        x-show="openModal" 
        class="fixed inset-0 z-50 overflow-y-auto" 
        aria-labelledby="modal-title" 
        role="dialog" 
        aria-modal="true"
        style="display: none;"
    >
        <!-- Modal Backdrop -->
        <div 
            x-show="openModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="openModal = false"
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
        ></div>

        <!-- Modal Dialog Placement -->
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div 
                x-show="openModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                @click.away="openModal = false"
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-slate-200/90"
            >
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 bg-slate-50/70">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900" id="modal-title">
                                <span x-show="activeComponentId">Edit Component: <span class="text-blue-600 font-extrabold" x-text="activeComponentName"></span></span>
                                <span x-show="!activeComponentId">Edit Content: <span class="text-blue-600 font-extrabold">{{ $section->section_name }}</span></span>
                            </h3>
                            <p class="text-xs text-slate-400 mt-0.5">
                                <span x-show="activeComponentId">Editing only this component. Saved directly to the database.</span>
                                <span x-show="!activeComponentId">Fill in the fields below. Data will be saved directly into the database.</span>
                            </p>
                        </div>
                    </div>
                    <button 
                        @click="openModal = false" 
                        type="button" 
                        class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer"
                        aria-label="Close"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body (Form) -->
                @if($section->components->isNotEmpty())
                    <form action="{{ route('admin.section.saveContent', $section->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="active_component_id" :value="activeComponentId">

                        <div class="px-6 py-5 space-y-4 max-h-[70vh] overflow-y-auto">
                            @foreach($section->components as $index => $comp)
                                @php
                                    $nameAndSlug = strtolower($comp->component_name . ' ' . $comp->component_slug);
                                    $existing = $contentData[$comp->id] ?? null;
                                @endphp

                                <div x-show="!activeComponentId || activeComponentId === {{ $comp->id }}" class="space-y-1">
                                    @if($comp->effective_subcomponents->isNotEmpty())
                                        {{-- CARD CONTAINER WITH ITS SUBCOMPONENTS --}}
                                        <div class="p-4 rounded-xl border border-indigo-200 bg-indigo-50/20 space-y-4">
                                            <div class="flex items-center justify-between pb-2 border-b border-indigo-100">
                                                <div class="flex items-center gap-2">
                                                    <span class="w-7 h-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs">
                                                        {{ strtoupper(substr($comp->component_name, 0, 1)) }}
                                                    </span>
                                                    <div>
                                                        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">{{ $comp->component_name }} Subcomponents</h4>
                                                        <p class="text-[11px] text-slate-400">Fill in the fields for this card container</p>
                                                    </div>
                                                </div>
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 text-indigo-700">
                                                    {{ $comp->effective_subcomponents->count() }} Fields
                                                </span>
                                            </div>

                                            <div class="space-y-4">
                                                @foreach($comp->effective_subcomponents as $subComp)
                                                    @php
                                                        $subNameAndSlug = strtolower($subComp->component_name . ' ' . $subComp->component_slug);
                                                        $subExisting = $contentData[$comp->id . '_' . $subComp->id] ?? null;
                                                    @endphp

                                                    {{-- SUBHEADING --}}
                                                    @if(str_contains($subNameAndSlug, 'subheading'))
                                                        <div>
                                                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                                                {{ $subComp->component_name }}
                                                            </label>
                                                            <input type="text" :disabled="activeComponentId && activeComponentId !== {{ $comp->id }}" name="components[{{ $comp->id }}][subcomponents][{{ $subComp->id }}][value]" value="{{ old('components.'.$comp->id.'.subcomponents.'.$subComp->id.'.value', $subExisting?->content_value) }}" placeholder="Enter subheading text..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all font-medium bg-white">
                                                        </div>

                                                    {{-- HEADING --}}
                                                    @elseif(str_contains($subNameAndSlug, 'heading'))
                                                        <div>
                                                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                                                {{ $subComp->component_name }}
                                                            </label>
                                                            <input type="text" :disabled="activeComponentId && activeComponentId !== {{ $comp->id }}" name="components[{{ $comp->id }}][subcomponents][{{ $subComp->id }}][value]" value="{{ old('components.'.$comp->id.'.subcomponents.'.$subComp->id.'.value', $subExisting?->content_value) }}" placeholder="Enter heading title..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all font-bold bg-white">
                                                        </div>

                                                    {{-- BUTTON with Live Interactive Binding --}}
                                                    @elseif(str_contains($subNameAndSlug, 'button'))
                                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-data="{ btnText: '{{ addslashes(old('components.'.$comp->id.'.subcomponents.'.$subComp->id.'.value', $subExisting?->content_value ?? 'Click Here')) }}' }">
                                                            <div>
                                                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                                                    {{ $subComp->component_name }} Label
                                                                </label>
                                                                <input type="text" :disabled="activeComponentId && activeComponentId !== {{ $comp->id }}" x-model="btnText" name="components[{{ $comp->id }}][subcomponents][{{ $subComp->id }}][value]" placeholder="e.g. Get Started / Click Here" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all font-medium bg-white">
                                                            </div>
                                                            <div>
                                                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                                                    {{ $subComp->component_name }} Target Link / URL
                                                                </label>
                                                                <div class="flex items-center gap-2">
                                                                    <input type="text" :disabled="activeComponentId && activeComponentId !== {{ $comp->id }}" name="components[{{ $comp->id }}][subcomponents][{{ $subComp->id }}][extra]" value="{{ old('components.'.$comp->id.'.subcomponents.'.$subComp->id.'.extra', $subExisting?->extra_value) }}" placeholder="e.g. #contact or https://example.com" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all font-medium bg-white">
                                                                    <input type="button" :value="btnText || 'Click Here'" value="{{ $subExisting?->content_value ?: 'Click Here' }}" class="shrink-0 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs cursor-pointer shadow-xs transition-colors">
                                                                </div>
                                                            </div>
                                                        </div>

                                                    {{-- PARAGRAPH --}}
                                                    @elseif(str_contains($subNameAndSlug, 'paragraph') || str_contains($subNameAndSlug, 'textarea') || str_contains($subNameAndSlug, 'desc'))
                                                        <div>
                                                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                                                {{ $subComp->component_name }} <span class="text-slate-400 text-[11px] font-normal font-mono">(&lt;textarea&gt;)</span>
                                                            </label>
                                                            <textarea :disabled="activeComponentId && activeComponentId !== {{ $comp->id }}" name="components[{{ $comp->id }}][subcomponents][{{ $subComp->id }}][value]" rows="3" placeholder="Enter paragraph description or content here..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all leading-relaxed bg-white">{{ old('components.'.$comp->id.'.subcomponents.'.$subComp->id.'.value', $subExisting?->content_value) }}</textarea>
                                                        </div>

                                                    {{-- IMAGE --}}
                                                    @elseif(str_contains($subNameAndSlug, 'image') || str_contains($subNameAndSlug, 'photo') || str_contains($subNameAndSlug, 'banner'))
                                                        <div>
                                                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                                                {{ $subComp->component_name }} <span class="text-slate-400 text-[11px] font-normal font-mono">(Image File)</span>
                                                            </label>
                                                            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                                                                <input type="file" accept="image/*" :disabled="activeComponentId && activeComponentId !== {{ $comp->id }}" name="components[{{ $comp->id }}][subcomponents][{{ $subComp->id }}][file]" class="w-full text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer rounded-xl border border-slate-300 p-1.5 bg-white">
                                                                @if($subExisting?->file_path && file_exists(public_path($subExisting->file_path)))
                                                                    <div class="flex items-center gap-2.5 shrink-0 p-1.5 bg-slate-50 rounded-xl border border-slate-200">
                                                                        <img src="{{ asset($subExisting->file_path) }}" alt="Preview" class="w-8 h-8 object-cover rounded-lg">
                                                                        <span class="text-xs text-slate-600 font-medium max-w-[140px] truncate">{{ basename($subExisting->file_path) }}</span>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>

                                                    {{-- VIDEO --}}
                                                    @elseif(str_contains($subNameAndSlug, 'video'))
                                                        <div>
                                                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                                                {{ $subComp->component_name }} <span class="text-slate-400 text-[11px] font-normal font-mono">(Video File)</span>
                                                            </label>
                                                            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                                                                <input type="file" accept="video/*" :disabled="activeComponentId && activeComponentId !== {{ $comp->id }}" name="components[{{ $comp->id }}][subcomponents][{{ $subComp->id }}][file]" class="w-full text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 cursor-pointer rounded-xl border border-slate-300 p-1.5 bg-white">
                                                                @if($subExisting?->file_path && file_exists(public_path($subExisting->file_path)))
                                                                    <div class="flex items-center gap-2.5 shrink-0 p-1.5 bg-slate-50 rounded-xl border border-slate-200">
                                                                        <video src="{{ asset($subExisting->file_path) }}" class="w-12 h-8 object-cover rounded-lg"></video>
                                                                        <span class="text-xs text-slate-600 font-medium max-w-[140px] truncate">{{ basename($subExisting->file_path) }}</span>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>

                                                    {{-- DEFAULT FALLBACK --}}
                                                    @else
                                                        <div>
                                                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                                                {{ $subComp->component_name }}
                                                            </label>
                                                            <input type="text" :disabled="activeComponentId && activeComponentId !== {{ $comp->id }}" name="components[{{ $comp->id }}][subcomponents][{{ $subComp->id }}][value]" value="{{ old('components.'.$comp->id.'.subcomponents.'.$subComp->id.'.value', $subExisting?->content_value) }}" placeholder="Enter value for {{ $subComp->component_name }}..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all font-medium bg-white">
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        {{-- 1. SUBHEADING --}}
                                        @if(str_contains($nameAndSlug, 'subheading'))
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                                    {{ $comp->component_name }}
                                                </label>
                                                <input type="text" :disabled="activeComponentId && activeComponentId !== {{ $comp->id }}" name="components[{{ $comp->id }}][value]" value="{{ old('components.'.$comp->id.'.value', $existing?->content_value) }}" placeholder="Enter subheading text..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all font-medium">
                                            </div>

                                        {{-- 2. HEADING --}}
                                        @elseif(str_contains($nameAndSlug, 'heading'))
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                                    {{ $comp->component_name }}
                                                </label>
                                                <input type="text" :disabled="activeComponentId && activeComponentId !== {{ $comp->id }}" name="components[{{ $comp->id }}][value]" value="{{ old('components.'.$comp->id.'.value', $existing?->content_value) }}" placeholder="Enter heading title..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all font-bold">
                                            </div>

                                        {{-- 3. BUTTON with Live Interactive Binding --}}
                                        @elseif(str_contains($nameAndSlug, 'button'))
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-data="{ btnText: '{{ addslashes(old('components.'.$comp->id.'.value', $existing?->content_value ?? 'Click Here')) }}' }">
                                                <div>
                                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                                        {{ $comp->component_name }} Label
                                                    </label>
                                                    <input type="text" :disabled="activeComponentId && activeComponentId !== {{ $comp->id }}" x-model="btnText" name="components[{{ $comp->id }}][value]" placeholder="e.g. Get Started / Click Here" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all font-medium">
                                                </div>
                                                <div>
                                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                                        {{ $comp->component_name }} Target Link / URL
                                                    </label>
                                                    <div class="flex items-center gap-2">
                                                        <input type="text" :disabled="activeComponentId && activeComponentId !== {{ $comp->id }}" name="components[{{ $comp->id }}][extra]" value="{{ old('components.'.$comp->id.'.extra', $existing?->extra_value) }}" placeholder="e.g. #contact or https://example.com" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all font-medium">
                                                        <input type="button" :value="btnText || 'Click Here'" value="{{ $existing?->content_value ?: 'Click Here' }}" class="shrink-0 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs cursor-pointer shadow-xs transition-colors">
                                                    </div>
                                                </div>
                                            </div>

                                        {{-- 4. PARAGRAPH --}}
                                        @elseif(str_contains($nameAndSlug, 'paragraph') || str_contains($nameAndSlug, 'textarea') || str_contains($nameAndSlug, 'desc'))
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                                    {{ $comp->component_name }} <span class="text-slate-400 text-[11px] font-normal font-mono">(&lt;textarea&gt;)</span>
                                                </label>
                                                <textarea :disabled="activeComponentId && activeComponentId !== {{ $comp->id }}" name="components[{{ $comp->id }}][value]" rows="3" placeholder="Enter paragraph description or content here..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all leading-relaxed">{{ old('components.'.$comp->id.'.value', $existing?->content_value) }}</textarea>
                                            </div>

                                        {{-- 5. IMAGE --}}
                                        @elseif(str_contains($nameAndSlug, 'image') || str_contains($nameAndSlug, 'photo') || str_contains($nameAndSlug, 'banner'))
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                                    {{ $comp->component_name }}
                                                </label>
                                                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                                                    <input type="file" accept="image/*" :disabled="activeComponentId && activeComponentId !== {{ $comp->id }}" name="components[{{ $comp->id }}][file]" class="w-full text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer rounded-xl border border-slate-300 p-1.5 bg-white">
                                                    @if($existing?->file_path && file_exists(public_path($existing->file_path)))
                                                        <div class="flex items-center gap-2.5 shrink-0 p-1.5 bg-slate-50 rounded-xl border border-slate-200">
                                                            <img src="{{ asset($existing->file_path) }}" alt="Preview" class="w-8 h-8 object-cover rounded-lg">
                                                            <span class="text-xs text-slate-600 font-medium max-w-[140px] truncate">{{ basename($existing->file_path) }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                        {{-- 6. VIDEO --}}
                                        @elseif(str_contains($nameAndSlug, 'video'))
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                                    {{ $comp->component_name }}
                                                </label>
                                                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                                                    <input type="file" accept="video/*" :disabled="activeComponentId && activeComponentId !== {{ $comp->id }}" name="components[{{ $comp->id }}][file]" class="w-full text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 cursor-pointer rounded-xl border border-slate-300 p-1.5 bg-white">
                                                    @if($existing?->file_path && file_exists(public_path($existing->file_path)))
                                                        <div class="flex items-center gap-2.5 shrink-0 p-1.5 bg-slate-50 rounded-xl border border-slate-200">
                                                            <video src="{{ asset($existing->file_path) }}" class="w-12 h-8 object-cover rounded-lg"></video>
                                                            <span class="text-xs text-slate-600 font-medium max-w-[140px] truncate">{{ basename($existing->file_path) }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                        {{-- 7. DEFAULT FALLBACK --}}
                                        @else
                                            <div>
                                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                                    {{ $comp->component_name }}
                                                </label>
                                                <input type="text" :disabled="activeComponentId && activeComponentId !== {{ $comp->id }}" name="components[{{ $comp->id }}][value]" value="{{ old('components.'.$comp->id.'.value', $existing?->content_value) }}" placeholder="Enter value for {{ $comp->component_name }}..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all font-medium">
                                            </div>
                                        @endif
                                    @endif
                                </div>

                            @endforeach
                        </div>

                        <!-- Modal Actions Footer -->
                        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3 rounded-b-2xl">
                            <button 
                                @click="openModal = false" 
                                type="button" 
                                class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-600 text-xs font-bold hover:bg-slate-100 transition-colors"
                            >
                                Cancel
                            </button>
                            <button 
                                type="submit" 
                                class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-all shadow-sm shadow-blue-500/20 cursor-pointer flex items-center gap-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span x-text="activeComponentId ? 'Save ' + activeComponentName : 'Save Section Content'">Save Section Content</span>
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
