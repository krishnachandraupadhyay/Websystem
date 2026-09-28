@extends('superadmin.layout')

@section('title', 'Assign Sections to Admin')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{
    openModal: false,
    editAdminId: null,
    editAdminName: '',
    mappings: {{ json_encode($adminSectionsMap ?? []) }},
    activeSections: {},

    // Section Components Modal State
    openCompModal: false,
    editSectionId: null,
    editSectionName: '',
    editSectionSlug: '',
    secCompMappings: {{ json_encode($sectionComponentsMap ?? []) }},
    activeComponents: {},

    openSectionComponents(secId, secName, secSlug) {
        this.editSectionId = secId;
        this.editSectionName = secName;
        this.editSectionSlug = secSlug;
        let secMap = this.secCompMappings[secId] || {};
        @foreach($components as $comp)
            this.activeComponents[{{ $comp->id }}] = secMap[{{ $comp->id }}] !== undefined ? Boolean(secMap[{{ $comp->id }}]) : false;
        @endforeach
        this.openCompModal = true;
    },

    toggleAllComponents(state) {
        @foreach($components as $comp)
            this.activeComponents[{{ $comp->id }}] = state;
        @endforeach
    },

    countActiveComponents() {
        return Object.values(this.activeComponents).filter(Boolean).length;
    },

    openEdit(adminId, adminName) {
        this.editAdminId = adminId;
        this.editAdminName = adminName;
        let adminMap = this.mappings[adminId] || {};
        @foreach($sections as $sec)
            this.activeSections[{{ $sec->id }}] = adminMap[{{ $sec->id }}] !== undefined ? Boolean(adminMap[{{ $sec->id }}]) : false;
        @endforeach
        this.openModal = true;
    },
    toggleAll(state) {
        @foreach($sections as $sec)
            this.activeSections[{{ $sec->id }}] = state;
        @endforeach
    }
}">


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
    @if(session('error') || (isset($errors) && $errors->any()))
        @php
            $errorMsgs = [];
            if (session('error')) { $errorMsgs[] = session('error'); }
            if (isset($errors) && $errors->any()) { $errorMsgs = array_merge($errorMsgs, $errors->all()); }
            $uniqueErrors = array_unique($errorMsgs);
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
                            @foreach($uniqueErrors as $err)
                                <li>{{ $err }}</li>
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

    <!-- Admin Wise Sections Table Card -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-2.5">
                <h2 class="text-base font-bold text-slate-800">All Admins & Their Assigned Sections</h2>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-500 font-semibold font-mono">{{ $admins->count() }}</span>
            </div>
            <a 
                href="{{ route('Superadmin.manageadmin') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-sm shadow-blue-500/20 cursor-pointer flex items-center gap-1.5"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Add Admin</span>
            </a>
        </div>

        @if($admins->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-200 py-16 flex flex-col items-center justify-center text-center">
                <p class="text-sm font-semibold text-slate-500">No administrators found.</p>
                <a href="{{ route('Superadmin.manageadmin') }}" class="mt-2 text-xs text-blue-600 font-bold hover:underline">
                    + Create First Admin
                </a>
            </div>
        @else
            <div class="overflow-x-auto rounded-xl border border-slate-200/80">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-[11px] font-bold tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">#</th>
                            <th class="px-5 py-3.5">Admin</th>
                            <th class="px-5 py-3.5">Assigned Sections</th>
                            <th class="px-5 py-3.5 text-center">Active Count</th>
                            <th class="px-5 py-3.5">Role</th>
                            <th class="px-5 py-3.5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($admins as $index => $admin)
                            @php
                                $activeSections = $admin->sections->where('pivot.status', 1);
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-5 py-4 text-xs font-semibold text-slate-400">
                                    {{ $index + 1 }}
                                </td>

                                <!-- Admin Name & Email -->
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs shrink-0">
                                            {{ strtoupper(substr($admin->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-slate-900 text-sm">
                                                {{ $admin->name }}
                                            </h4>
                                            <p class="text-xs text-slate-400">
                                                {{ $admin->email }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Assigned Sections Pills -->
                                <td class="px-5 py-4">
                                    @if($activeSections->isNotEmpty())
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach($activeSections as $sec)
                                                @php
                                                    $secActiveCount = $sec->components()->wherePivot('status', 1)->count();
                                                @endphp
                                                <button 
                                                    type="button"
                                                    @click="openSectionComponents({{ $sec->id }}, '{{ addslashes($sec->section_name) }}', '{{ addslashes($sec->section_slug) }}')"
                                                    class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200/80 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all cursor-pointer shadow-2xs group/pill"
                                                    title="Click to view & configure components for {{ $sec->section_name }}"
                                                >
                                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 group-hover/pill:bg-white transition-colors"></span>
                                                    <span>{{ $sec->section_name }}</span>
                                                    <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-blue-100 text-blue-800 group-hover/pill:bg-white/20 group-hover/pill:text-white font-mono transition-colors">
                                                        {{ $secActiveCount }}
                                                    </span>
                                                </button>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-xs text-slate-400 italic">
                                            <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                            </svg>
                                            No sections assigned yet
                                        </span>
                                    @endif
                                </td>

                                <!-- Active Count Badge -->
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $activeSections->count() > 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500' }}">
                                        {{ $activeSections->count() }} Active
                                    </span>
                                </td>

                                <!-- Role -->
                                <td class="px-5 py-4">
                                    @if($admin->role === 'Super Admin')
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                            ★ Super Admin
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                            Admin
                                        </span>
                                    @endif
                                </td>

                                <!-- Action (Edit Sections Button) -->
                                <td class="px-5 py-4 text-right">
                                    <button 
                                        type="button" 
                                        @click="openEdit({{ $admin->id }}, '{{ addslashes($admin->name) }}')"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-blue-600 hover:text-blue-700 bg-white hover:bg-blue-50 border border-slate-200 hover:border-blue-300 transition-all cursor-pointer shadow-2xs"
                                        title="Assign or remove sections for this admin"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Edit Modal Dialog for Assigning / Removing Sections -->
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900" id="modal-title">
                                Assign Sections: <span class="text-blue-600" x-text="editAdminName"></span>
                            </h3>
                            <p class="text-xs text-slate-400 mt-0.5">
                                Toggle ON to assign sections, or toggle OFF to remove them for this admin.
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
                <form action="{{ route('Superadmin.assignsection.store') }}" method="POST">
                    @csrf
                    <!-- Hidden Admin ID -->
                    <input type="hidden" name="admin_id" :value="editAdminId">

                    <div class="px-6 py-5 space-y-4 max-h-[65vh] overflow-y-auto">
                        <!-- Quick Actions Bar -->
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200/70">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                                All Sections Status
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

                        <!-- Sections Toggle Grid -->
                        @if($sections->isEmpty())
                            <div class="p-6 rounded-xl border border-dashed border-slate-200 text-center text-slate-400 text-sm">
                                No sections available yet. Please add sections in <a href="{{ route('Superadmin.addsection') }}" class="text-blue-600 font-bold hover:underline">Add Section</a>.
                            </div>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                @foreach($sections as $sec)
                                    <div 
                                        class="p-3.5 rounded-xl border transition-all duration-200 flex items-center justify-between gap-3 cursor-pointer select-none"
                                        :class="activeSections[{{ $sec->id }}] ? 'border-blue-500 bg-blue-50/50 ring-1 ring-blue-500/20' : 'border-slate-200 bg-white hover:border-slate-300 hover:bg-slate-50/40'"
                                        @click="activeSections[{{ $sec->id }}] = !activeSections[{{ $sec->id }}]"
                                    >
                                        <div class="flex items-center gap-2.5 min-w-0 pointer-events-none">
                                            <div 
                                                class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 transition-colors duration-200"
                                                :class="activeSections[{{ $sec->id }}] ? 'bg-blue-600 text-white shadow-xs shadow-blue-500/30' : 'bg-slate-100 text-slate-500'"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                                </svg>
                                            </div>
                                            <div class="truncate">
                                                <div class="flex items-center gap-1.5">
                                                    <h4 class="text-xs font-bold text-slate-900 truncate">
                                                        {{ $sec->section_name }}
                                                    </h4>
                                                    <span 
                                                        class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider transition-colors duration-200"
                                                        :class="activeSections[{{ $sec->id }}] ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-500'"
                                                        x-text="activeSections[{{ $sec->id }}] ? 'Active' : 'Inactive'"
                                                    >Inactive</span>
                                                </div>
                                                <p class="text-[11px] text-slate-400 font-mono truncate mt-0.5">
                                                    /{{ $sec->section_slug }}
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Toggle Switch Button -->
                                        <div class="shrink-0 flex items-center">
                                            <input 
                                                type="hidden" 
                                                name="sections[{{ $sec->id }}]" 
                                                :value="activeSections[{{ $sec->id }}] ? '1' : '0'"
                                            >
                                            <button 
                                                type="button" 
                                                @click.stop="activeSections[{{ $sec->id }}] = !activeSections[{{ $sec->id }}]"
                                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                                :class="activeSections[{{ $sec->id }}] ? 'bg-blue-600' : 'bg-slate-300'"
                                                role="switch" 
                                                :aria-checked="activeSections[{{ $sec->id }}]"
                                            >
                                                <span 
                                                    aria-hidden="true" 
                                                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                                    :class="activeSections[{{ $sec->id }}] ? 'translate-x-5' : 'translate-x-0'"
                                                ></span>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
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

    <!-- ======================================================== -->
    <!-- 2. SECTION COMPONENTS MODAL (Opens on clicking Section Pill) -->
    <!-- ======================================================== -->
    <div 
        x-show="openCompModal" 
        class="fixed inset-0 z-50 overflow-y-auto" 
        aria-labelledby="comp-modal-title" 
        role="dialog" 
        aria-modal="true"
        style="display: none;"
    >
        <!-- Backdrop -->
        <div 
            x-show="openCompModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="openCompModal = false"
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
        ></div>

        <!-- Modal Dialog Placement -->
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div 
                x-show="openCompModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                @click.away="openCompModal = false"
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-slate-200/90"
            >
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 bg-slate-50/70">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H5a1 1 0 01-1-1v-4zM14 15a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900" id="comp-modal-title">
                                Components for Section: <span class="text-blue-600 font-extrabold" x-text="editSectionName"></span>
                            </h3>
                            <p class="text-xs text-slate-400 mt-0.5">
                                All components are listed below. Active components are marked ON, inactive are marked OFF.
                            </p>
                        </div>
                    </div>
                    <button 
                        @click="openCompModal = false" 
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
                    <!-- Hidden Section ID & Redirect Target -->
                    <input type="hidden" name="section_id" :value="editSectionId">
                    <input type="hidden" name="redirect_to" value="assignsection">

                    <div class="px-6 py-5 space-y-4 max-h-[65vh] overflow-y-auto">
                        <!-- Quick Actions Bar & Status Summary -->
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200/70">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Components Status
                                </span>
                                <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-blue-100 text-blue-700">
                                    <span x-text="countActiveComponents()"></span> / {{ $components->count() }} Active
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button 
                                    type="button" 
                                    @click="toggleAllComponents(true)"
                                    class="px-3 py-1 rounded-lg text-xs font-bold text-blue-600 bg-white hover:bg-blue-50 border border-slate-200 transition-colors cursor-pointer shadow-2xs"
                                >
                                    Enable All
                                </button>
                                <button 
                                    type="button" 
                                    @click="toggleAllComponents(false)"
                                    class="px-3 py-1 rounded-lg text-xs font-bold text-slate-600 bg-white hover:bg-slate-100 border border-slate-200 transition-colors cursor-pointer shadow-2xs"
                                >
                                    Disable All
                                </button>
                            </div>
                        </div>

                        <!-- Components Items List -->
                        <div class="divide-y divide-slate-100 border border-slate-200/80 rounded-xl overflow-hidden bg-white">
                            @forelse($components as $comp)
                                <div 
                                    class="flex items-center justify-between p-3.5 hover:bg-slate-50/70 transition-colors cursor-pointer"
                                    @click="activeComponents[{{ $comp->id }}] = !activeComponents[{{ $comp->id }}]"
                                >
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs shrink-0">
                                            {{ strtoupper(substr($comp->component_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <h4 class="text-xs font-bold text-slate-800">
                                                    {{ $comp->component_name }}
                                                </h4>
                                                <span 
                                                    x-show="activeComponents[{{ $comp->id }}]" 
                                                    class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"
                                                >
                                                    <span class="w-1 h-1 rounded-full bg-emerald-500"></span>
                                                    Active
                                                </span>
                                                <span 
                                                    x-show="!activeComponents[{{ $comp->id }}]" 
                                                    class="inline-flex items-center gap-1 px-1.5 py-0.2 rounded-full text-[10px] font-bold bg-slate-100 text-slate-400 border border-slate-200"
                                                >
                                                    Inactive
                                                </span>
                                            </div>
                                            <p class="text-[11px] text-slate-400 font-mono">
                                                slug: {{ $comp->component_slug }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Switch Toggle with Hidden Input -->
                                    <div class="flex items-center" @click.stop>
                                        <input type="hidden" name="components[{{ $comp->id }}]" :value="activeComponents[{{ $comp->id }}] ? '1' : '0'">
                                        <button 
                                            type="button" 
                                            @click.stop="activeComponents[{{ $comp->id }}] = !activeComponents[{{ $comp->id }}]"
                                            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                                            :class="activeComponents[{{ $comp->id }}] ? 'bg-blue-600' : 'bg-slate-300'"
                                            role="switch"
                                            :aria-checked="activeComponents[{{ $comp->id }}] ? 'true' : 'false'"
                                        >
                                            <span 
                                                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out"
                                                :class="activeComponents[{{ $comp->id }}] ? 'translate-x-5' : 'translate-x-0'"
                                            ></span>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center text-slate-400 text-xs">
                                    No components available in system.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Modal Actions Footer -->
                    <div class="border-t border-slate-100 px-6 py-4 bg-slate-50 flex items-center justify-end gap-3 rounded-b-2xl">
                        <button 
                            @click="openCompModal = false" 
                            type="button" 
                            class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 text-xs font-bold hover:bg-white hover:text-slate-800 transition-colors cursor-pointer"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-all shadow-sm shadow-blue-500/20 cursor-pointer flex items-center gap-1.5"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Save Components</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
