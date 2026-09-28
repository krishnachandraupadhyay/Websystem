@extends('superadmin.layout')

@section('title', 'Section Management')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{ 
    openModal: {{ (isset($errors) && $errors->any()) ? 'true' : 'false' }}, 
    isEdit: false,
    formAction: '{{ route('sections.store') }}',
    sectionName: '{{ old('section_name', '') }}', 
    sectionTitle: '{{ old('section_title', '') }}',
    sectionSlug: '{{ old('section_slug', '') }}',
    description: '{{ old('description', '') }}',
    status: '{{ old('status', '1') }}',
    isSubsection: {{ old('is_subsection', 0) ? 'true' : 'false' }},
    subsections: [],
    openSubModal: false,
    activeManageSection: null,
    manageSubAction: '',
    manageSubsectionsList: [],
    openViewModal: false,
    viewParentSection: null,
    viewSectionName: '',
    viewSectionSlug: '',
    viewIsSubsectionEnabled: false,
    viewSubsectionsList: [],
    toggleSubsection() {
        if (!this.isSubsection) {
            let confirmed = confirm('Do you want to use multiple subsections (like carousel/slides) inside this section?');
            if (confirmed) {
                this.isSubsection = true;
                if (this.subsections.length === 0) {
                    this.addSubsectionRow();
                }
            } else {
                this.isSubsection = false;
            }
        } else {
            this.isSubsection = false;
        }
    },
    addSubsectionRow() {
        let nextNum = this.subsections.length + 1;
        let baseName = this.sectionName ? (this.sectionName + ' Item ' + nextNum) : ('Slide ' + nextNum);
        this.subsections.push({
            id: null,
            subsection_name: baseName,
            subsection_title: '',
            subsection_slug: this.sectionSlug ? (this.sectionSlug + '-item-' + nextNum) : ('item-' + nextNum),
            status: 1
        });
    },
    removeSubsectionRow(index) {
        this.subsections.splice(index, 1);
    },
    generateSlug() {
        if (!this.isEdit) {
            this.sectionSlug = this.sectionName.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
        }
    },
    openCreate() {
        this.isEdit = false;
        this.formAction = '{{ route('sections.store') }}';
        this.sectionName = '';
        this.sectionTitle = '';
        this.sectionSlug = '';
        this.description = '';
        this.status = '1';
        this.isSubsection = false;
        this.subsections = [];
        this.openModal = true;
    },
    openEdit(item) {
        this.isEdit = true;
        this.formAction = '/sections/' + item.id;
        this.sectionName = item.section_name || '';
        this.sectionTitle = item.section_title || '';
        this.sectionSlug = item.section_slug || '';
        this.description = item.description || '';
        this.status = (item.status == 1 || item.status === true || item.status === '1') ? '1' : '0';
        this.isSubsection = Boolean(item.is_subsection);
        this.subsections = (item.subsections || []).map(s => ({
            id: s.id,
            subsection_name: s.subsection_name,
            subsection_title: s.subsection_title || '',
            subsection_slug: s.subsection_slug || '',
            status: s.status ? 1 : 0
        }));
        this.openModal = true;
    },
    openManageSubsections(section) {
        this.activeManageSection = section;
        this.manageSubAction = '/sections/' + section.id + '/subsections';
        this.manageSubsectionsList = (section.subsections || []).map(s => ({
            id: s.id,
            subsection_name: s.subsection_name,
            subsection_title: s.subsection_title || '',
            subsection_slug: s.subsection_slug || '',
            status: s.status ? 1 : 0
        }));
        if (this.manageSubsectionsList.length === 0) {
            this.addManageSubRow();
        }
        this.openSubModal = true;
    },
    addManageSubRow() {
        let nextNum = this.manageSubsectionsList.length + 1;
        let sName = this.activeManageSection ? this.activeManageSection.section_name : 'Section';
        let sSlug = this.activeManageSection ? this.activeManageSection.section_slug : 'section';
        this.manageSubsectionsList.push({
            id: null,
            subsection_name: sName + ' Slide ' + nextNum,
            subsection_title: '',
            subsection_slug: sSlug + '-slide-' + nextNum,
            status: 1
        });
    },
    removeManageSubRow(index) {
        this.manageSubsectionsList.splice(index, 1);
    },
    openSubCompModal: false,
    currentSubsection: null,
    activeSubComps: {},
    openViewSubsections(section) {
        this.viewParentSection = section;
        this.viewSectionName = section.section_name || '';
        this.viewSectionSlug = section.section_slug || '';
        this.viewIsSubsectionEnabled = Boolean(section.is_subsection);
        this.viewSubsectionsList = section.subsections || [];
        this.openViewModal = true;
    },
    openSubCompAssign(subsection) {
        this.currentSubsection = subsection;
        this.activeSubComps = {};
        let assigned = subsection.components || [];
        assigned.forEach(c => {
            this.activeSubComps[c.id] = true;
        });
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
                    let sub = this.viewSubsectionsList.find(s => s.id === subsectionId);
                    if (sub) sub.components = data.components;
                    this.currentSubsection.components = data.components;
                    this.openSubCompModal = false;
                }
            });
    },
    toggleSubsectionStatus(sub, mode = 'toggle') {
        if (!sub || !sub.id) return;
        sub._loading = true;
        let form = new FormData();
        form.append('_token', document.querySelector('meta[name=csrf-token]').content);
        form.append('_method', 'PATCH');
        form.append('mode', mode);

        fetch('/subsections/' + sub.id + '/toggle-status', {
            method: 'POST',
            body: form,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            sub._loading = false;
            if (data.success) {
                if (mode === 'single') {
                    this.viewSubsectionsList.forEach(s => {
                        s.status = (s.id === sub.id) ? 1 : 0;
                    });
                } else {
                    sub.status = data.status ? 1 : 0;
                }
                if (this.viewParentSection) {
                    this.viewParentSection.subsections = data.subsections;
                }
            }
        })
        .catch(err => {
            sub._loading = false;
            console.error('Failed to toggle subsection:', err);
        });
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

    <!-- Main Content Container: Table or Empty State -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-2.5">
                <h2 class="text-base font-bold text-slate-800">All Website Sections</h2>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-500 font-semibold font-mono">{{ $sections->count() }}</span>
            </div>
            <button 
                @click="openCreate()" 
                type="button" 
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-sm shadow-blue-500/20 cursor-pointer flex items-center gap-1.5"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Add Section</span>
            </button>
        </div>

        @if($sections->isEmpty())
            <!-- Empty State Container -->
            <div class="rounded-2xl border border-blue-200/90 py-16 flex flex-col items-center justify-center bg-blue-50/20 text-center">
                <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center mb-3 shadow-2xs">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <h3 class="text-blue-600 text-lg font-bold">No Sections Found</h3>
                <p class="text-xs text-slate-400 mt-1 max-w-sm">
                    No sections have been created yet. Click the button below to add your first section.
                </p>
                <button 
                    @click="openCreate()" 
                    type="button" 
                    class="mt-4 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-all shadow-xs cursor-pointer"
                >
                    + Create First Section
                </button>
            </div>
        @else
            <!-- Sections Table -->
            <div class="overflow-x-auto rounded-xl border border-slate-200/80">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-[11px] font-bold tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">#</th>
                            <th class="px-5 py-3.5">Section Name</th>
                            <th class="px-5 py-3.5">Section Title</th>
                            <th class="px-5 py-3.5">Slug (URL)</th>
                            <th class="px-5 py-3.5">Description</th>
                            <th class="px-5 py-3.5">Status</th>
                            <th class="px-5 py-3.5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($sections as $index => $section)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-5 py-4 text-xs font-semibold text-slate-400">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-5 py-4 font-bold text-slate-900">
                                    <div class="flex items-center gap-1.5">
                                        <span>{{ $section->section_name }}</span>
                                        @if($section->is_subsection)
                                            <span class="text-amber-500 text-sm leading-none select-none cursor-default" title="Subsections ({{ $section->subsections->count() }})">★</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-slate-600 font-medium">
                                    {{ $section->section_title }}
                                </td>
                                <td class="px-5 py-4 text-xs font-mono text-slate-500">
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 border border-slate-200 text-slate-600">
                                        /{{ $section->section_slug }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-xs text-slate-500 max-w-xs truncate">
                                    {{ $section->description ?? '—' }}
                                </td>
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
                                <td class="px-5 py-4 text-right">
                                    <div class="inline-flex items-center gap-1.5 justify-end">
                                        <!-- View Subsections Button -->
                                        <button 
                                            type="button" 
                                            @click='openViewSubsections(@json($section))'
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-emerald-600 hover:text-emerald-700 bg-white hover:bg-emerald-50 border border-slate-200 hover:border-emerald-300 transition-all cursor-pointer shadow-2xs"
                                            title="View Subsections"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>

                                        @if($section->is_subsection)
                                            <!-- Add / Manage Subsections Button -->
                                            <button 
                                                type="button" 
                                                @click='openManageSubsections(@json($section))'
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-purple-600 hover:text-purple-700 bg-white hover:bg-purple-50 border border-slate-200 hover:border-purple-300 transition-all cursor-pointer shadow-2xs"
                                                title="Add / Configure Subsections"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                            </button>
                                        @endif

                                        <!-- Edit Section Button -->
                                        <button 
                                            @click='openEdit(@json($section))' 
                                            type="button" 
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-blue-600 hover:text-blue-700 bg-white hover:bg-blue-50 border border-slate-200 hover:border-blue-300 transition-all cursor-pointer shadow-2xs"
                                            title="Edit Section"
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

    <!-- Modal Dialog for Creating New Section -->
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
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200/90"
            >
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 bg-slate-50/70">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900" id="modal-title" x-text="isEdit ? 'Edit Section' : 'Create New Section'">
                            Create New Section
                        </h3>
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
                <form :action="formAction" method="POST">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>
                    <div class="px-6 py-5 space-y-4">
                        <!-- Section Name -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Section Name <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="section_name" 
                                x-model="sectionName"
                                @input="generateSlug()"
                                value="{{ old('section_name') }}"
                                placeholder="e.g. Hero Banner, Features, Testimonials" 
                                class="w-full px-3.5 py-2.5 rounded-xl border @error('section_name') border-rose-300 bg-rose-50/30 @else border-slate-300 @enderror text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all"
                                required
                            />
                            @error('section_name')
                                <p x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Section Title -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Section Title <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="section_title" 
                                x-model="sectionTitle"
                                value="{{ old('section_title') }}"
                                placeholder="e.g. Our Core Features & Advantages" 
                                class="w-full px-3.5 py-2.5 rounded-xl border @error('section_title') border-rose-300 bg-rose-50/30 @else border-slate-300 @enderror text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all"
                                required
                            />
                            @error('section_title')
                                <p x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Section Slug / URL -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Section Slug <span class="text-rose-500">*</span>
                            </label>
                            <div class="flex rounded-xl border @error('section_slug') border-rose-300 @else border-slate-300 @enderror overflow-hidden focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/20">
                                <span class="inline-flex items-center px-3 bg-slate-100 text-slate-500 text-xs font-mono border-r border-slate-200">
                                    /sections/
                                </span>
                                <input 
                                    type="text" 
                                    name="section_slug" 
                                    x-model="sectionSlug"
                                    value="{{ old('section_slug') }}"
                                    placeholder="hero-banner" 
                                    class="w-full px-3.5 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none"
                                    required
                                />
                            </div>
                            @error('section_slug')
                                <p x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Description
                            </label>
                            <textarea 
                                name="description" 
                                x-model="description"
                                rows="3"
                                placeholder="Enter section description or details (optional)..."
                                class="w-full px-3.5 py-2.5 rounded-xl border @error('description') border-rose-300 bg-rose-50/30 @else border-slate-300 @enderror text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all resize-none"
                            >{{ old('description') }}</textarea>
                            @error('description')
                                <p x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Status <span class="text-rose-500">*</span>
                            </label>
                            <select 
                                name="status" 
                                x-model="status"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all bg-white"
                                required
                            >
                                <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status') === '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                            @error('status')
                                <p x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Subsection Toggle & Dynamic Subsections Area -->
                        <div class="pt-2 border-t border-slate-200/80">
                            <div class="flex items-center justify-between p-3.5 rounded-xl bg-purple-50/50 border border-purple-200/80">
                                <div>
                                    <span class="block text-xs font-bold text-purple-900 uppercase tracking-wider">
                                        Enable Subsections
                                    </span>
                                    <span class="text-xs text-purple-600">
                                        Do you want to use multiple subsections (like carousel/slides) inside this section?
                                    </span>
                                </div>
                                <div class="flex items-center">
                                    <input type="hidden" name="is_subsection" :value="isSubsection ? '1' : '0'">
                                    <button 
                                        type="button" 
                                        @click="toggleSubsection()"
                                        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                        :class="isSubsection ? 'bg-purple-600' : 'bg-slate-300'"
                                        role="switch" 
                                        :aria-checked="isSubsection"
                                    >
                                        <span 
                                            aria-hidden="true" 
                                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                            :class="isSubsection ? 'translate-x-5' : 'translate-x-0'"
                                        ></span>
                                    </button>
                                </div>
                            </div>

                            <!-- Dynamic Subsection Rows when isSubsection is ON -->
                            <div x-show="isSubsection" x-transition class="mt-3.5 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                                        Subsections / Slides List (<span x-text="subsections.length"></span>)
                                    </span>
                                    <button 
                                        type="button" 
                                        @click="addSubsectionRow()"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold text-purple-700 bg-purple-100/80 hover:bg-purple-200 transition-colors cursor-pointer"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        <span>+ Add Subsection</span>
                                    </button>
                                </div>

                                <template x-if="subsections.length === 0">
                                    <div class="p-4 rounded-xl border border-dashed border-purple-300 bg-purple-50/20 text-center text-xs text-purple-600">
                                        No subsections added yet. Click "+ Add Subsection" to add your first slide/item.
                                    </div>
                                </template>

                                <div class="space-y-2.5 max-h-56 overflow-y-auto pr-1">
                                    <template x-for="(sub, index) in subsections" :key="index">
                                        <div class="p-3 rounded-xl border border-slate-200 bg-slate-50/60 flex items-start gap-2.5">
                                            <input type="hidden" :name="'subsections[' + index + '][id]'" :value="sub.id || ''">
                                            <input type="hidden" :name="'subsections[' + index + '][status]'" :value="sub.status">
                                            
                                            <span class="w-6 h-6 rounded-md bg-purple-100 text-purple-700 font-bold text-xs flex items-center justify-center shrink-0 mt-1" x-text="index + 1"></span>
                                            
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 grow">
                                                <div>
                                                    <input 
                                                        type="text" 
                                                        :name="'subsections[' + index + '][subsection_name]'" 
                                                        x-model="sub.subsection_name"
                                                        placeholder="Subsection Name (e.g. Slide 1)" 
                                                        class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-purple-500"
                                                        required
                                                    />
                                                </div>
                                                <div>
                                                    <input 
                                                        type="text" 
                                                        :name="'subsections[' + index + '][subsection_title]'" 
                                                        x-model="sub.subsection_title"
                                                        placeholder="Title / Heading (optional)" 
                                                        class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-purple-500"
                                                    />
                                                </div>
                                            </div>

                                            <button 
                                                type="button" 
                                                @click="removeSubsectionRow(index)"
                                                class="text-rose-500 hover:text-rose-700 p-1 rounded-lg hover:bg-rose-50 transition-colors cursor-pointer shrink-0 mt-1"
                                                title="Remove Subsection"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>
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
                            <span x-text="isEdit ? 'Update Section' : 'Save Section'">Save Section</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Manage Subsections Modal Dialog (Add / Edit / Remove Multiple Subsections) -->
    <div 
        x-show="openSubModal" 
        x-cloak
        style="display: none;"
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-sub-title" 
        role="dialog" 
        aria-modal="true"
        @keydown.escape.window="openSubModal = false"
    >
        <!-- Modal Backdrop -->
        <div 
            x-show="openSubModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
            @click="openSubModal = false"
        ></div>

        <!-- Modal Dialog Placement -->
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div 
                x-show="openSubModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                @click.away="openSubModal = false"
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-slate-200/90"
            >
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 bg-slate-50/70">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900" x-text="activeManageSection ? 'Manage Subsections: ' + activeManageSection.section_name : 'Manage Subsections'">
                                Manage Subsections
                            </h3>
                            <p class="text-xs text-slate-400 font-mono" x-show="activeManageSection" x-text="'/' + activeManageSection?.section_slug"></p>
                        </div>
                    </div>
                    <button 
                        @click="openSubModal = false" 
                        type="button" 
                        class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors cursor-pointer"
                        aria-label="Close"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Subsections Form -->
                <form :action="manageSubAction" method="POST">
                    @csrf
                    <div class="px-6 py-5 max-h-[60vh] overflow-y-auto space-y-4">
                        <div class="flex items-center justify-between">
                            <p class="text-xs text-slate-500">
                                Add or edit multiple subsections / slides for this section:
                            </p>
                            <button 
                                type="button" 
                                @click="addManageSubRow()"
                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold text-purple-700 bg-purple-50 hover:bg-purple-100 border border-purple-200 transition-colors cursor-pointer"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span>+ Add Another Subsection</span>
                            </button>
                        </div>

                        <template x-if="manageSubsectionsList.length === 0">
                            <div class="py-8 text-center border-2 border-dashed border-slate-200 rounded-xl bg-slate-50/50">
                                <p class="text-xs font-semibold text-slate-500">No subsections added yet.</p>
                                <button 
                                    type="button" 
                                    @click="addManageSubRow()"
                                    class="mt-2 inline-flex items-center gap-1 px-3 py-1 rounded-lg text-xs font-bold text-purple-600 bg-white border border-purple-200 shadow-2xs hover:bg-purple-50"
                                >
                                    + Add First Subsection
                                </button>
                            </div>
                        </template>

                        <div class="space-y-3">
                            <template x-for="(sub, index) in manageSubsectionsList" :key="index">
                                <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 space-y-2.5">
                                    <input type="hidden" :name="'subsections[' + index + '][id]'" :value="sub.id || ''">
                                    <div class="flex items-center justify-between pb-1.5 border-b border-slate-200/60">
                                        <div class="flex items-center gap-2">
                                            <span class="w-5 h-5 rounded-md bg-purple-100 text-purple-700 font-bold text-xs flex items-center justify-center" x-text="index + 1"></span>
                                            <span class="text-xs font-bold text-slate-700">Subsection #<span x-text="index + 1"></span></span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <label class="inline-flex items-center gap-1.5 text-xs text-slate-600 font-medium cursor-pointer">
                                                <input 
                                                    type="checkbox" 
                                                    :name="'subsections[' + index + '][status]'" 
                                                    value="1" 
                                                    :checked="Boolean(sub.status)"
                                                    class="rounded border-slate-300 text-purple-600 focus:ring-purple-500"
                                                >
                                                <span>Active</span>
                                            </label>
                                            <button 
                                                type="button" 
                                                @click="removeManageSubRow(index)"
                                                class="text-rose-500 hover:text-rose-700 p-1 rounded-lg hover:bg-rose-50 transition-colors cursor-pointer"
                                                title="Remove this subsection"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Name <span class="text-rose-500">*</span></label>
                                            <input 
                                                type="text" 
                                                :name="'subsections[' + index + '][subsection_name]'" 
                                                x-model="sub.subsection_name"
                                                placeholder="e.g. Hero Slide 1" 
                                                class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-purple-500"
                                                required
                                            />
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Title</label>
                                            <input 
                                                type="text" 
                                                :name="'subsections[' + index + '][subsection_title]'" 
                                                x-model="sub.subsection_title"
                                                placeholder="e.g. Huge Summer Sale" 
                                                class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-purple-500"
                                            />
                                        </div>
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-600 uppercase mb-1">Slug</label>
                                            <input 
                                                type="text" 
                                                :name="'subsections[' + index + '][subsection_slug]'" 
                                                x-model="sub.subsection_slug"
                                                placeholder="e.g. slide-1" 
                                                class="w-full px-2.5 py-1.5 rounded-lg border border-slate-300 bg-white text-xs text-slate-800 placeholder:text-slate-400 focus:outline-none focus:border-purple-500 font-mono"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-2.5">
                        <button 
                            type="button" 
                            @click="openSubModal = false"
                            class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-800 hover:bg-slate-200/60 transition-colors cursor-pointer"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="bg-purple-600 hover:bg-purple-700 text-white px-5 py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm hover:shadow cursor-pointer flex items-center gap-1.5"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Save Subsections</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Subsections Modal Dialog -->
    <div 
        x-show="openViewModal" 
        x-cloak
        style="display: none;"
        class="fixed inset-0 z-50 overflow-y-auto"
        aria-labelledby="modal-view-title" 
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
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-slate-200/90"
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
                            <h3 class="text-base font-bold text-slate-900" x-text="viewSectionName ? 'Subsections: ' + viewSectionName : 'Subsections'">
                                Subsections
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
                <div class="px-6 py-5 max-h-[60vh] overflow-y-auto">
                    <template x-if="viewSubsectionsList.length === 0">
                        <div class="py-10 text-center flex flex-col items-center justify-center">
                            <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-slate-700">No sub-sections configured</p>
                            <p class="text-xs text-slate-400 mt-1 max-w-xs">
                                <span x-show="viewIsSubsectionEnabled">This section has subsections enabled, but no items/slides added yet.</span>
                                <span x-show="!viewIsSubsectionEnabled">This section does not have subsections enabled.</span>
                            </p>
                            <template x-if="viewIsSubsectionEnabled">
                                <button 
                                    type="button" 
                                    @click="openViewModal = false; openManageSubsections(viewParentSection)"
                                    class="mt-4 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-purple-600 hover:bg-purple-700 transition-colors shadow-2xs cursor-pointer"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    <span>Add Subsections</span>
                                </button>
                            </template>
                        </div>
                    </template>

                    <template x-if="viewSubsectionsList.length > 0">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between pb-2 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                <span>Subsections & Their Components</span>
                                <span class="px-2 py-0.5 rounded-full bg-purple-50 text-purple-700 font-mono text-[11px]" x-text="viewSubsectionsList.length + ' Items'"></span>
                            </div>

                            <div class="space-y-3">
                                <template x-for="(sub, idx) in viewSubsectionsList" :key="sub.id || idx">
                                    <div 
                                        class="rounded-xl border overflow-hidden bg-white shadow-2xs transition-all"
                                        :class="sub.status ? 'border-emerald-300 ring-2 ring-emerald-500/15' : 'border-slate-200/90'"
                                    >
                                        <!-- Subsection Header Bar -->
                                        <div 
                                            class="flex flex-wrap items-center justify-between gap-3 px-4 py-3 transition-colors"
                                            :class="sub.status ? 'bg-emerald-50/30 hover:bg-emerald-50/50' : 'bg-white hover:bg-slate-50/50'"
                                        >
                                            <div class="flex items-center gap-3">
                                                <span 
                                                    class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold shrink-0 transition-colors"
                                                    :class="sub.status ? 'bg-emerald-600 text-white shadow-xs' : 'bg-purple-50 text-purple-600'"
                                                    x-text="idx + 1"
                                                ></span>
                                                <div>
                                                    <div class="flex items-center gap-2">
                                                        <h5 class="text-sm font-bold text-slate-900" x-text="sub.subsection_name"></h5>
                                                        <span x-show="sub.status" class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                            In Use
                                                        </span>
                                                    </div>
                                                    <p class="text-xs text-slate-400" x-show="sub.subsection_title" x-text="sub.subsection_title"></p>
                                                    <p class="text-xs text-slate-400 font-mono" x-show="sub.subsection_slug" x-text="'/' + sub.subsection_slug"></p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2.5">
                                                <!-- "Use This" Single-Select Option Button -->
                                                <button
                                                    type="button"
                                                    @click="toggleSubsectionStatus(sub, 'single')"
                                                    :disabled="sub._loading"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all cursor-pointer shadow-2xs disabled:opacity-50"
                                                    :class="sub.status ? 'bg-emerald-600 text-white shadow-emerald-500/25 ring-2 ring-emerald-600/30' : 'bg-white text-slate-600 border border-slate-200 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-300'"
                                                    :title="sub.status ? 'Currently Selected' : 'Set as the only active variant'"
                                                >
                                                    <svg x-show="sub.status" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    <span x-text="sub.status ? 'Selected' : 'Use This'"></span>
                                                </button>

                                                <!-- Interactive Toggle Switch -->
                                                <div class="flex items-center gap-1.5 bg-slate-50 border border-slate-200/80 px-2 py-1 rounded-lg">
                                                    <button 
                                                        type="button" 
                                                        @click="toggleSubsectionStatus(sub, 'toggle')"
                                                        :disabled="sub._loading"
                                                        class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-1 disabled:opacity-50"
                                                        :class="sub.status ? 'bg-emerald-500' : 'bg-slate-300'"
                                                        :title="sub.status ? 'Active - Click to toggle off' : 'Inactive - Click to toggle on'"
                                                    >
                                                        <span 
                                                            class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                                            :class="sub.status ? 'translate-x-4' : 'translate-x-0'"
                                                        ></span>
                                                    </button>
                                                    <span 
                                                        class="text-[11px] font-bold select-none cursor-pointer" 
                                                        :class="sub.status ? 'text-emerald-700' : 'text-slate-400'"
                                                        @click="toggleSubsectionStatus(sub, 'toggle')"
                                                        x-text="sub.status ? 'Active' : 'Off'"
                                                    ></span>
                                                </div>

                                                <!-- Add Component Button -->
                                                <button
                                                    type="button"
                                                    @click="openSubCompAssign(sub)"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-purple-700 bg-purple-50 hover:bg-purple-100 border border-purple-200 hover:border-purple-300 transition-colors cursor-pointer shadow-2xs"
                                                    title="Assign Components to this Subsection"
                                                >
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                                    </svg>
                                                    <span>Add Component</span>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Subsection's Assigned Components List -->
                                        <div class="border-t border-slate-100 divide-y divide-slate-100 bg-slate-50/50">
                                            <template x-if="!sub.components || sub.components.length === 0">
                                                <div class="px-4 py-2.5 flex items-center justify-between text-xs text-slate-400 italic">
                                                    <span>No components assigned yet.</span>
                                                    <button
                                                        type="button"
                                                        @click="openSubCompAssign(sub)"
                                                        class="text-xs font-semibold text-purple-600 hover:text-purple-700 hover:underline not-italic cursor-pointer"
                                                    >
                                                        + Add Component
                                                    </button>
                                                </div>
                                            </template>
                                            <template x-for="(comp, ci) in sub.components" :key="comp.id">
                                                <div class="flex items-center justify-between px-4 py-2 bg-white hover:bg-slate-50/70 transition-colors">
                                                    <div class="flex items-center gap-2.5">
                                                        <span class="w-5 h-5 rounded bg-slate-100 text-slate-500 flex items-center justify-center text-[10px] font-bold shrink-0" x-text="ci + 1"></span>
                                                        <div>
                                                            <h6 class="text-xs font-bold text-slate-800" x-text="comp.component_name"></h6>
                                                            <p class="text-[10px] text-slate-400 font-mono" x-show="comp.component_slug" x-text="'/' + comp.component_slug"></p>
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
                        </div>
                    </template>
                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                    <div>
                        <template x-if="viewIsSubsectionEnabled && viewSubsectionsList.length > 0">
                            <button 
                                type="button" 
                                @click="openViewModal = false; openManageSubsections(viewParentSection)"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-purple-700 bg-purple-50 border border-purple-200 hover:bg-purple-100 transition-colors cursor-pointer"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span>Manage Subsections</span>
                            </button>
                        </template>
                    </div>
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
                        <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
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
                        @isset($components)
                            @foreach($components as $comp)
                            <label
                                for="addsec_sub_comp_{{ $comp->id }}"
                                class="flex items-center gap-3 px-4 py-3 bg-white hover:bg-slate-50/70 transition-colors cursor-pointer"
                            >
                                <input
                                    type="checkbox"
                                    id="addsec_sub_comp_{{ $comp->id }}"
                                    x-model="activeSubComps[{{ $comp->id }}]"
                                    class="w-4 h-4 rounded text-purple-600 border-slate-300 focus:ring-purple-500 cursor-pointer"
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
                        @endisset
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
                        class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-purple-600 hover:bg-purple-700 border border-purple-600 hover:border-purple-700 transition-all cursor-pointer shadow-sm shadow-purple-500/20"
                    >
                        Save Components
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
