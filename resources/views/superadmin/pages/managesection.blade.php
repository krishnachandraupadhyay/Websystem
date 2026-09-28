@extends('superadmin.layout')

@section('title', 'Manage Section Components')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="manageSectionApp()">



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

            <button @click="show = false" type="button" class="text-emerald-500 hover:text-emerald-700 p-1 rounded-lg hover:bg-emerald-100/60 transition-colors cursor-pointer" title="Dismiss">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    <!-- Error Flash Notification (Auto-dismisses in 5s) -->
    @if(isset($errors) && $errors->any())
        @php
            $uniqueErrors = array_unique($errors->all());
        @endphp
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
                <div class="w-6 h-6 rounded-full bg-rose-500 text-white flex items-center justify-center shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    @if(count($uniqueErrors) === 1)
                        <span>{{ $uniqueErrors[0] }}</span>
                    @else
                        <span class="font-bold block mb-1">Please fix the errors below:</span>
                        <ul class="list-disc list-inside text-xs space-y-0.5 pl-1 font-medium">
                            @foreach($uniqueErrors as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            <button @click="show = false" type="button" class="text-rose-500 hover:text-rose-700 p-1 rounded-lg hover:bg-rose-100/60 transition-colors cursor-pointer shrink-0" title="Dismiss">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    @endif

    <!-- Section Wise Components Table Card -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-2.5">
                <h2 class="text-base font-bold text-slate-800">All Sections & Their Components</h2>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-500 font-semibold font-mono">{{ $sections->count() }}</span>
            </div>
            <a 
                href="{{ route('Superadmin.addsection') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-sm shadow-blue-500/20 cursor-pointer flex items-center gap-1.5"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Add Section</span>
            </a>
        </div>

        @if($sections->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-200 py-16 flex flex-col items-center justify-center text-center">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-slate-800">No Sections Found</h3>
                <p class="text-xs text-slate-400 mt-1">Please create sections first to manage their components.</p>
                <a href="{{ route('Superadmin.addsection') }}" class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-blue-700 transition-colors">
                    + Add New Section
                </a>
            </div>
        @else
            <div class="overflow-x-auto rounded-xl border border-slate-200/80">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-[11px] font-bold tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">#</th>
                            <th class="px-5 py-3.5">Section Name</th>
                            <th class="px-5 py-3.5 text-center">Active Count</th>
                            <th class="px-5 py-3.5">Section Status</th>
                            <th class="px-5 py-3.5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($sections as $index => $section)
                            @php
                                $activeComps = $section->components->where('pivot.status', 1)->values();
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-5 py-4 text-xs font-semibold text-slate-400">
                                    {{ $index + 1 }}
                                </td>

                                <!-- Section Name & Title -->
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs shrink-0">
                                            {{ strtoupper(substr($section->section_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-slate-900 text-sm">
                                                {{ $section->section_name }}
                                            </h4>
                                            <p class="text-xs text-slate-400 font-mono">
                                                /{{ $section->section_slug }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Active Count Badge -->
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $activeComps->count() > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500' }}">
                                        {{ $activeComps->count() }} Active
                                    </span>
                                </td>

                                <!-- Section Status -->
                                <td class="px-5 py-4">
                                    <form action="{{ route('sections.toggleStatus', $section->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button 
                                            type="submit" 
                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold transition-all cursor-pointer shadow-2xs hover:scale-105 select-none {{ $section->status ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 hover:border-emerald-300' : 'bg-slate-100 text-slate-600 border border-slate-200 hover:bg-slate-200 hover:text-slate-800' }}"
                                            title="Click to {{ $section->status ? 'deactivate' : 'activate' }}"
                                        >
                                            <span class="w-1.5 h-1.5 rounded-full {{ $section->status ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                            <span>{{ $section->status ? 'Active' : 'Inactive' }}</span>
                                        </button>
                                    </form>
                                </td>

                                <!-- Action (View & Edit Component Buttons) -->
                                <td class="px-5 py-4 text-right">
                                    <div class="inline-flex items-center gap-1.5 justify-end">
                                        <button 
                                            type="button" 
                                            @click="openView({{ $section->id }})"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-emerald-600 hover:text-emerald-700 bg-white hover:bg-emerald-50 border border-slate-200 hover:border-emerald-300 transition-all cursor-pointer shadow-2xs"
                                            title="View Assigned Components"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                        <button 
                                            type="button" 
                                            @click="openEdit({{ $section->id }}, '{{ addslashes($section->section_name) }}', '{{ addslashes($section->section_title) }}')"
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-blue-600 hover:text-blue-700 bg-white hover:bg-blue-50 border border-slate-200 hover:border-blue-300 transition-all cursor-pointer shadow-2xs"
                                            title="Configure Components for this section"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Edit Modal Dialog for Adding / Removing Components -->
    <div 
        x-show="openModal" 
        x-cloak
        style="display: none;"
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-title" 
        role="dialog" 
        aria-modal="true"
        @keydown.escape.window="openModal = false"
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
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
            @click="openModal = false"
        ></div>

        <!-- Modal Dialog Placement -->
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <!-- Modal Content Box -->
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
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900" id="modal-title">
                                Manage Components: <span class="text-blue-600" x-text="editSectionName"></span>
                            </h3>
                            <p class="text-xs text-slate-400 mt-0.5">
                                Toggle ON to add components, or toggle OFF to remove them from this section.
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
                <form action="{{ route('Superadmin.managesection.store') }}" method="POST">
                    @csrf
                    <!-- Hidden Section ID -->
                    <input type="hidden" name="section_id" :value="editSectionId">

                    <div class="px-6 py-5 space-y-4 max-h-[65vh] overflow-y-auto">
                        <!-- Quick Actions Bar -->
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200/70">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                                All Components Status
                            </span>
                            <div class="flex items-center gap-2">
                                <button 
                                    type="button" 
                                    @click="toggleAll(true)"
                                    class="px-3 py-1 rounded-lg text-xs font-bold text-blue-600 bg-white hover:bg-blue-50 border border-slate-200 transition-colors cursor-pointer"
                                >
                                    Enable All
                                </button>
                                <button 
                                    type="button" 
                                    @click="toggleAll(false)"
                                    class="px-3 py-1 rounded-lg text-xs font-bold text-slate-600 bg-white hover:bg-slate-100 border border-slate-200 transition-colors cursor-pointer"
                                >
                                    Disable All
                                </button>
                            </div>
                        </div>

                        <!-- Components Toggle Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            @foreach($components as $component)
                                <div 
                                    class="p-3.5 rounded-xl border transition-all duration-200 flex flex-col justify-between gap-3 cursor-pointer select-none"
                                    :class="activeComponents[{{ $component->id }}] ? 'border-blue-500 bg-blue-50/50 ring-1 ring-blue-500/20' : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/40'"
                                    @click="activeComponents[{{ $component->id }}] = !activeComponents[{{ $component->id }}]"
                                >
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="flex items-center gap-2.5 min-w-0 pointer-events-none">
                                            <div 
                                                class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 transition-colors duration-200"
                                                :class="activeComponents[{{ $component->id }}] ? 'bg-blue-600 text-white shadow-xs shadow-blue-500/30' : 'bg-slate-100 text-slate-500'"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                                </svg>
                                            </div>
                                            <div class="truncate">
                                                <div class="flex items-center gap-1.5">
                                                    <h4 class="text-xs font-bold text-slate-900 truncate">
                                                        {{ $component->component_name }}
                                                    </h4>
                                                    <span 
                                                        class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider transition-colors duration-200"
                                                        :class="activeComponents[{{ $component->id }}] ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-500'"
                                                        x-text="activeComponents[{{ $component->id }}] ? 'Active' : 'Inactive'"
                                                    >Inactive</span>
                                                </div>
                                                <p class="text-[11px] text-slate-400 font-mono truncate mt-0.5">
                                                    /{{ $component->component_slug }}
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Toggle Switch Button -->
                                        <div class="shrink-0 flex items-center">
                                            <input 
                                                type="hidden" 
                                                name="components[{{ $component->id }}]" 
                                                :value="activeComponents[{{ $component->id }}] ? '1' : '0'"
                                            >
                                            <button 
                                                type="button" 
                                                @click.stop="activeComponents[{{ $component->id }}] = !activeComponents[{{ $component->id }}]"
                                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                                :class="activeComponents[{{ $component->id }}] ? 'bg-blue-600' : 'bg-slate-300'"
                                                role="switch" 
                                                :aria-checked="activeComponents[{{ $component->id }}]"
                                            >
                                                <span 
                                                    aria-hidden="true" 
                                                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                                    :class="activeComponents[{{ $component->id }}] ? 'translate-x-5' : 'translate-x-0'"
                                                ></span>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Allow Multiple Checkbox (When Active) -->
                                    <div 
                                        x-show="activeComponents[{{ $component->id }}]" 
                                        x-transition 
                                        @click.stop
                                        class="pt-2 border-t border-blue-200/60 flex items-center justify-between"
                                    >
                                        <input type="hidden" name="is_multiple[{{ $component->id }}]" :value="multipleComponents[{{ $component->id }}] ? '1' : '0'">
                                        <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-slate-700">
                                            <input 
                                                type="checkbox" 
                                                x-model="multipleComponents[{{ $component->id }}]"
                                                class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 w-4 h-4 cursor-pointer"
                                            >
                                            <span>Allow Multiple (Repeater)</span>
                                        </label>
                                        <span class="text-[10px] text-blue-600 font-semibold bg-blue-100/60 px-2 py-0.5 rounded-full" x-show="multipleComponents[{{ $component->id }}]">
                                            + Add Enabled
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2.5">
                        <button 
                            type="button" 
                            @click="openModal = false"
                            class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-800 hover:bg-slate-200/60 transition-colors cursor-pointer"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm hover:shadow cursor-pointer flex items-center gap-1.5"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Save Changes</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Assigned Components Modal -->
    <div 
        x-show="openViewModal" 
        x-cloak
        style="display: none;"
        class="fixed inset-0 z-50 overflow-y-auto"
        role="dialog" 
        aria-modal="true"
        @keydown.escape.window="openViewModal = false"
    >
        <!-- Modal Backdrop -->
        <div 
            x-show="openViewModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
            @click="openViewModal = false"
        ></div>

        <!-- Modal Dialog Placement -->
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div 
                x-show="openViewModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                @click.away="openViewModal = false"
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl border border-slate-200/90"
            >
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 bg-slate-50/70">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900" x-text="viewSectionName ? 'Assigned Components: ' + viewSectionName : 'Assigned Components'">
                                Assigned Components
                            </h3>
                            <p class="text-xs text-slate-400 font-mono" x-show="viewSectionSlug" x-text="'/' + viewSectionSlug"></p>
                        </div>
                    </div>
                    <button 
                        @click="openViewModal = false" 
                        type="button" 
                        class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer"
                        aria-label="Close"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="px-6 py-5 max-h-[65vh] overflow-y-auto space-y-4">

                    {{-- Branch A: Has Subsections --}}
                    <template x-if="viewIsSubsection">
                        <div class="space-y-4">
                            <template x-if="viewSubsections.length === 0">
                                <div class="py-10 text-center flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-bold text-slate-700">No subsections defined</p>
                                    <p class="text-xs text-slate-400 mt-1">Add subsections from the Section management page first.</p>
                                </div>
                            </template>

                            <template x-if="viewSubsections.length > 0">
                                <div class="space-y-3">
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-100 pb-2">Subsections &amp; Their Components</p>
                                    <template x-for="(sub, si) in viewSubsections" :key="sub.id">
                                        <div class="rounded-xl border border-slate-200 overflow-hidden">
                                            <!-- Subsection Header -->
                                            <div class="flex items-center justify-between px-4 py-3 bg-slate-50 border-b border-slate-200">
                                                <div class="flex items-center gap-2">
                                                    <span class="w-6 h-6 rounded-md bg-blue-100 text-blue-700 flex items-center justify-center text-[11px] font-bold shrink-0" x-text="si + 1"></span>
                                                    <div>
                                                        <h5 class="text-sm font-bold text-slate-900" x-text="sub.subsection_name"></h5>
                                                        <p class="text-[11px] text-slate-400 font-mono" x-text="sub.subsection_slug ? '/' + sub.subsection_slug : ''"></p>
                                                    </div>
                                                </div>
                                                <!-- + button to assign components -->
                                                <button
                                                    type="button"
                                                    @click="openSubCompAssign(sub)"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 hover:border-blue-300 transition-all cursor-pointer"
                                                    title="Assign components to this subsection"
                                                >
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                                    </svg>
                                                    Add
                                                </button>
                                            </div>
                                            <!-- Subsection's components -->
                                            <div class="divide-y divide-slate-100">
                                                <template x-if="!sub.components || sub.components.length === 0">
                                                    <p class="px-4 py-3 text-xs text-slate-400 italic">No components assigned yet. Click <strong>Add</strong> to assign.</p>
                                                </template>
                                                <template x-for="(comp, ci) in sub.components" :key="comp.id">
                                                    <div class="flex items-center justify-between px-4 py-2.5 bg-white hover:bg-slate-50/60 transition-colors">
                                                        <div class="flex items-center gap-2.5">
                                                            <span class="w-5 h-5 rounded bg-slate-100 text-slate-500 flex items-center justify-center text-[10px] font-bold shrink-0" x-text="ci + 1"></span>
                                                            <div>
                                                                <h6 class="text-xs font-bold text-slate-800" x-text="comp.component_name"></h6>
                                                                <p class="text-[10px] text-slate-400 font-mono" x-text="comp.component_slug ? '/' + comp.component_slug : ''"></p>
                                                            </div>
                                                        </div>
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                            <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                                                            Active
                                                        </span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>

                    {{-- Branch B: No Subsections — show flat component list --}}
                    <template x-if="!viewIsSubsection">
                        <div>
                            <template x-if="viewComponents.length === 0">
                                <div class="py-10 text-center flex flex-col items-center justify-center">
                                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-bold text-slate-700">No components assigned</p>
                                    <p class="text-xs text-slate-400 mt-1 max-w-xs">There are no active components assigned to this section yet.</p>
                                </div>
                            </template>

                            <template x-if="viewComponents.length > 0">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between pb-2 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                        <span>Assigned Components List</span>
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-mono text-[11px]" x-text="viewComponents.length + ' Active'"></span>
                                    </div>
                                    <div class="divide-y divide-slate-100 rounded-xl border border-slate-200/80 overflow-hidden">
                                        <template x-for="(comp, idx) in viewComponents" :key="comp.id">
                                            <div class="px-4 py-3 bg-white hover:bg-slate-50/70 transition-colors">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center gap-3">
                                                        <span class="w-6 h-6 rounded-md bg-slate-100 text-slate-500 flex items-center justify-center text-xs font-bold shrink-0" x-text="idx + 1"></span>
                                                        <div>
                                                            <div class="flex items-center gap-2">
                                                                <h5 class="text-sm font-bold text-slate-900" x-text="comp.component_name"></h5>
                                                                <template x-if="comp.is_subcomponent">
                                                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">Container</span>
                                                                </template>
                                                            </div>
                                                            <p class="text-xs text-slate-400 font-mono" x-text="'/' + comp.component_slug"></p>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <template x-if="comp.is_subcomponent">
                                                            <button
                                                                type="button"
                                                                @click="openCardSubCompAssign(viewSectionId, comp)"
                                                                class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 hover:border-indigo-300 transition-all cursor-pointer"
                                                                title="Configure Subcomponents for this section"
                                                            >
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                                                </svg>
                                                                Subcomponents
                                                            </button>
                                                        </template>
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                            Active
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end">
                    <button
                        type="button"
                        @click="openViewModal = false"
                        class="px-5 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-100 transition-all cursor-pointer shadow-2xs"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>


    {{-- Assign Components to Subsection Modal --}}
    <div
        x-show="openSubCompModal"
        x-cloak
        style="display: none;"
        class="fixed inset-0 z-[60] overflow-y-auto"
        role="dialog"
        aria-modal="true"
        @keydown.escape.window="openSubCompModal = false"
    >
        <!-- Backdrop -->
        <div
            x-show="openSubCompModal"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-900/70 backdrop-blur-xs transition-opacity"
            @click="openSubCompModal = false"
        ></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div
                x-show="openSubCompModal"
                x-transition:enter="ease-out duration-250"
                x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                @click.away="openSubCompModal = false"
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200/90"
            >
                <!-- Header -->
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 bg-slate-50/70">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900" x-text="currentSubsection ? 'Assign Components: ' + currentSubsection.subsection_name : 'Assign Components'">Assign Components</h3>
                            <p class="text-xs text-slate-400 font-mono" x-show="currentSubsection && currentSubsection.subsection_slug" x-text="'/' + (currentSubsection ? currentSubsection.subsection_slug : '')"></p>
                        </div>
                    </div>
                    <button @click="openSubCompModal = false" type="button" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer" aria-label="Close">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Body: All components as toggleable rows -->
                <div class="px-6 py-5 max-h-[55vh] overflow-y-auto">
                    <p class="text-xs text-slate-500 mb-3">Select the components to assign to this subsection:</p>
                    <div class="divide-y divide-slate-100 rounded-xl border border-slate-200/80 overflow-hidden">
                        @foreach($components as $comp)
                        <label
                            for="sub_comp_{{ $comp->id }}"
                            class="flex items-center gap-3 px-4 py-3 bg-white hover:bg-slate-50/70 transition-colors cursor-pointer"
                        >
                            <input
                                type="checkbox"
                                id="sub_comp_{{ $comp->id }}"
                                x-model="activeSubComps[{{ $comp->id }}]"
                                class="w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500 cursor-pointer"
                            />
                            <div class="flex-1 min-w-0">
                                <span class="block text-sm font-bold text-slate-900">{{ $comp->component_name }}</span>
                                <span class="block text-xs text-slate-400 font-mono">/{{ $comp->component_slug }}</span>
                            </div>
                            <template x-if="activeSubComps[{{ $comp->id }}]">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Selected
                                </span>
                            </template>
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button
                        type="button"
                        @click="openSubCompModal = false"
                        class="px-5 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-100 transition-all cursor-pointer shadow-2xs"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        @click="saveSubComps()"
                        class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 border border-blue-600 hover:border-blue-700 transition-all cursor-pointer shadow-sm shadow-blue-500/20"
                    >
                        Save Components
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Assign Subcomponents to Component (e.g. Card) for a Section Modal --}}
    <div
        x-show="openCardModal"
        x-cloak
        style="display: none;"
        class="fixed inset-0 z-[60] overflow-y-auto"
        role="dialog"
        aria-modal="true"
        @keydown.escape.window="openCardModal = false"
    >
        <div
            x-show="openCardModal"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-900/70 backdrop-blur-xs transition-opacity"
            @click="openCardModal = false"
        ></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div
                x-show="openCardModal"
                x-transition:enter="ease-out duration-250"
                x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 scale-95"
                @click.away="openCardModal = false"
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200/90"
            >
                <!-- Header -->
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 bg-slate-50/70">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">
                                Configure <span class="text-indigo-600" x-text="currentCardComponent ? currentCardComponent.component_name : 'Card'"></span> for <span class="text-slate-700" x-text="viewSectionName"></span>
                            </h3>
                            <p class="text-xs text-slate-400">Select which subcomponents are used in this section's card</p>
                        </div>
                    </div>
                    <button @click="openCardModal = false" type="button" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer" aria-label="Close">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Body -->
                <div class="px-6 py-5 max-h-[55vh] overflow-y-auto">
                    <template x-if="cardSubCompsLoading">
                        <div class="py-8 text-center text-xs text-slate-400">Loading subcomponents...</div>
                    </template>
                    <template x-if="!cardSubCompsLoading">
                        <div>
                            <p class="text-xs text-slate-500 mb-3">Choose the subcomponents for this specific section:</p>
                            <div class="divide-y divide-slate-100 rounded-xl border border-slate-200/80 overflow-hidden">
                                @foreach($components as $subComp)
                                    <template x-if="!currentCardComponent || currentCardComponent.id !== {{ $subComp->id }}">
                                        <label
                                            for="card_sub_{{ $subComp->id }}"
                                            class="flex items-center gap-3 px-4 py-3 bg-white hover:bg-slate-50/70 transition-colors cursor-pointer"
                                        >
                                            <input
                                                type="checkbox"
                                                id="card_sub_{{ $subComp->id }}"
                                                x-model="activeCardSubComps[{{ $subComp->id }}]"
                                                class="w-4 h-4 rounded text-indigo-600 border-slate-300 focus:ring-indigo-500 cursor-pointer"
                                            />
                                            <div class="flex-1 min-w-0">
                                                <span class="block text-sm font-bold text-slate-900">{{ $subComp->component_name }}</span>
                                                <span class="block text-xs text-slate-400 font-mono">/{{ $subComp->component_slug }}</span>
                                            </div>
                                            <template x-if="activeCardSubComps[{{ $subComp->id }}]">
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                                    Selected
                                                </span>
                                            </template>
                                        </label>
                                    </template>
                                @endforeach
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
                    <button
                        type="button"
                        @click="openCardModal = false"
                        class="px-5 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-100 transition-all cursor-pointer shadow-2xs"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        @click="saveCardSubComps()"
                        class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 border border-indigo-600 hover:border-indigo-700 transition-all cursor-pointer shadow-sm shadow-indigo-500/20"
                    >
                        Save Subcomponents
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function manageSectionApp() {
    return {
        openModal: false,
        openViewModal: false,
        openSubCompModal: false,
        openCardModal: false,
        viewSectionId: null,
        currentCardSectionId: null,
        currentCardComponent: null,
        activeCardSubComps: {},
        cardSubCompsLoading: false,
        editSectionId: null,
        editSectionName: '',
        editSectionTitle: '',
        viewSectionName: '',
        viewSectionSlug: '',
        viewIsSubsection: false,
        viewSubsections: [],
        viewComponents: [],
        activeSubComps: {},
        currentSubsection: null,
        sectionsData: @json($sections->load(['subsections.components', 'components.subcomponents'])->keyBy('id')),
        mappings: @json($sectionComponentsMap ?? []),
        multipleMappings: @json($sectionComponentsMultipleMap ?? []),
        activeComponents: {},
        multipleComponents: {},
        openView(sectionId) {
            let section = this.sectionsData[sectionId];
            if (!section) return;
            this.viewSectionId = section.id || null;
            this.viewSectionName = section.section_name || '';
            this.viewSectionSlug = section.section_slug || '';
            this.viewIsSubsection = section.is_subsection ? true : false;
            if (this.viewIsSubsection) {
                this.viewSubsections = section.subsections || [];
                this.viewComponents = [];
            } else {
                this.viewSubsections = [];
                this.viewComponents = (section.components || []).filter(c => c.pivot && c.pivot.status == 1);
            }
            this.openViewModal = true;
        },
        openCardSubCompAssign(sectionId, comp) {
            this.currentCardSectionId = sectionId;
            this.currentCardComponent = comp;
            this.activeCardSubComps = {};
            this.cardSubCompsLoading = true;
            this.openCardModal = true;
            fetch('/sections/' + sectionId + '/components/' + comp.id + '/subcomponents')
                .then(r => r.json())
                .then(data => {
                    this.activeCardSubComps = {};
                    if (data && data.length > 0) {
                        data.forEach(c => { this.activeCardSubComps[c.id] = true; });
                    } else if (comp.subcomponents && comp.subcomponents.length > 0) {
                        comp.subcomponents.forEach(c => { this.activeCardSubComps[c.id] = true; });
                    }
                    this.cardSubCompsLoading = false;
                })
                .catch(() => { this.cardSubCompsLoading = false; });
        },
        saveCardSubComps() {
            let secId = this.currentCardSectionId;
            let compId = this.currentCardComponent.id;
            let ids = Object.keys(this.activeCardSubComps).filter(k => this.activeCardSubComps[k]);
            let form = new FormData();
            ids.forEach(id => form.append('sub_component_ids[]', id));
            form.append('_token', document.querySelector('meta[name=csrf-token]').content);
            fetch('/sections/' + secId + '/components/' + compId + '/subcomponents', { method: 'POST', body: form })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        this.openCardModal = false;
                    }
                });
        },
        openSubCompAssign(subsection) {
            this.currentSubsection = subsection;
            this.activeSubComps = {};
            let assigned = subsection.components || [];
            assigned.forEach(c => { this.activeSubComps[c.id] = true; });
            this.openSubCompModal = true;
        },
        saveSubComps() {
            let subsectionId = this.currentSubsection.id;
            let ids = Object.keys(this.activeSubComps).filter(k => this.activeSubComps[k]);
            let form = new FormData();
            ids.forEach(id => form.append('component_ids[]', id));
            form.append('_token', document.querySelector('meta[name=csrf-token]').content);
            fetch('/subsections/' + subsectionId + '/components', { method: 'POST', body: form })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        // Update local subsection components so view refreshes
                        let sub = this.viewSubsections.find(s => s.id === subsectionId);
                        if (sub) sub.components = data.components;
                        this.currentSubsection.components = data.components;
                        this.openSubCompModal = false;
                    }
                });
        },
        openEdit(sectionId, sectionName, sectionTitle) {
            this.editSectionId = sectionId;
            this.editSectionName = sectionName;
            this.editSectionTitle = sectionTitle;
            let secMap = this.mappings[sectionId] || {};
            let mulMap = this.multipleMappings[sectionId] || {};
            @foreach($components as $comp)
                this.activeComponents[{{ $comp->id }}] = secMap[{{ $comp->id }}] !== undefined ? Boolean(secMap[{{ $comp->id }}]) : false;
                this.multipleComponents[{{ $comp->id }}] = mulMap[{{ $comp->id }}] !== undefined ? Boolean(mulMap[{{ $comp->id }}]) : {{ $comp->is_multiple ? 'true' : 'false' }};
            @endforeach
            this.openModal = true;
        },
        toggleAll(state) {
            @foreach($components as $comp)
                this.activeComponents[{{ $comp->id }}] = state;
            @endforeach
        }
    };
}
document.addEventListener('alpine:init', () => {
    Alpine.data('manageSectionApp', manageSectionApp);
});
</script>
@endpush

