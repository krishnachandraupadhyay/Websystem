@extends('superadmin.layout')

@section('title', $section->section_name . ' - Section Content')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{ openModal: false, activeComponentId: null, activeComponentName: '', activeSubCompId: null, activeInstanceIndex: null, activeInstanceLabel: '' }">

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
                @click="openModal = true; activeComponentId = null; activeComponentName = ''; activeSubCompId = null; activeInstanceIndex = null; activeInstanceLabel = ''" 
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
                            <th class="px-5 py-3.5 w-16">#</th>
                            <th class="px-5 py-3.5 min-w-[180px]">Component</th>
                            <th class="px-5 py-3.5 w-32">Type</th>
                            <th class="px-5 py-3.5 min-w-[240px]">Configured Content / Preview</th>
                            <th class="px-4 py-3.5 text-center w-28">Order</th>
                            <th class="px-5 py-3.5 text-center w-28">Status</th>
                            <th class="px-5 py-3.5 text-right w-24">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($section->components as $index => $comp)
                            @php
                                $nameAndSlug = strtolower($comp->component_name . ' ' . $comp->component_slug);
                                $isCompMultiple = (bool)($comp->pivot->is_multiple ?? $comp->is_multiple ?? false);
                                $compInstMap = $multiFieldData[$comp->id] ?? [];
                                $compInstCount = count($compInstMap);
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
                                            Card
                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold text-slate-700 bg-slate-100 border border-slate-200">
                                            <span class="text-slate-500 font-medium">Count:</span>
                                            <span class="font-bold text-indigo-600">{{ $comp->effective_subcomponents->count() }}</span>
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-center text-slate-300 font-semibold text-xs">
                                        -
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Active
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <button 
                                            @click="openModal = true; activeComponentId = {{ $comp->id }}; activeComponentName = '{{ addslashes($comp->component_name) }}'; activeSubCompId = null; activeInstanceIndex = null; activeInstanceLabel = ''" 
                                            type="button" 
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-blue-600 hover:text-blue-700 bg-white hover:bg-blue-50 border border-slate-200 hover:border-blue-300 transition-all cursor-pointer shadow-2xs"
                                            title="Edit {{ $comp->component_name }}"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>

                                {{-- 2. Nested Subcomponents Rows for Card --}}
                                @foreach($comp->effective_subcomponents as $subIndex => $subComp)
                                    @php
                                        $subNameAndSlug = strtolower($subComp->component_name . ' ' . $subComp->component_slug);
                                        $isSubMultiple = (bool)($subComp->pivot->is_multiple ?? $subComp->is_multiple);
                                        $subExisting = $contentData[$comp->id . '_' . $subComp->id] ?? null;
                                        $subFields = $subComp->fields->where('is_active', true)->sortBy('sort_order');
                                        $subInstMap = $multiFieldData[$comp->id . '_' . $subComp->id] ?? [];
                                        $subInstCount = count($subInstMap);
                                    @endphp

                                    @if($isSubMultiple && $subInstCount > 0)
                                        {{-- 2.1 Individual Repeater Rows (e.g. 4.1 Home, 4.2 About Us, 4.3 Message...) --}}
                                        @foreach($subInstMap as $sPos => $instFields)
                                            @php
                                                $instLabel = null;
                                                $instUrl = null;
                                                $otherFields = [];

                                                foreach($subFields as $sf) {
                                                    $fVal = is_array($instFields) ? ($instFields[$sf->id] ?? null) : null;
                                                    $valStr = $fVal?->content_value ?? '';
                                                    $fNameLower = strtolower($sf->field_name);
                                                    $fLabelLower = strtolower($sf->field_label);

                                                    if (!$instLabel && in_array($fNameLower, ['anchor_text', 'text', 'label', 'title', 'heading', 'name', 'button_text'], true) && !empty($valStr)) {
                                                        $instLabel = $valStr;
                                                    }
                                                    if (!$instUrl && (in_array($fNameLower, ['anchor_url', 'url', 'link', 'href'], true) || str_contains($fNameLower, 'url') || str_contains($fNameLower, 'link')) && !empty($valStr)) {
                                                        $instUrl = $valStr;
                                                    }

                                                    // Exclude metadata/technical fields (target, rel, title/tooltip, url duplicates) from table preview
                                                    $isMetaOrDuplicate = in_array($fNameLower, ['anchor_text', 'anchor_url', 'target', 'title', 'rel', 'window', 'tooltip'], true)
                                                        || str_contains($fNameLower, 'target')
                                                        || str_contains($fNameLower, 'rel')
                                                        || str_contains($fNameLower, 'tooltip')
                                                        || str_contains($fNameLower, 'window')
                                                        || str_contains($fNameLower, 'url')
                                                        || str_contains($fNameLower, 'href')
                                                        || str_contains($fNameLower, 'text')
                                                        || str_contains($fLabelLower, 'target')
                                                        || str_contains($fLabelLower, 'relationship')
                                                        || str_contains($fLabelLower, 'tooltip')
                                                        || str_contains($fLabelLower, 'anchor url')
                                                        || str_contains($fLabelLower, 'anchor text');

                                                    if (!$isMetaOrDuplicate && (!empty($valStr) || $fVal?->file_path)) {
                                                        $otherFields[] = [
                                                            'label' => $sf->field_label,
                                                            'val' => $valStr,
                                                            'file' => $fVal?->file_path
                                                        ];
                                                    }
                                                }

                                                if (!$instLabel) {
                                                    $firstVal = is_array($instFields) ? (reset($instFields)?->content_value ?? null) : null;
                                                    $instLabel = $firstVal ?: ($subComp->component_name . ' #' . ($sPos + 1));
                                                }
                                            @endphp
                                            <tr 
                                                class="repeater-table-row bg-slate-50/40 hover:bg-indigo-50/40 transition-colors border-l-4 border-l-indigo-500"
                                                data-group-key="comp-{{ $comp->id }}-sub-{{ $subComp->id }}"
                                                data-parent-prefix="{{ $index + 1 }}."
                                            >
                                                {{-- 1. Sequence number: 4.1, 4.2, 4.3... --}}
                                                <td class="px-5 py-3.5 text-[11px] font-bold text-indigo-700 pl-8 table-seq-num">
                                                    {{ $index + 1 }}.{{ $sPos + 1 }}
                                                </td>

                                                {{-- 2. Component Name & Title --}}
                                                <td class="px-5 py-3.5 pl-8">
                                                    <div class="flex items-center gap-2.5">
                                                        <span class="text-indigo-400 font-mono text-xs font-bold">↳</span>
                                                        <div>
                                                            <h5 class="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                                                                <span class="item-title text-slate-900">{{ $instLabel }}</span>
                                                            </h5>
                                                            <p class="text-[10px] text-slate-400 font-mono">
                                                                {{ $subComp->component_name }} • /{{ $subComp->component_slug }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- 3. Type --}}
                                                <td class="px-5 py-3.5">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                        {{ $subComp->component_name }} Item
                                                    </span>
                                                </td>

                                                {{-- 4. Preview / Configured Content --}}
                                                <td class="px-5 py-3.5">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        @if($instUrl)
                                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-200 text-xs font-mono font-medium">
                                                                <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                                                <span>{{ $instUrl }}</span>
                                                            </span>
                                                        @endif
                                                        @foreach($otherFields as $of)
                                                            @if($of['file'])
                                                                <img src="{{ asset($of['file']) }}" class="w-7 h-7 object-cover rounded border border-slate-200 shadow-2xs" title="{{ $of['label'] }}">
                                                            @elseif(!empty($of['val']))
                                                                <span class="text-[11px] text-slate-600 bg-slate-100 px-2 py-0.5 rounded border border-slate-200 font-medium">
                                                                    <span class="text-slate-400 font-semibold">{{ $of['label'] }}:</span> {{ $of['val'] }}
                                                                </span>
                                                            @endif
                                                        @endforeach
                                                        @if(!$instUrl && empty($otherFields))
                                                            <span class="text-xs text-slate-400 italic">-</span>
                                                        @endif
                                                    </div>
                                                </td>

                                                {{-- 5. In-Table Order Controls --}}
                                                <td class="px-4 py-3.5 text-center">
                                                    <div class="inline-flex items-center justify-center gap-1.5 bg-slate-100/90 p-1 rounded-xl border border-slate-200/90 shadow-2xs">
                                                        <input 
                                                            type="number" 
                                                            min="1" 
                                                            max="{{ $subInstCount }}" 
                                                            value="{{ $sPos + 1 }}" 
                                                            data-comp-id="{{ $comp->id }}" 
                                                            data-subcomp-id="{{ $subComp->id }}" 
                                                            data-inst-idx="{{ $sPos }}" 
                                                            data-pos="{{ $sPos }}" 
                                                            class="table-order-input w-10 h-6 text-center text-xs font-bold text-slate-800 bg-white border border-slate-300 rounded-lg focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                                                            onchange="handleTableOrderChange(this)"
                                                            title="Change order position"
                                                        >
                                                        <div class="flex flex-col gap-0.5">
                                                            <button 
                                                                type="button" 
                                                                onclick="moveTableRow(this, 'up')" 
                                                                {{ $sPos === 0 ? 'disabled' : '' }} 
                                                                class="table-btn-up w-5 h-3 rounded flex items-center justify-center text-slate-500 hover:text-indigo-600 hover:bg-white disabled:opacity-20 disabled:pointer-events-none cursor-pointer transition-colors"
                                                                title="Move Up"
                                                            >
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>
                                                            </button>
                                                            <button 
                                                                type="button" 
                                                                onclick="moveTableRow(this, 'down')" 
                                                                {{ $sPos === $subInstCount - 1 ? 'disabled' : '' }} 
                                                                class="table-btn-down w-5 h-3 rounded flex items-center justify-center text-slate-500 hover:text-indigo-600 hover:bg-white disabled:opacity-20 disabled:pointer-events-none cursor-pointer transition-colors"
                                                                title="Move Down"
                                                            >
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </td>

                                                {{-- 6. Status --}}
                                                <td class="px-5 py-3.5 text-center">
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                        <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                                                        Active
                                                    </span>
                                                </td>

                                                {{-- 7. Action: Focus modal on this specific instance --}}
                                                <td class="px-5 py-3.5 text-right">
                                                    <button 
                                                        @click="openModal = true; activeComponentId = {{ $comp->id }}; activeComponentName = '{{ addslashes($comp->component_name) }}'; activeSubCompId = {{ $subComp->id }}; activeInstanceIndex = {{ $sPos }}; activeInstanceLabel = '{{ addslashes($instLabel) }}'" 
                                                        type="button" 
                                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-blue-600 hover:text-blue-700 bg-white hover:bg-blue-50 border border-slate-200 hover:border-blue-300 transition-all cursor-pointer shadow-2xs"
                                                        title="Edit {{ $instLabel }}"
                                                    >
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @elseif($isSubMultiple)
                                        {{-- 2.2 Empty Repeater Subcomponent Row --}}
                                        <tr class="bg-slate-50/40 hover:bg-slate-50 transition-colors border-l-4 border-l-indigo-500">
                                            <td class="px-5 py-3 text-[11px] font-semibold text-slate-400 pl-8">
                                                {{ $index + 1 }}.{{ $subIndex + 1 }}
                                            </td>
                                            <td class="px-5 py-3 pl-8">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-indigo-400 font-mono text-xs">↳</span>
                                                    <div>
                                                        <h5 class="font-bold text-slate-800 text-xs">{{ $subComp->component_name }}</h5>
                                                        <p class="text-[10px] text-slate-400 font-mono">/{{ $subComp->component_slug }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-5 py-3">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                                    Repeater (0)
                                                </span>
                                            </td>
                                            <td class="px-5 py-3">
                                                <span class="text-xs text-slate-400 italic">No items added yet</span>
                                            </td>
                                            <td class="px-4 py-3 text-center text-slate-300 font-semibold text-xs">-</td>
                                            <td class="px-5 py-3 text-center">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                                                    Active
                                                </span>
                                            </td>
                                            <td class="px-5 py-3 text-right">
                                                <button 
                                                    @click="openModal = true; activeComponentId = {{ $comp->id }}; activeComponentName = '{{ addslashes($comp->component_name) }}'; activeSubCompId = {{ $subComp->id }}; activeInstanceIndex = null; activeInstanceLabel = ''" 
                                                    type="button" 
                                                    class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-blue-600 hover:text-blue-700 bg-white hover:bg-blue-50 border border-slate-200 hover:border-blue-300 transition-all cursor-pointer shadow-2xs"
                                                    title="Add {{ $subComp->component_name }}"
                                                >
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                </button>
                                            </td>
                                        </tr>
                                    @else
                                        {{-- 2.3 Single / Non-repeater Subcomponent Row --}}
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
                                                @if($subFields->isNotEmpty())
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                        {{ $subFields->count() }} {{ \Illuminate\Support\Str::plural('Field', $subFields->count()) }}
                                                    </span>
                                                @elseif(str_contains($subNameAndSlug, 'subheading'))
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
                                                @if($subFields->isNotEmpty())
                                                    <div class="space-y-1">
                                                        @foreach($subFields as $f)
                                                            @php
                                                                $fItem = $fieldData[$comp->id . '_' . $subComp->id][$f->id] ?? null;
                                                            @endphp
                                                            <div class="flex items-center gap-1.5 text-xs">
                                                                <span class="text-slate-400 font-semibold text-[11px]">{{ $f->field_label }}:</span>
                                                                @if(in_array($f->field_type, ['image', 'video', 'file'], true))
                                                                    @if($fItem?->file_path && file_exists(public_path($fItem->file_path)))
                                                                        <a href="{{ asset($fItem->file_path) }}" target="_blank" class="text-blue-600 hover:underline font-mono text-[11px] truncate max-w-[150px]">
                                                                            {{ basename($fItem->file_path) }}
                                                                        </a>
                                                                    @else
                                                                        <span class="text-slate-400 italic text-[11px]">No file</span>
                                                                    @endif
                                                                @else
                                                                    @if($fItem?->content_value !== null && $fItem?->content_value !== '')
                                                                        <span class="text-slate-800 font-medium">{{ $fItem->content_value }}</span>
                                                                    @else
                                                                        <span class="text-slate-400 italic text-[11px]">Empty</span>
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @elseif(str_contains($subNameAndSlug, 'heading'))
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
                                            <td class="px-4 py-3 text-center text-slate-300 font-semibold text-xs">
                                                -
                                            </td>
                                            <td class="px-5 py-3 text-center">
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                                                    Active
                                                </span>
                                            </td>
                                            <td class="px-5 py-3 text-right">
                                                <button 
                                                    @click="openModal = true; activeComponentId = {{ $comp->id }}; activeComponentName = '{{ addslashes($comp->component_name) }}'; activeSubCompId = {{ $subComp->id }}; activeInstanceIndex = null; activeInstanceLabel = ''" 
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
                                    @endif
                                @endforeach
                            @else
                                @php
                                    $nameAndSlug = strtolower($comp->component_name . ' ' . $comp->component_slug);
                                    $isCompMultiple = (bool)($comp->pivot->is_multiple ?? $comp->is_multiple);
                                    $compFields = $comp->fields->where('is_active', true)->sortBy('sort_order');
                                    $compInstMap = $multiFieldData[$comp->id] ?? [];
                                    $compInstCount = count($compInstMap);
                                    $existing = $contentData[$comp->id] ?? null;
                                @endphp

                                @if($isCompMultiple && $compInstCount > 0)
                                    {{-- 3.1 Top-Level Repeater Individual Rows --}}
                                    @foreach($compInstMap as $cPos => $instFields)
                                        @php
                                            $cLabel = null;
                                            $cUrl = null;
                                            $cOtherFields = [];

                                            foreach($compFields as $cf) {
                                                $fVal = is_array($instFields) ? ($instFields[$cf->id] ?? null) : null;
                                                $valStr = $fVal?->content_value ?? '';
                                                $fNameLower = strtolower($cf->field_name);
                                                $fLabelLower = strtolower($cf->field_label);

                                                if (!$cLabel && in_array($fNameLower, ['title', 'heading', 'name', 'text', 'label', 'button_text', 'anchor_text'], true) && !empty($valStr)) {
                                                    $cLabel = $valStr;
                                                }
                                                if (!$cUrl && (in_array($fNameLower, ['url', 'link', 'href', 'anchor_url'], true) || str_contains($fNameLower, 'url') || str_contains($fNameLower, 'link')) && !empty($valStr)) {
                                                    $cUrl = $valStr;
                                                }

                                                $isMetaOrDuplicate = in_array($fNameLower, ['target', 'rel', 'tooltip', 'window', 'title'], true)
                                                    || str_contains($fNameLower, 'target')
                                                    || str_contains($fNameLower, 'rel')
                                                    || str_contains($fNameLower, 'tooltip')
                                                    || str_contains($fNameLower, 'window')
                                                    || str_contains($fNameLower, 'url')
                                                    || str_contains($fNameLower, 'href')
                                                    || str_contains($fLabelLower, 'target')
                                                    || str_contains($fLabelLower, 'relationship')
                                                    || str_contains($fLabelLower, 'tooltip')
                                                    || str_contains($fLabelLower, 'url');

                                                if (!$isMetaOrDuplicate && (!empty($valStr) || $fVal?->file_path)) {
                                                    $cOtherFields[] = [
                                                        'label' => $cf->field_label,
                                                        'val' => $valStr,
                                                        'file' => $fVal?->file_path
                                                    ];
                                                }
                                            }

                                            if (!$cLabel) {
                                                $firstVal = is_array($instFields) ? (reset($instFields)?->content_value ?? null) : null;
                                                $cLabel = $firstVal ?: ($comp->component_name . ' #' . ($cPos + 1));
                                            }
                                        @endphp
                                        <tr 
                                            class="repeater-table-row hover:bg-blue-50/40 transition-colors"
                                            data-group-key="comp-{{ $comp->id }}"
                                            data-parent-prefix="{{ $index + 1 }}."
                                        >
                                            <td class="px-5 py-4 text-xs font-bold text-blue-700 table-seq-num">
                                                {{ $index + 1 }}.{{ $cPos + 1 }}
                                            </td>
                                            <td class="px-5 py-4">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs shrink-0">
                                                        {{ strtoupper(substr($comp->component_name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <h4 class="font-bold text-slate-900 text-xs item-title">
                                                            {{ $cLabel }}
                                                        </h4>
                                                        <p class="text-[10px] text-slate-400 font-mono">
                                                            {{ $comp->component_name }} • /{{ $comp->component_slug }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-5 py-4">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                    {{ $comp->component_name }} Item
                                                </span>
                                            </td>
                                            <td class="px-5 py-4">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    @if($cUrl)
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-200 text-xs font-mono font-medium">
                                                            <svg class="w-3 h-3 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                                            <span>{{ $cUrl }}</span>
                                                        </span>
                                                    @endif
                                                    @foreach($cOtherFields as $of)
                                                        @if($of['file'])
                                                            <img src="{{ asset($of['file']) }}" class="w-8 h-8 object-cover rounded-lg border border-slate-200 shadow-2xs" title="{{ $of['label'] }}">
                                                        @elseif(!empty($of['val']))
                                                            <span class="text-[11px] text-slate-600 bg-slate-100 px-2 py-0.5 rounded border border-slate-200 font-medium">
                                                                <span class="text-slate-400 font-semibold">{{ $of['label'] }}:</span> {{ $of['val'] }}
                                                            </span>
                                                        @endif
                                                    @endforeach
                                                    @if(!$cUrl && empty($cOtherFields))
                                                        <span class="text-xs text-slate-400 italic">-</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-4 py-4 text-center">
                                                <div class="inline-flex items-center justify-center gap-1.5 bg-slate-100/90 p-1 rounded-xl border border-slate-200/90 shadow-2xs">
                                                    <input 
                                                        type="number" 
                                                        min="1" 
                                                        max="{{ $compInstCount }}" 
                                                        value="{{ $cPos + 1 }}" 
                                                        data-comp-id="{{ $comp->id }}" 
                                                        data-subcomp-id="" 
                                                        data-inst-idx="{{ $cPos }}" 
                                                        data-pos="{{ $cPos }}" 
                                                        class="table-order-input w-10 h-6 text-center text-xs font-bold text-slate-800 bg-white border border-slate-300 rounded-lg focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none"
                                                        onchange="handleTableOrderChange(this)"
                                                        title="Change order position"
                                                    >
                                                    <div class="flex flex-col gap-0.5">
                                                        <button 
                                                            type="button" 
                                                            onclick="moveTableRow(this, 'up')" 
                                                            {{ $cPos === 0 ? 'disabled' : '' }} 
                                                            class="table-btn-up w-5 h-3 rounded flex items-center justify-center text-slate-500 hover:text-blue-600 hover:bg-white disabled:opacity-20 disabled:pointer-events-none cursor-pointer transition-colors"
                                                            title="Move Up"
                                                        >
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>
                                                        </button>
                                                        <button 
                                                            type="button" 
                                                            onclick="moveTableRow(this, 'down')" 
                                                            {{ $cPos === $compInstCount - 1 ? 'disabled' : '' }} 
                                                            class="table-btn-down w-5 h-3 rounded flex items-center justify-center text-slate-500 hover:text-blue-600 hover:bg-white disabled:opacity-20 disabled:pointer-events-none cursor-pointer transition-colors"
                                                            title="Move Down"
                                                        >
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                                                        </button>
                                                    </div>
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
                                                    @click="openModal = true; activeComponentId = {{ $comp->id }}; activeComponentName = '{{ addslashes($comp->component_name) }}'; activeSubCompId = null; activeInstanceIndex = {{ $cPos }}; activeInstanceLabel = '{{ addslashes($cLabel) }}'" 
                                                    type="button" 
                                                    class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-blue-600 hover:text-blue-700 bg-white hover:bg-blue-50 border border-slate-200 hover:border-blue-300 transition-all cursor-pointer shadow-2xs"
                                                    title="Edit {{ $cLabel }}"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    {{-- 3.2 Standard Component Row (no subcomponents or single) --}}
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
                                            @if($compFields->isNotEmpty())
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                                    {{ $compFields->count() }} {{ \Illuminate\Support\Str::plural('Field', $compFields->count()) }}
                                                </span>
                                            @elseif(str_contains($nameAndSlug, 'subheading'))
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
                                            @if($compFields->isNotEmpty())
                                                <div class="space-y-1">
                                                    @foreach($compFields as $f)
                                                        @php
                                                            $fItem = $fieldData[$comp->id][$f->id] ?? null;
                                                        @endphp
                                                        <div class="flex items-center gap-1.5 text-xs">
                                                            <span class="text-slate-400 font-semibold text-[11px]">{{ $f->field_label }}:</span>
                                                            @if(in_array($f->field_type, ['image', 'video', 'file'], true))
                                                                @if($fItem?->file_path && file_exists(public_path($fItem->file_path)))
                                                                    <a href="{{ asset($fItem->file_path) }}" target="_blank" class="text-blue-600 hover:underline font-mono text-[11px] truncate max-w-[150px]">
                                                                        {{ basename($fItem->file_path) }}
                                                                    </a>
                                                                @else
                                                                    <span class="text-slate-400 italic text-[11px]">No file</span>
                                                                @endif
                                                            @else
                                                                @if($fItem?->content_value !== null && $fItem?->content_value !== '')
                                                                    <span class="text-slate-800 font-medium">{{ $fItem->content_value }}</span>
                                                                @else
                                                                    <span class="text-slate-400 italic text-[11px]">Empty</span>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @elseif(str_contains($nameAndSlug, 'heading'))
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

                                        <!-- Order -->
                                        <td class="px-4 py-4 text-center text-slate-300 font-semibold text-xs">
                                            -
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
                                                @click="openModal = true; activeComponentId = {{ $comp->id }}; activeComponentName = '{{ addslashes($comp->component_name) }}'; activeSubCompId = null; activeInstanceIndex = null; activeInstanceLabel = ''" 
                                                type="button" 
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-blue-600 hover:text-blue-700 bg-white hover:bg-blue-50 border border-slate-200 hover:border-blue-300 transition-all cursor-pointer shadow-2xs"
                                                title="Edit {{ $comp->component_name }}"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endif
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
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-3xl border border-slate-200/90"
            >
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4.5 bg-gradient-to-r from-slate-50 via-white to-blue-50/30">
                    <div class="flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 text-white flex items-center justify-center font-bold shadow-md shadow-blue-500/20 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base font-extrabold text-slate-900 tracking-tight" id="modal-title">
                                    <span x-show="activeInstanceLabel">Edit <span class="text-blue-600 font-extrabold" x-text="activeComponentName"></span>: <span class="text-indigo-600 font-extrabold" x-text="activeInstanceLabel"></span></span>
                                    <span x-show="!activeInstanceLabel && activeComponentId">Edit Component: <span class="text-blue-600 font-extrabold" x-text="activeComponentName"></span></span>
                                    <span x-show="!activeInstanceLabel && !activeComponentId">Edit Content: <span class="text-blue-600 font-extrabold">{{ $section->section_name }}</span></span>
                                </h3>
                                <span class="hidden sm:inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200/70">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Live Editor
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">
                                <span x-show="activeInstanceLabel">Editing only <span class="font-bold text-slate-700" x-text="activeInstanceLabel"></span>. Other items will be preserved safely.</span>
                                <span x-show="!activeInstanceLabel && activeComponentId">Editing only this component. Changes sync immediately.</span>
                                <span x-show="!activeInstanceLabel && !activeComponentId">Fill in the fields below. Data will be saved directly into the database.</span>
                            </p>
                        </div>
                    </div>
                    <button 
                        @click="openModal = false; activeComponentId = null; activeComponentName = ''; activeSubCompId = null; activeInstanceIndex = null; activeInstanceLabel = ''" 
                        type="button" 
                        class="text-slate-400 hover:text-slate-700 p-2 rounded-xl hover:bg-slate-100 transition-colors cursor-pointer"
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

                        <div class="px-6 py-6 max-h-[72vh] overflow-y-auto bg-white">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                                @foreach($section->components as $index => $comp)
                                    @php
                                        $nameAndSlug = strtolower($comp->component_name . ' ' . $comp->component_slug);
                                        $isCompMultiple = (bool)($comp->pivot->is_multiple ?? $comp->is_multiple ?? false);
                                        $existing = $contentData[$comp->id] ?? null;
                                        $cSlug = strtolower($comp->component_slug ?? $comp->component_name);
                                        $hasSubcomps = $comp->effective_subcomponents->isNotEmpty();
                                        
                                        // Heading and Subheading are 1 column each so they sit in the same row
                                        $isHalfCol = ($cSlug === 'heading' || $cSlug === 'subheading' || str_contains($cSlug, 'heading'));
                                        $colSpanClass = ($isHalfCol && !$hasSubcomps && !$isCompMultiple) ? 'md:col-span-1' : 'md:col-span-2';
                                    @endphp

                                    <div 
                                        x-show="!activeComponentId || activeComponentId === {{ $comp->id }}" 
                                        :class="activeComponentId === {{ $comp->id }} ? 'md:col-span-2' : '{{ $colSpanClass }}'"
                                        class="space-y-1.5"
                                    >
                                    @if($comp->effective_subcomponents->isNotEmpty())
                                        {{-- CARD CONTAINER WITH ITS SUBCOMPONENTS --}}
                                        <div class="p-4 rounded-2xl border border-indigo-200/90 bg-white shadow-2xs space-y-4">
                                            <div class="flex items-center justify-between pb-3 border-b border-indigo-100/70">
                                                <div class="flex items-center gap-2.5">
                                                    <span class="w-8 h-8 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-700 text-white flex items-center justify-center font-bold text-xs shadow-xs">
                                                        {{ strtoupper(substr($comp->component_name, 0, 1)) }}
                                                    </span>
                                                    <div>
                                                        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">{{ $comp->component_name }} Container</h4>
                                                    </div>
                                                </div>
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/70">
                                                    {{ $comp->effective_subcomponents->count() }} Subcomponents
                                                </span>
                                            </div>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                @foreach($comp->effective_subcomponents as $subComp)
                                                    @php
                                                        $subNameAndSlug = strtolower($subComp->component_name . ' ' . $subComp->component_slug);
                                                        $subExisting = $contentData[$comp->id . '_' . $subComp->id] ?? null;
                                                        $subFields = $subComp->fields->where('is_active', true)->sortBy('sort_order');
                                                        $subSlug = strtolower($subComp->component_slug ?? $subComp->component_name);
                                                        $isSubHalfCol = ($subSlug === 'heading' || $subSlug === 'subheading' || str_contains($subSlug, 'heading'));
                                                    @endphp

                                                    @php
                                                        $isSubMultiple = (bool)($subComp->pivot->is_multiple ?? $subComp->is_multiple);
                                                        $subColSpanClass = ($isSubHalfCol && !$isSubMultiple) ? 'md:col-span-1' : 'md:col-span-2';
                                                        $subInstMap = $multiFieldData[$comp->id . '_' . $subComp->id] ?? [];
                                                        $subInstKeys = array_keys($subInstMap);
                                                        if (empty($subInstKeys)) {
                                                            $subInstKeys = [0];
                                                        }
                                                    @endphp

                                                    <div class="{{ $subColSpanClass }}">

                                                    @if($isSubMultiple && $subFields->isNotEmpty())
                                                        {{-- Repeater Subcomponent (e.g. Nav Anchors) --}}
                                                        <div class="p-3.5 bg-white rounded-xl border border-indigo-100 shadow-2xs space-y-3">
                                                            <div class="flex items-center justify-between pb-1.5 border-b border-slate-100">
                                                                <div class="flex items-center gap-2">
                                                                    <h5 class="text-xs font-bold text-slate-800 uppercase tracking-wider">{{ $subComp->component_name }}</h5>
                                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">Repeater</span>
                                                                </div>
                                                                <span class="text-[10px] font-mono text-slate-400">/{{ $subComp->component_slug }}</span>
                                                            </div>

                                                            <div id="repeater-subcomp-{{ $comp->id }}-{{ $subComp->id }}" data-next-index="{{ count($subInstKeys) }}" class="space-y-3">
                                                                @foreach($subInstKeys as $sPos => $instIdx)
                                                                    <div 
                                                                        class="repeater-sub-item p-3 bg-slate-50/70 rounded-xl border border-slate-200 relative space-y-2.5 shadow-2xs"
                                                                        data-pos="{{ $sPos }}"
                                                                        x-show="activeInstanceIndex === null || activeInstanceIndex === {{ $sPos }}"
                                                                    >
                                                                        <div class="flex items-center justify-between pb-1.5 border-b border-slate-200/70 repeater-item-header">
                                                                            <div class="flex items-center gap-2">
                                                                                <span class="inline-flex items-center justify-center px-2 py-0.5 rounded text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 repeater-index-badge">
                                                                                    #{{ $sPos + 1 }}
                                                                                </span>
                                                                                <span class="text-xs font-bold text-slate-700">{{ $subComp->component_name }}</span>
                                                                            </div>
                                                                            
                                                                            <div class="flex items-center gap-1.5" x-show="activeInstanceIndex === null">
                                                                                <div class="flex items-center gap-1 bg-white px-2 py-0.5 rounded-lg border border-slate-200 shadow-2xs">
                                                                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Order</span>
                                                                                    <input 
                                                                                        type="number" 
                                                                                        min="1" 
                                                                                        max="{{ count($subInstKeys) }}"
                                                                                        value="{{ $sPos + 1 }}" 
                                                                                        title="Set order position"
                                                                                        onchange="setRepeaterItemOrder(this, 'subcomp')"
                                                                                        class="w-12 h-6 text-center text-xs font-bold text-slate-800 bg-slate-50 border border-slate-300 rounded focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none repeater-order-input"
                                                                                    >
                                                                                    <button 
                                                                                        type="button" 
                                                                                        onclick="moveRepeaterItem(this, 'up', 'subcomp')"
                                                                                        title="Move Up" 
                                                                                        {{ $sPos === 0 ? 'disabled' : '' }}
                                                                                        class="w-6 h-6 rounded flex items-center justify-center text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 transition-colors disabled:opacity-25 disabled:pointer-events-none cursor-pointer btn-move-up"
                                                                                    >
                                                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>
                                                                                    </button>
                                                                                    <button 
                                                                                        type="button" 
                                                                                        onclick="moveRepeaterItem(this, 'down', 'subcomp')"
                                                                                        title="Move Down" 
                                                                                        {{ $sPos === count($subInstKeys) - 1 ? 'disabled' : '' }}
                                                                                        class="w-6 h-6 rounded flex items-center justify-center text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 transition-colors disabled:opacity-25 disabled:pointer-events-none cursor-pointer btn-move-down"
                                                                                    >
                                                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                                                                                    </button>
                                                                                </div>

                                                                                <button 
                                                                                    type="button" 
                                                                                    onclick="removeRepeaterItem(this, 'subcomp')" 
                                                                                    class="px-2 py-1 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg text-xs font-semibold cursor-pointer transition-colors flex items-center gap-1 btn-remove-item {{ count($subInstKeys) <= 1 ? 'hidden' : '' }}"
                                                                                >
                                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                                                    <span>Remove</span>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                                            @foreach($subFields as $field)
                                                                                @php
                                                                                    $fData = $multiFieldData[$comp->id . '_' . $subComp->id][$instIdx][$field->id] ?? null;
                                                                                @endphp
                                                                                <x-dynamic-field 
                                                                                    :field="$field" 
                                                                                    :comp="$comp" 
                                                                                    :subComp="$subComp" 
                                                                                    :instanceIndex="$instIdx"
                                                                                    :value="$fData?->content_value" 
                                                                                    :filePath="$fData?->file_path" 
                                                                                    :disabledCondition="'activeComponentId && activeComponentId !== ' . $comp->id" 
                                                                                    :hideLabel="$subFields->count() === 1"
                                                                                />
                                                                            @endforeach
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>

                                                            <!-- Template for dynamic add -->
                                                            <template id="template-subcomp-{{ $comp->id }}-{{ $subComp->id }}">
                                                                <div class="repeater-sub-item p-3 bg-slate-50/70 rounded-xl border border-slate-200 relative space-y-2.5 shadow-2xs">
                                                                    <div class="flex items-center justify-between pb-1.5 border-b border-slate-200/70 repeater-item-header">
                                                                        <div class="flex items-center gap-2">
                                                                            <span class="inline-flex items-center justify-center px-2 py-0.5 rounded text-[11px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200 repeater-index-badge">
                                                                                #__DISPLAY_INDEX__
                                                                            </span>
                                                                            <span class="text-xs font-bold text-slate-700">{{ $subComp->component_name }}</span>
                                                                        </div>
                                                                        
                                                                        <div class="flex items-center gap-1.5">
                                                                            <div class="flex items-center gap-1 bg-white px-2 py-0.5 rounded-lg border border-slate-200 shadow-2xs">
                                                                                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Order</span>
                                                                                <input 
                                                                                    type="number" 
                                                                                    min="1" 
                                                                                    value="__DISPLAY_NUM__" 
                                                                                    title="Set order position"
                                                                                    onchange="setRepeaterItemOrder(this, 'subcomp')"
                                                                                    class="w-12 h-6 text-center text-xs font-bold text-slate-800 bg-slate-50 border border-slate-300 rounded focus:bg-white focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 focus:outline-none repeater-order-input"
                                                                                >
                                                                                <button 
                                                                                    type="button" 
                                                                                    onclick="moveRepeaterItem(this, 'up', 'subcomp')"
                                                                                    title="Move Up" 
                                                                                    class="w-6 h-6 rounded flex items-center justify-center text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 transition-colors disabled:opacity-25 disabled:pointer-events-none cursor-pointer btn-move-up"
                                                                                >
                                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>
                                                                                </button>
                                                                                <button 
                                                                                    type="button" 
                                                                                    onclick="moveRepeaterItem(this, 'down', 'subcomp')"
                                                                                    title="Move Down" 
                                                                                    class="w-6 h-6 rounded flex items-center justify-center text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 transition-colors disabled:opacity-25 disabled:pointer-events-none cursor-pointer btn-move-down"
                                                                                >
                                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                                                                                </button>
                                                                            </div>

                                                                            <button 
                                                                                type="button" 
                                                                                onclick="removeRepeaterItem(this, 'subcomp')" 
                                                                                class="px-2 py-1 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg text-xs font-semibold cursor-pointer transition-colors flex items-center gap-1 btn-remove-item"
                                                                            >
                                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                                                <span>Remove</span>
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                                        @foreach($subFields as $field)
                                                                            <x-dynamic-field 
                                                                                :field="$field" 
                                                                                :comp="$comp" 
                                                                                :subComp="$subComp" 
                                                                                instanceIndex="__INDEX__"
                                                                                :value="null" 
                                                                                :filePath="null" 
                                                                                :disabledCondition="'activeComponentId && activeComponentId !== ' . $comp->id" 
                                                                                :hideLabel="$subFields->count() === 1"
                                                                            />
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            </template>

                                                            <div x-show="activeInstanceIndex === null" class="pt-1">
                                                                <button 
                                                                    type="button" 
                                                                    onclick="addSubRepeaterItem({{ $comp->id }}, {{ $subComp->id }})" 
                                                                    class="w-full py-2 px-3 rounded-lg border border-dashed border-indigo-300 bg-indigo-50/40 hover:bg-indigo-50 text-indigo-700 font-bold text-xs flex items-center justify-center gap-1.5 cursor-pointer transition-colors"
                                                                >
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                                    <span>+ Add Another {{ $subComp->component_name }}</span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @elseif($subFields->isNotEmpty())
                                                        @php
                                                            $isSubSingle = $subFields->count() === 1;
                                                            $subPrimary = $subFields->first();
                                                        @endphp
                                                        <div class="space-y-1.5">
                                                            <div class="flex items-center justify-between mb-1">
                                                                <label class="flex items-center gap-1.5 text-xs font-bold text-slate-700 tracking-wide uppercase">
                                                                    <span>{{ $subComp->component_name }}</span>
                                                                    @if($isSubSingle && $subPrimary->is_required)
                                                                        <span class="text-rose-500 font-bold">*</span>
                                                                    @endif
                                                                </label>
                                                                <div class="flex items-center gap-1.5">
                                                                    @if($isSubSingle)
                                                                        <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full border bg-indigo-50 text-indigo-700 border-indigo-200/70">
                                                                            {{ $subPrimary->field_type }}
                                                                        </span>
                                                                    @endif
                                                                    <span class="text-[10px] font-mono text-slate-400">/{{ $subComp->component_slug }}</span>
                                                                </div>
                                                            </div>
                                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                                @foreach($subFields as $field)
                                                                    @php
                                                                        $fData = $fieldData[$comp->id . '_' . $subComp->id][$field->id] ?? null;
                                                                    @endphp
                                                                    <x-dynamic-field 
                                                                        :field="$field" 
                                                                        :comp="$comp" 
                                                                        :subComp="$subComp" 
                                                                        :value="$fData?->content_value" 
                                                                        :filePath="$fData?->file_path" 
                                                                        :disabledCondition="'activeComponentId && activeComponentId !== ' . $comp->id" 
                                                                        :hideLabel="$isSubSingle"
                                                                    />
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @else
                                                        {{-- Legacy Fallback for subcomponents without field definitions --}}
                                                        @if(str_contains($subNameAndSlug, 'subheading'))
                                                            <div>
                                                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                                                    {{ $subComp->component_name }}
                                                                </label>
                                                                <input type="text" :disabled="activeComponentId && activeComponentId !== {{ $comp->id }}" name="components[{{ $comp->id }}][subcomponents][{{ $subComp->id }}][value]" value="{{ old('components.'.$comp->id.'.subcomponents.'.$subComp->id.'.value', $subExisting?->content_value) }}" placeholder="Enter subheading text..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all font-medium bg-white">
                                                            </div>
                                                        @elseif(str_contains($subNameAndSlug, 'heading'))
                                                            <div>
                                                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                                                    {{ $subComp->component_name }}
                                                                </label>
                                                                <input type="text" :disabled="activeComponentId && activeComponentId !== {{ $comp->id }}" name="components[{{ $comp->id }}][subcomponents][{{ $subComp->id }}][value]" value="{{ old('components.'.$comp->id.'.subcomponents.'.$subComp->id.'.value', $subExisting?->content_value) }}" placeholder="Enter heading title..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all font-bold bg-white">
                                                            </div>
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
                                                        @elseif(str_contains($subNameAndSlug, 'paragraph') || str_contains($subNameAndSlug, 'textarea') || str_contains($subNameAndSlug, 'desc'))
                                                            <div>
                                                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                                                    {{ $subComp->component_name }} <span class="text-slate-400 text-[11px] font-normal font-mono">(&lt;textarea&gt;)</span>
                                                                </label>
                                                                <textarea :disabled="activeComponentId && activeComponentId !== {{ $comp->id }}" name="components[{{ $comp->id }}][subcomponents][{{ $subComp->id }}][value]" rows="3" placeholder="Enter paragraph description or content here..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all leading-relaxed bg-white">{{ old('components.'.$comp->id.'.subcomponents.'.$subComp->id.'.value', $subExisting?->content_value) }}</textarea>
                                                            </div>
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
                                                        @else
                                                            <div>
                                                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                                                    {{ $subComp->component_name }}
                                                                </label>
                                                                <input type="text" :disabled="activeComponentId && activeComponentId !== {{ $comp->id }}" name="components[{{ $comp->id }}][subcomponents][{{ $subComp->id }}][value]" value="{{ old('components.'.$comp->id.'.subcomponents.'.$subComp->id.'.value', $subExisting?->content_value) }}" placeholder="Enter value for {{ $subComp->component_name }}..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all font-medium bg-white">
                                                            </div>
                                                        @endif
                                                    @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        @php
                                            $compFields = $comp->fields->where('is_active', true)->sortBy('sort_order');
                                            $isCompMultiple = (bool)($comp->pivot->is_multiple ?? $comp->is_multiple);
                                            $compInstMap = $multiFieldData[$comp->id] ?? [];
                                            $compInstKeys = array_keys($compInstMap);
                                            if (empty($compInstKeys)) {
                                                $compInstKeys = [0];
                                            }
                                        @endphp

                                        @if($isCompMultiple && $compFields->isNotEmpty())
                                            {{-- Top-Level Repeater Component (e.g. Gallery Images, Buttons, Cards) --}}
                                            <div class="p-4 rounded-xl border border-slate-200/90 bg-white space-y-3.5">
                                                <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                                                    <div class="flex items-center gap-2">
                                                        <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wider">{{ $comp->component_name }}</h4>
                                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">Repeater</span>
                                                    </div>
                                                    <span class="text-[10px] font-mono text-slate-400">/{{ $comp->component_slug }}</span>
                                                </div>

                                                <div id="repeater-comp-{{ $comp->id }}" data-next-index="{{ count($compInstKeys) }}" class="space-y-3.5">
                                                    @foreach($compInstKeys as $pos => $instIdx)
                                                        <div 
                                                            class="repeater-item p-3.5 bg-slate-50/70 rounded-xl border border-slate-200 relative space-y-3 shadow-2xs"
                                                            data-pos="{{ $pos }}"
                                                            x-show="activeInstanceIndex === null || activeInstanceIndex === {{ $pos }}"
                                                        >
                                                            <div class="flex items-center justify-between pb-1.5 border-b border-slate-200/70 repeater-item-header">
                                                                <div class="flex items-center gap-2">
                                                                    <span class="inline-flex items-center justify-center px-2 py-0.5 rounded text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200 repeater-index-badge">
                                                                        #{{ $pos + 1 }}
                                                                    </span>
                                                                    <span class="text-xs font-bold text-slate-700">{{ $comp->component_name }}</span>
                                                                </div>
                                                                
                                                                <div class="flex items-center gap-1.5" x-show="activeInstanceIndex === null">
                                                                    <div class="flex items-center gap-1 bg-white px-2 py-0.5 rounded-lg border border-slate-200 shadow-2xs">
                                                                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Order</span>
                                                                        <input 
                                                                            type="number" 
                                                                            min="1" 
                                                                            max="{{ count($compInstKeys) }}"
                                                                            value="{{ $pos + 1 }}" 
                                                                            title="Set order position"
                                                                            onchange="setRepeaterItemOrder(this, 'comp')"
                                                                            class="w-12 h-6 text-center text-xs font-bold text-slate-800 bg-slate-50 border border-slate-300 rounded focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none repeater-order-input"
                                                                        >
                                                                        <button 
                                                                            type="button" 
                                                                            onclick="moveRepeaterItem(this, 'up', 'comp')"
                                                                            title="Move Up" 
                                                                            {{ $pos === 0 ? 'disabled' : '' }}
                                                                            class="w-6 h-6 rounded flex items-center justify-center text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition-colors disabled:opacity-25 disabled:pointer-events-none cursor-pointer btn-move-up"
                                                                        >
                                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>
                                                                        </button>
                                                                        <button 
                                                                            type="button" 
                                                                            onclick="moveRepeaterItem(this, 'down', 'comp')"
                                                                            title="Move Down" 
                                                                            {{ $pos === count($compInstKeys) - 1 ? 'disabled' : '' }}
                                                                            class="w-6 h-6 rounded flex items-center justify-center text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition-colors disabled:opacity-25 disabled:pointer-events-none cursor-pointer btn-move-down"
                                                                        >
                                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                                                                        </button>
                                                                    </div>

                                                                    <button 
                                                                        type="button" 
                                                                        onclick="removeRepeaterItem(this, 'comp')" 
                                                                        class="px-2 py-1 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg text-xs font-semibold cursor-pointer transition-colors flex items-center gap-1 btn-remove-item {{ count($compInstKeys) <= 1 ? 'hidden' : '' }}"
                                                                    >
                                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                                        <span>Remove</span>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                                @foreach($compFields as $field)
                                                                    @php
                                                                        $fData = $multiFieldData[$comp->id][$instIdx][$field->id] ?? null;
                                                                    @endphp
                                                                    <x-dynamic-field 
                                                                        :field="$field" 
                                                                        :comp="$comp" 
                                                                        :subComp="null" 
                                                                        :instanceIndex="$instIdx"
                                                                        :value="$fData?->content_value" 
                                                                        :filePath="$fData?->file_path" 
                                                                        :disabledCondition="'activeComponentId && activeComponentId !== ' . $comp->id" 
                                                                        :hideLabel="$compFields->count() === 1"
                                                                    />
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <!-- Template for dynamic add -->
                                                <template id="template-comp-{{ $comp->id }}">
                                                    <div class="repeater-item p-3.5 bg-slate-50/70 rounded-xl border border-slate-200 relative space-y-3 shadow-2xs">
                                                        <div class="flex items-center justify-between pb-1.5 border-b border-slate-200/70 repeater-item-header">
                                                            <div class="flex items-center gap-2">
                                                                <span class="inline-flex items-center justify-center px-2 py-0.5 rounded text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200 repeater-index-badge">
                                                                    #__DISPLAY_INDEX__
                                                                </span>
                                                                <span class="text-xs font-bold text-slate-700">{{ $comp->component_name }}</span>
                                                            </div>
                                                            
                                                            <div class="flex items-center gap-1.5">
                                                                <div class="flex items-center gap-1 bg-white px-2 py-0.5 rounded-lg border border-slate-200 shadow-2xs">
                                                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Order</span>
                                                                    <input 
                                                                        type="number" 
                                                                        min="1" 
                                                                        value="__DISPLAY_NUM__" 
                                                                        title="Set order position"
                                                                        onchange="setRepeaterItemOrder(this, 'comp')"
                                                                        class="w-12 h-6 text-center text-xs font-bold text-slate-800 bg-slate-50 border border-slate-300 rounded focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none repeater-order-input"
                                                                    >
                                                                    <button 
                                                                        type="button" 
                                                                        onclick="moveRepeaterItem(this, 'up', 'comp')"
                                                                        title="Move Up" 
                                                                        class="w-6 h-6 rounded flex items-center justify-center text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition-colors disabled:opacity-25 disabled:pointer-events-none cursor-pointer btn-move-up"
                                                                    >
                                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>
                                                                    </button>
                                                                    <button 
                                                                        type="button" 
                                                                        onclick="moveRepeaterItem(this, 'down', 'comp')"
                                                                        title="Move Down" 
                                                                        class="w-6 h-6 rounded flex items-center justify-center text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition-colors disabled:opacity-25 disabled:pointer-events-none cursor-pointer btn-move-down"
                                                                    >
                                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                                                                    </button>
                                                                </div>

                                                                <button 
                                                                    type="button" 
                                                                    onclick="removeRepeaterItem(this, 'comp')" 
                                                                    class="px-2 py-1 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg text-xs font-semibold cursor-pointer transition-colors flex items-center gap-1 btn-remove-item"
                                                                >
                                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                                    <span>Remove</span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                            @foreach($compFields as $field)
                                                                <x-dynamic-field 
                                                                    :field="$field" 
                                                                    :comp="$comp" 
                                                                    :subComp="null" 
                                                                    instanceIndex="__INDEX__"
                                                                    :value="null" 
                                                                    :filePath="null" 
                                                                    :disabledCondition="'activeComponentId && activeComponentId !== ' . $comp->id" 
                                                                    :hideLabel="$compFields->count() === 1"
                                                                />
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </template>

                                                <div x-show="activeInstanceIndex === null" class="pt-1">
                                                    <button 
                                                        type="button" 
                                                        onclick="addRepeaterItem({{ $comp->id }})" 
                                                        class="w-full py-2.5 px-4 rounded-xl border-2 border-dashed border-blue-400 bg-blue-50/40 hover:bg-blue-50 text-blue-600 font-bold text-xs flex items-center justify-center gap-2 cursor-pointer transition-colors shadow-2xs"
                                                    >
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                        <span>+ Add Another {{ $comp->component_name }}</span>
                                                    </button>
                                                </div>
                                            </div>
                                        @elseif($compFields->isNotEmpty())
                                            @php
                                                $cSlug = strtolower($comp->component_slug ?? $comp->component_name);
                                                $isSingleField = $compFields->count() === 1;
                                                $singleField = $compFields->first();

                                                // Icon & theme classes tailored for the component type
                                                if (str_contains($cSlug, 'head') || str_contains($cSlug, 'title')) {
                                                    $iconBg = 'bg-blue-50 text-blue-600 border-blue-200/70';
                                                    $iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>';
                                                } elseif (str_contains($cSlug, 'subhead') || str_contains($cSlug, 'subtitle')) {
                                                    $iconBg = 'bg-sky-50 text-sky-600 border-sky-200/70';
                                                    $iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h6"/>';
                                                } elseif (str_contains($cSlug, 'image') || str_contains($cSlug, 'photo') || str_contains($cSlug, 'banner') || str_contains($cSlug, 'logo')) {
                                                    $iconBg = 'bg-emerald-50 text-emerald-600 border-emerald-200/70';
                                                    $iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>';
                                                } elseif (str_contains($cSlug, 'video')) {
                                                    $iconBg = 'bg-purple-50 text-purple-600 border-purple-200/70';
                                                    $iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>';
                                                } elseif (str_contains($cSlug, 'button') || str_contains($cSlug, 'link') || str_contains($cSlug, 'action')) {
                                                    $iconBg = 'bg-indigo-50 text-indigo-600 border-indigo-200/70';
                                                    $iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>';
                                                } elseif (str_contains($cSlug, 'desc') || str_contains($cSlug, 'para') || str_contains($cSlug, 'text') || str_contains($cSlug, 'content')) {
                                                    $iconBg = 'bg-amber-50 text-amber-600 border-amber-200/70';
                                                    $iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h10"/>';
                                                } else {
                                                    $iconBg = 'bg-slate-100 text-slate-600 border-slate-200';
                                                    $iconSvg = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>';
                                                }
                                            @endphp
                                            <div class="space-y-1.5">
                                                <div class="flex items-center justify-between mb-1">
                                                    <label class="flex items-center gap-1.5 text-xs font-bold text-slate-700 tracking-wide uppercase">
                                                        <span>{{ $comp->component_name }}</span>
                                                        @if($isSingleField && $singleField->is_required)
                                                            <span class="text-rose-500 font-extrabold text-xs" title="Required field">*</span>
                                                        @endif
                                                    </label>
                                                    <div class="flex items-center gap-2">
                                                        @if($isSingleField)
                                                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full border {{ $singleField->field_type === 'image' ? 'bg-emerald-50 text-emerald-700 border-emerald-200/70' : ($singleField->field_type === 'video' ? 'bg-purple-50 text-purple-700 border-purple-200/70' : 'bg-blue-50 text-blue-700 border-blue-200/70') }}">
                                                                {{ $singleField->field_type }}
                                                            </span>
                                                        @else
                                                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full border bg-slate-100 text-slate-600 border-slate-200">
                                                                {{ $compFields->count() }} Fields
                                                            </span>
                                                        @endif
                                                        <span class="text-[10px] font-mono text-slate-400">/{{ $comp->component_slug }}</span>
                                                    </div>
                                                </div>
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                    @foreach($compFields as $field)
                                                        @php
                                                            $fData = $fieldData[$comp->id][$field->id] ?? null;
                                                        @endphp
                                                        <x-dynamic-field 
                                                            :field="$field" 
                                                            :comp="$comp" 
                                                            :subComp="null" 
                                                            :value="$fData?->content_value" 
                                                            :filePath="$fData?->file_path" 
                                                            :disabledCondition="'activeComponentId && activeComponentId !== ' . $comp->id" 
                                                            :hideLabel="$isSingleField"
                                                        />
                                                    @endforeach
                                                </div>
                                            </div>
                                        @else
                                            {{-- Legacy Fallback for top-level component without field definitions --}}
                                            <div class="space-y-1.5">
                                                @if(str_contains($nameAndSlug, 'subheading'))
                                                    <div>
                                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                                            {{ $comp->component_name }}
                                                        </label>
                                                        <input type="text" :disabled="activeComponentId && activeComponentId !== {{ $comp->id }}" name="components[{{ $comp->id }}][value]" value="{{ old('components.'.$comp->id.'.value', $existing?->content_value) }}" placeholder="Enter subheading text..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/90 bg-slate-50/50 hover:bg-white focus:bg-white text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all font-medium">
                                                    </div>
                                                @elseif(str_contains($nameAndSlug, 'heading'))
                                                    <div>
                                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                                            {{ $comp->component_name }}
                                                        </label>
                                                        <input type="text" :disabled="activeComponentId && activeComponentId !== {{ $comp->id }}" name="components[{{ $comp->id }}][value]" value="{{ old('components.'.$comp->id.'.value', $existing?->content_value) }}" placeholder="Enter heading title..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/90 bg-slate-50/50 hover:bg-white focus:bg-white text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all font-bold">
                                                    </div>
                                                @elseif(str_contains($nameAndSlug, 'button'))
                                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-data="{ btnText: '{{ addslashes(old('components.'.$comp->id.'.value', $existing?->content_value ?? 'Click Here')) }}' }">
                                                        <div>
                                                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                                                {{ $comp->component_name }} Label
                                                            </label>
                                                            <input type="text" :disabled="activeComponentId && activeComponentId !== {{ $comp->id }}" x-model="btnText" name="components[{{ $comp->id }}][value]" placeholder="e.g. Get Started / Click Here" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/90 bg-slate-50/50 hover:bg-white focus:bg-white text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all font-medium">
                                                        </div>
                                                        <div>
                                                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                                                {{ $comp->component_name }} Target Link / URL
                                                            </label>
                                                            <div class="flex items-center gap-2">
                                                                <input type="text" :disabled="activeComponentId && activeComponentId !== {{ $comp->id }}" name="components[{{ $comp->id }}][extra]" value="{{ old('components.'.$comp->id.'.extra', $existing?->extra_value) }}" placeholder="e.g. #contact or https://example.com" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/90 bg-slate-50/50 hover:bg-white focus:bg-white text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all font-medium">
                                                                <input type="button" :value="btnText || 'Click Here'" value="{{ $existing?->content_value ?: 'Click Here' }}" class="shrink-0 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs cursor-pointer shadow-xs transition-colors">
                                                            </div>
                                                        </div>
                                                    </div>
                                                @elseif(str_contains($nameAndSlug, 'paragraph') || str_contains($nameAndSlug, 'textarea') || str_contains($nameAndSlug, 'desc'))
                                                    <div>
                                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                                            {{ $comp->component_name }} <span class="text-slate-400 text-[11px] font-normal font-mono">(&lt;textarea&gt;)</span>
                                                        </label>
                                                        <textarea :disabled="activeComponentId && activeComponentId !== {{ $comp->id }}" name="components[{{ $comp->id }}][value]" rows="3" placeholder="Enter paragraph description or content here..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/90 bg-slate-50/50 hover:bg-white focus:bg-white text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all leading-relaxed">{{ old('components.'.$comp->id.'.value', $existing?->content_value) }}</textarea>
                                                    </div>
                                                @elseif(str_contains($nameAndSlug, 'image') || str_contains($nameAndSlug, 'photo') || str_contains($nameAndSlug, 'banner'))
                                                    <div>
                                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                                            {{ $comp->component_name }}
                                                        </label>
                                                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                                                            <input type="file" accept="image/*" :disabled="activeComponentId && activeComponentId !== {{ $comp->id }}" name="components[{{ $comp->id }}][file]" class="w-full text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer rounded-xl border border-slate-200/90 p-1.5 bg-slate-50/50">
                                                            @if($existing?->file_path && file_exists(public_path($existing->file_path)))
                                                                <div class="flex items-center gap-2.5 shrink-0 p-1.5 bg-slate-50 rounded-xl border border-slate-200">
                                                                    <img src="{{ asset($existing->file_path) }}" alt="Preview" class="w-8 h-8 object-cover rounded-lg">
                                                                    <span class="text-xs text-slate-600 font-medium max-w-[140px] truncate">{{ basename($existing->file_path) }}</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @elseif(str_contains($nameAndSlug, 'video'))
                                                    <div>
                                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                                            {{ $comp->component_name }}
                                                        </label>
                                                        <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                                                            <input type="file" accept="video/*" :disabled="activeComponentId && activeComponentId !== {{ $comp->id }}" name="components[{{ $comp->id }}][file]" class="w-full text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 cursor-pointer rounded-xl border border-slate-200/90 p-1.5 bg-slate-50/50">
                                                            @if($existing?->file_path && file_exists(public_path($existing->file_path)))
                                                                <div class="flex items-center gap-2.5 shrink-0 p-1.5 bg-slate-50 rounded-xl border border-slate-200">
                                                                    <video src="{{ asset($existing->file_path) }}" class="w-12 h-8 object-cover rounded-lg"></video>
                                                                    <span class="text-xs text-slate-600 font-medium max-w-[140px] truncate">{{ basename($existing->file_path) }}</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    <div>
                                                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                                            {{ $comp->component_name }}
                                                        </label>
                                                        <input type="text" :disabled="activeComponentId && activeComponentId !== {{ $comp->id }}" name="components[{{ $comp->id }}][value]" value="{{ old('components.'.$comp->id.'.value', $existing?->content_value) }}" placeholder="Enter value for {{ $comp->component_name }}..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200/90 bg-slate-50/50 hover:bg-white focus:bg-white text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all font-medium">
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    @endif
                                </div>

                            @endforeach
                            </div>
                        </div>

                        <!-- Modal Actions Footer -->
                        <div class="px-6 py-4 bg-white/95 backdrop-blur-sm border-t border-slate-100 flex items-center justify-end gap-2.5 rounded-b-2xl">
                            <button 
                                    @click="openModal = false; activeComponentId = null; activeComponentName = ''; activeSubCompId = null; activeInstanceIndex = null; activeInstanceLabel = ''" 
                                    type="button" 
                                    class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-slate-50 hover:text-slate-900 transition-all cursor-pointer shadow-2xs"
                                >
                                    Cancel
                                </button>
                                <button 
                                    type="submit" 
                                    class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-xs font-bold transition-all shadow-md shadow-blue-500/25 hover:shadow-lg hover:shadow-blue-500/30 cursor-pointer flex items-center gap-2"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span x-text="activeInstanceLabel ? 'Save ' + activeInstanceLabel : (activeComponentId ? 'Save ' + activeComponentName : 'Save Section Content')">Save Section Content</span>
                                </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>

</div>
@push('scripts')
<script>
function moveRepeaterItem(btn, direction, type) {
    const itemClass = type === 'subcomp' ? '.repeater-sub-item' : '.repeater-item';
    const item = btn.closest(itemClass);
    if (!item) return;
    const container = item.parentElement;
    
    if (direction === 'up') {
        const prev = item.previousElementSibling;
        if (prev && prev.matches(itemClass)) {
            container.insertBefore(item, prev);
        }
    } else if (direction === 'down') {
        const next = item.nextElementSibling;
        if (next && next.matches(itemClass)) {
            container.insertBefore(next, item);
        }
    }
    
    refreshRepeaterIndices(container, type);
}

function setRepeaterItemOrder(input, type) {
    const itemClass = type === 'subcomp' ? '.repeater-sub-item' : '.repeater-item';
    const item = input.closest(itemClass);
    if (!item) return;
    const container = item.parentElement;
    const items = Array.from(container.querySelectorAll(itemClass));
    const total = items.length;
    const currentIndex = items.indexOf(item);
    
    let targetPos = parseInt(input.value);
    if (isNaN(targetPos) || targetPos < 1) targetPos = 1;
    if (targetPos > total) targetPos = total;
    
    const targetIndex = targetPos - 1;
    if (targetIndex !== currentIndex) {
        if (targetIndex >= total - 1) {
            container.appendChild(item);
        } else if (targetIndex > currentIndex) {
            container.insertBefore(item, items[targetIndex].nextSibling);
        } else {
            container.insertBefore(item, items[targetIndex]);
        }
    }
    
    refreshRepeaterIndices(container, type);
}

function removeRepeaterItem(btn, type) {
    const itemClass = type === 'subcomp' ? '.repeater-sub-item' : '.repeater-item';
    const item = btn.closest(itemClass);
    if (!item) return;
    const container = item.parentElement;
    item.remove();
    refreshRepeaterIndices(container, type);
}

function refreshRepeaterIndices(container, type) {
    if (!container) return;
    const itemClass = type === 'subcomp' ? '.repeater-sub-item' : '.repeater-item';
    const items = container.querySelectorAll(itemClass);
    const total = items.length;

    items.forEach((item, index) => {
        const displayNum = index + 1;

        // 1. Update index badge #1, #2...
        const badge = item.querySelector('.repeater-index-badge');
        if (badge) {
            badge.textContent = '#' + displayNum;
        }

        // 2. Update order input
        const orderInput = item.querySelector('.repeater-order-input');
        if (orderInput) {
            orderInput.value = displayNum;
            orderInput.max = total;
        }

        // 3. Update Move Up / Down button disabled state
        const upBtn = item.querySelector('.btn-move-up');
        if (upBtn) {
            upBtn.disabled = (index === 0);
        }
        const downBtn = item.querySelector('.btn-move-down');
        if (downBtn) {
            downBtn.disabled = (index === total - 1);
        }

        // 4. Update Remove button visibility: hide if only 1 item, or show if > 1
        const removeBtn = item.querySelector('.btn-remove-item');
        if (removeBtn) {
            if (total <= 1) {
                removeBtn.classList.add('hidden');
            } else {
                removeBtn.classList.remove('hidden');
            }
        }

        // 5. Update input / select / textarea name attributes to match DOM order index
        const formFields = item.querySelectorAll('input, select, textarea');
        formFields.forEach(field => {
            if (field.name && field.name.includes('[instances]')) {
                field.name = field.name.replace(/\[instances\]\[(?:\d+|__INDEX__)\]/g, `[instances][${index}]`);
            }
            if (field.id && field.id.includes('_inst_')) {
                field.id = field.id.replace(/_inst_(?:\d+|__INDEX__)_/g, `_inst_${index}_`);
            }
        });
    });

    container.dataset.nextIndex = total;
}

function addRepeaterItem(compId) {
    const container = document.getElementById('repeater-comp-' + compId);
    const template = document.getElementById('template-comp-' + compId);
    if (!container || !template) return;
    
    const existingItems = container.querySelectorAll('.repeater-item');
    const nextIndex = existingItems.length;
    const displayIndex = nextIndex + 1;
    
    let html = template.innerHTML
        .replace(/__INDEX__/g, nextIndex)
        .replace(/__DISPLAY_INDEX__/g, '#' + displayIndex)
        .replace(/__DISPLAY_NUM__/g, displayIndex);
        
    const wrapper = document.createElement('div');
    wrapper.innerHTML = html.trim();
    container.appendChild(wrapper.firstElementChild);

    refreshRepeaterIndices(container, 'comp');
}

function addSubRepeaterItem(compId, subCompId) {
    const container = document.getElementById('repeater-subcomp-' + compId + '-' + subCompId);
    const template = document.getElementById('template-subcomp-' + compId + '-' + subCompId);
    if (!container || !template) return;
    
    const existingItems = container.querySelectorAll('.repeater-sub-item');
    const nextIndex = existingItems.length;
    const displayIndex = nextIndex + 1;
    
    let html = template.innerHTML
        .replace(/__INDEX__/g, nextIndex)
        .replace(/__DISPLAY_INDEX__/g, '#' + displayIndex)
        .replace(/__DISPLAY_NUM__/g, displayIndex);
        
    const wrapper = document.createElement('div');
    wrapper.innerHTML = html.trim();
    container.appendChild(wrapper.firstElementChild);

    refreshRepeaterIndices(container, 'subcomp');
}

// ==========================================
// In-Table Reordering Functions (Direct Row Reorder)
// ==========================================
function moveTableRow(btn, direction) {
    const row = btn.closest('tr.repeater-table-row');
    if (!row) return;
    const groupKey = row.getAttribute('data-group-key');
    const groupRows = Array.from(document.querySelectorAll(`tr.repeater-table-row[data-group-key="${groupKey}"]`));
    const currentIndex = groupRows.indexOf(row);
    if (currentIndex === -1) return;

    if (direction === 'up' && currentIndex > 0) {
        row.parentNode.insertBefore(row, groupRows[currentIndex - 1]);
        refreshTableGroupOrder(groupKey, true);
    } else if (direction === 'down' && currentIndex < groupRows.length - 1) {
        row.parentNode.insertBefore(row, groupRows[currentIndex + 1].nextSibling);
        refreshTableGroupOrder(groupKey, true);
    }
}

function handleTableOrderChange(input) {
    const row = input.closest('tr.repeater-table-row');
    if (!row) return;
    const groupKey = row.getAttribute('data-group-key');
    const groupRows = Array.from(document.querySelectorAll(`tr.repeater-table-row[data-group-key="${groupKey}"]`));
    const currentIndex = groupRows.indexOf(row);
    if (currentIndex === -1) return;

    let targetNum = parseInt(input.value);
    if (isNaN(targetNum)) targetNum = currentIndex + 1;
    targetNum = Math.max(1, Math.min(groupRows.length, targetNum));
    const targetIndex = targetNum - 1;

    if (targetIndex !== currentIndex) {
        if (targetIndex > currentIndex) {
            row.parentNode.insertBefore(row, groupRows[targetIndex].nextSibling);
        } else {
            row.parentNode.insertBefore(row, groupRows[targetIndex]);
        }
    }
    refreshTableGroupOrder(groupKey, true);
}

function refreshTableGroupOrder(groupKey, saveToServer = false) {
    const groupRows = Array.from(document.querySelectorAll(`tr.repeater-table-row[data-group-key="${groupKey}"]`));
    const total = groupRows.length;
    const orderedIndices = [];

    groupRows.forEach((r, idx) => {
        const displayNum = idx + 1;
        const prefix = r.getAttribute('data-parent-prefix') || '';
        
        // 1. Update sequence number column (e.g. 4.1, 4.2...)
        const seqCell = r.querySelector('.table-seq-num');
        if (seqCell) {
            seqCell.textContent = prefix + displayNum;
        }

        // 2. Update order input
        const orderInput = r.querySelector('.table-order-input');
        if (orderInput) {
            orderInput.value = displayNum;
            orderInput.max = total;
            const originalInstIdx = parseInt(orderInput.getAttribute('data-inst-idx'));
            orderedIndices.push(originalInstIdx);
        }

        // 3. Update up/down buttons disabled state
        const upBtn = r.querySelector('.table-btn-up');
        if (upBtn) upBtn.disabled = (idx === 0);

        const downBtn = r.querySelector('.table-btn-down');
        if (downBtn) downBtn.disabled = (idx === total - 1);
    });

    if (saveToServer && orderedIndices.length > 0) {
        saveTableReorderToServer(groupKey, orderedIndices);
    }
}

function saveTableReorderToServer(groupKey, orderedIndices) {
    // groupKey format: comp-{compId}-sub-{subCompId}
    const match = groupKey.match(/^comp-(\d+)-sub-(\d+|null)$/);
    if (!match) return;

    const compId = parseInt(match[1]);
    const subCompId = match[2] === 'null' ? null : parseInt(match[2]);

    showReorderToast('Saving new order...', 'loading');

    fetch("{{ route('admin.section.reorderInstances', $section->id) }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            component_id: compId,
            sub_component_id: subCompId,
            ordered_indices: orderedIndices
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showReorderToast('Order updated successfully! 🎉', 'success');
            // Update the data-inst-idx to reflect new positions for future reorders without reloading
            const groupRows = Array.from(document.querySelectorAll(`tr.repeater-table-row[data-group-key="${groupKey}"]`));
            groupRows.forEach((r, idx) => {
                const orderInput = r.querySelector('.table-order-input');
                if (orderInput) {
                    orderInput.setAttribute('data-inst-idx', idx);
                }
            });
        } else {
            showReorderToast(data.message || 'Failed to update order', 'error');
        }
    })
    .catch(err => {
        console.error(err);
        showReorderToast('Network error while saving order', 'error');
    });
}

function showReorderToast(message, type = 'info') {
    let toast = document.getElementById('table-reorder-toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'table-reorder-toast';
        toast.className = 'fixed bottom-5 right-5 z-50 px-4 py-2.5 rounded-xl shadow-xl font-medium text-xs flex items-center gap-2 transition-all transform duration-300';
        document.body.appendChild(toast);
    }

    if (type === 'loading') {
        toast.className = 'fixed bottom-5 right-5 z-50 px-4 py-2.5 rounded-xl shadow-xl font-medium text-xs flex items-center gap-2 transition-all transform duration-300 bg-slate-900 text-white';
        toast.innerHTML = `<svg class="animate-spin -ml-0.5 mr-1 h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> <span>${message}</span>`;
    } else if (type === 'success') {
        toast.className = 'fixed bottom-5 right-5 z-50 px-4 py-2.5 rounded-xl shadow-xl font-medium text-xs flex items-center gap-2 transition-all transform duration-300 bg-emerald-600 text-white';
        toast.innerHTML = `<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg> <span>${message}</span>`;
        setTimeout(() => { toast.classList.add('opacity-0', 'translate-y-2'); }, 2500);
    } else {
        toast.className = 'fixed bottom-5 right-5 z-50 px-4 py-2.5 rounded-xl shadow-xl font-medium text-xs flex items-center gap-2 transition-all transform duration-300 bg-rose-600 text-white';
        toast.innerHTML = `<svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg> <span>${message}</span>`;
        setTimeout(() => { toast.classList.add('opacity-0', 'translate-y-2'); }, 3500);
    }
    toast.classList.remove('opacity-0', 'translate-y-2');
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[id^="repeater-subcomp-"]').forEach(c => refreshRepeaterIndices(c, 'subcomp'));
    document.querySelectorAll('[id^="repeater-comp-"]').forEach(c => refreshRepeaterIndices(c, 'comp'));
});
</script>
@endpush

@endsection
