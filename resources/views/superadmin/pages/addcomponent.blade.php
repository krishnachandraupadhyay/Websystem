@extends('superadmin.layout')

@section('title', 'Component Management')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{ 
    openModal: {{ (isset($errors) && $errors->any()) ? 'true' : 'false' }}, 
    openSubModal: false,
    parentComponentId: null,
    parentComponentName: '',
    parentComponentTitle: '',
    subMappings: {{ json_encode($componentSubcomponentsMap ?? []) }},
    activeSubcomponents: {},
    isEdit: false,
    formAction: '{{ route('components.store') }}',
    componentName: '{{ old('component_name', '') }}', 
    componentTitle: '{{ old('component_title', '') }}',
    componentSlug: '{{ old('component_slug', '') }}',
    description: '{{ old('description', '') }}',
    status: '{{ old('status', '1') }}',
    isSubcomponent: {{ old('is_subcomponent', 0) ? 'true' : 'false' }},
    toggleSubcomponent() {
        if (!this.isSubcomponent) {
            let confirmed = confirm('Do you want to use subcomponents inside this component?');
            if (confirmed) {
                this.isSubcomponent = true;
            } else {
                this.isSubcomponent = false;
            }
        } else {
            this.isSubcomponent = false;
        }
    },
    generateSlug() {
        if (!this.isEdit) {
            this.componentSlug = this.componentName.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
        }
    },
    openCreate() {
        this.isEdit = false;
        this.formAction = '{{ route('components.store') }}';
        this.componentName = '';
        this.componentTitle = '';
        this.componentSlug = '';
        this.description = '';
        this.status = '1';
        this.isSubcomponent = false;
        this.openModal = true;
    },
    openEdit(item) {
        this.isEdit = true;
        this.formAction = '/components/' + item.id;
        this.componentName = item.component_name || '';
        this.componentTitle = item.component_title || '';
        this.componentSlug = item.component_slug || '';
        this.description = item.description || '';
        this.status = (item.status == 1 || item.status === true || item.status === '1') ? '1' : '0';
        this.isSubcomponent = Boolean(item.is_subcomponent);
        this.openModal = true;
    },
    openManageSubcomponents(comp) {
        this.parentComponentId = comp.id;
        this.parentComponentName = comp.component_name || '';
        this.parentComponentTitle = comp.component_title || '';
        let currentSubs = this.subMappings[comp.id] || {};
        this.activeSubcomponents = {};
        @foreach($components as $c)
            if ({{ $c->id }} !== comp.id) {
                this.activeSubcomponents[{{ $c->id }}] = currentSubs[{{ $c->id }}] !== undefined ? Boolean(currentSubs[{{ $c->id }}]) : false;
            }
        @endforeach
        this.openSubModal = true;
    },
    openViewModal: false,
    viewParentComp: null,
    viewComponentName: '',
    viewComponentTitle: '',
    viewComponentSlug: '',
    viewIsSubcomponentEnabled: false,
    viewSubcomponents: [],
    openViewSubcomponents(comp, subs) {
        this.viewParentComp = comp;
        this.viewComponentName = comp.component_name || '';
        this.viewComponentTitle = comp.component_title || '';
        this.viewComponentSlug = comp.component_slug || '';
        this.viewIsSubcomponentEnabled = Boolean(comp.is_subcomponent);
        this.viewSubcomponents = subs || [];
        this.openViewModal = true;
    },
    // Dynamic Fields Management State & Methods
    openFieldsModal: false,
    fieldsComponentId: null,
    fieldsComponentName: '',
    fieldsComponentTitle: '',
    fieldsComponentSlug: '',
    fieldsList: [],
    isLoadingFields: false,
    isFieldFormOpen: false,
    isEditingField: false,
    fieldErrorMsg: '',
    isSavingField: false,
    fieldForm: {
        id: null,
        field_label: '',
        field_name: '',
        field_type: 'text',
        placeholder: '',
        default_value: '',
        help_text: '',
        is_required: false,
        is_active: true,
        sort_order: 1,
        options: [{ value: '', label: '' }]
    },
    slugify(text) {
        return (text || '').toString().toLowerCase().trim()
            .replace(/\s+/g, '_')
            .replace(/[^\w\-]+/g, '')
            .replace(/\-\-+/g, '_')
            .replace(/^-+/, '')
            .replace(/-+$/, '');
    },
    onFieldLabelChange() {
        if (!this.isEditingField) {
            this.fieldForm.field_name = this.slugify(this.fieldForm.field_label);
        }
    },
    addOptionRow() {
        this.fieldForm.options.push({ value: '', label: '' });
    },
    removeOptionRow(index) {
        if (this.fieldForm.options.length > 1) {
            this.fieldForm.options.splice(index, 1);
        } else {
            this.fieldForm.options = [{ value: '', label: '' }];
        }
    },
    openManageFields(comp) {
        this.fieldsComponentId = comp.id;
        this.fieldsComponentName = comp.component_name || '';
        this.fieldsComponentTitle = comp.component_title || comp.component_name || '';
        this.fieldsComponentSlug = comp.component_slug || '';
        this.isFieldFormOpen = false;
        this.fieldErrorMsg = '';
        this.openFieldsModal = true;
        this.loadFields();
    },
    quickAddPreset(presetKey) {
        const presets = {
            'text': { label: 'Title / Label', name: 'label', type: 'text', placeholder: 'e.g. Enter title or label...', default_value: '', help_text: 'Text input displayed in admin form', required: true },
            'url': { label: 'Link URL (href)', name: 'link_url', type: 'url', placeholder: 'https://example.com, /page, or #section', default_value: '#', help_text: 'Target destination URL or anchor', required: true },
            'textarea': { label: 'Description Content', name: 'description', type: 'textarea', placeholder: 'Enter description content here...', default_value: '', help_text: 'Multi-line paragraph text', required: false },
            'image': { label: 'Image File', name: 'image', type: 'image', placeholder: '', default_value: '', help_text: 'Upload JPG, PNG, or WEBP image', required: false },
            'select': { label: 'Target Window', name: 'target', type: 'select', placeholder: '-- Select Target --', default_value: '_self', help_text: 'Choose where link opens', required: false, options: [{ value: '_self', label: 'Same Tab (_self)' }, { value: '_blank', label: 'New Tab (_blank)' }] },
            'date': { label: 'Date', name: 'event_date', type: 'date', placeholder: '', default_value: '', help_text: 'Select date (YYYY-MM-DD)', required: false }
        };
        let p = presets[presetKey] || presets['text'];
        this.isEditingField = false;
        this.fieldErrorMsg = '';
        this.fieldForm = {
            id: null,
            field_label: p.label,
            field_name: p.name,
            field_type: p.type,
            placeholder: p.placeholder || '',
            default_value: p.default_value || '',
            help_text: p.help_text || '',
            is_required: Boolean(p.required),
            is_active: true,
            sort_order: (this.fieldsList.length + 1),
            options: p.options ? JSON.parse(JSON.stringify(p.options)) : [{ value: '', label: '' }]
        };
        this.isFieldFormOpen = true;
    },
    async loadFields() {
        this.isLoadingFields = true;
        try {
            let res = await fetch(`/components/${this.fieldsComponentId}/fields`);
            let data = await res.json();
            this.fieldsList = data.fields || [];
        } catch (e) {
            console.error('Error loading component fields:', e);
        } finally {
            this.isLoadingFields = false;
        }
    },
    openNewFieldForm() {
        this.isEditingField = false;
        this.fieldErrorMsg = '';
        this.fieldForm = {
            id: null,
            field_label: '',
            field_name: '',
            field_type: 'text',
            placeholder: '',
            default_value: '',
            help_text: '',
            is_required: false,
            is_active: true,
            sort_order: (this.fieldsList.length + 1),
            options: [{ value: '', label: '' }]
        };
        this.isFieldFormOpen = true;
    },
    editField(f) {
        this.isEditingField = true;
        this.fieldErrorMsg = '';
        let opts = [];
        if (f.options && Array.isArray(f.options) && f.options.length > 0) {
            opts = JSON.parse(JSON.stringify(f.options));
        } else {
            opts = [{ value: '', label: '' }];
        }
        this.fieldForm = {
            id: f.id,
            field_label: f.field_label || '',
            field_name: f.field_name || '',
            field_type: f.field_type || 'text',
            placeholder: f.placeholder || '',
            default_value: f.default_value || '',
            help_text: f.help_text || '',
            is_required: Boolean(f.is_required),
            is_active: Boolean(f.is_active),
            sort_order: f.sort_order || 1,
            options: opts
        };
        this.isFieldFormOpen = true;
    },
    async saveField() {
        this.fieldErrorMsg = '';
        if (!this.fieldForm.field_label || !this.fieldForm.field_name || !this.fieldForm.field_type) {
            this.fieldErrorMsg = 'Please enter Field Label, Field Name, and select a Field Type.';
            return;
        }
        this.isSavingField = true;
        let url = this.isEditingField 
            ? `/components/${this.fieldsComponentId}/fields/${this.fieldForm.id}`
            : `/components/${this.fieldsComponentId}/fields`;
        let method = this.isEditingField ? 'PUT' : 'POST';

        try {
            let res = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(this.fieldForm)
            });
            let data = await res.json();
            if (!res.ok) {
                this.fieldErrorMsg = data.message || 'Validation error saving field.';
                return;
            }
            this.isFieldFormOpen = false;
            await this.loadFields();
        } catch (e) {
            this.fieldErrorMsg = 'Failed to save field: ' + e.message;
        } finally {
            this.isSavingField = false;
        }
    },
    async deleteField(fieldId) {
        if (!confirm('Are you sure you want to delete this field definition? Existing section data for this field will also be removed.')) {
            return;
        }
        try {
            let res = await fetch(`/components/${this.fieldsComponentId}/fields/${fieldId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            });
            if (res.ok) {
                await this.loadFields();
            }
        } catch (e) {
            console.error(e);
        }
    },
    async updateFieldSortOrder(fieldId, newOrder) {
        try {
            await fetch(`/components/${this.fieldsComponentId}/fields/order`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    orders: [{ id: fieldId, sort_order: parseInt(newOrder) || 1 }]
                })
            });
            await this.loadFields();
        } catch (e) {
            console.error(e);
        }
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
                <h2 class="text-base font-bold text-slate-800">All Website Components</h2>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-500 font-semibold font-mono">{{ $components->count() }}</span>
            </div>
            <button 
                @click="openCreate()" 
                type="button" 
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-sm shadow-blue-500/20 cursor-pointer flex items-center gap-1.5"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Add Component</span>
            </button>
        </div>

        @if($components->isEmpty())
            <!-- Empty State Container -->
            <div class="rounded-2xl border border-blue-200/90 py-16 flex flex-col items-center justify-center bg-blue-50/20 text-center">
                <div class="w-14 h-14 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center mb-3 shadow-2xs">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                </div>
                <h3 class="text-blue-600 text-lg font-bold">No Components Found</h3>
                <p class="text-xs text-slate-400 mt-1 max-w-sm">
                    No components have been created yet. Click the button below to add your first component.
                </p>
                <button 
                    @click="openCreate()" 
                    type="button" 
                    class="mt-4 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition-all shadow-xs cursor-pointer"
                >
                    + Create First Component
                </button>
            </div>
        @else
            <!-- Components Table -->
            <div class="overflow-x-auto rounded-xl border border-slate-200/80">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-[11px] font-bold tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">#</th>
                            <th class="px-5 py-3.5">Component Name</th>
                            <th class="px-5 py-3.5">Component Title</th>
                            <th class="px-5 py-3.5">Slug (URL)</th>
                            <th class="px-5 py-3.5">Description</th>
                            <th class="px-5 py-3.5">Status</th>
                            <th class="px-5 py-3.5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($components as $index => $component)
                            @php
                                $subComps = $component->subcomponents->where('pivot.status', 1)->values();
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-5 py-4 text-xs font-semibold text-slate-400">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-5 py-4 font-bold text-slate-900">
                                    <div class="flex items-center gap-1.5">
                                        <span>{{ $component->component_name }}</span>
                                        @if($component->is_subcomponent)
                                            <span class="text-amber-500 text-sm leading-none select-none cursor-default" title="Subcomponents ({{ $subComps->count() }})">★</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-slate-600 font-medium">
                                    {{ $component->component_title }}
                                </td>
                                <td class="px-5 py-4 text-xs font-mono text-slate-500">
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 border border-slate-200 text-slate-600">
                                        /{{ $component->component_slug }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-xs text-slate-500 max-w-xs truncate">
                                    {{ $component->description ?? '—' }}
                                </td>
                                <td class="px-5 py-4">
                                    <form action="{{ route('components.toggleStatus', $component->id) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button 
                                            type="submit" 
                                            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold transition-all cursor-pointer shadow-2xs hover:scale-105 select-none {{ $component->status ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 hover:border-emerald-300' : 'bg-slate-100 text-slate-600 border border-slate-200 hover:bg-slate-200 hover:text-slate-800' }}"
                                            title="Click to {{ $component->status ? 'deactivate' : 'activate' }}"
                                        >
                                            <span class="w-1.5 h-1.5 rounded-full {{ $component->status ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                            <span>{{ $component->status ? 'Active' : 'Inactive' }}</span>
                                        </button>
                                    </form>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="inline-flex items-center gap-1.5 justify-end">
                                        <!-- View Subcomponents Button -->
                                        <button 
                                            type="button" 
                                            @click='openViewSubcomponents(@json($component), @json($subComps))'
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-emerald-600 hover:text-emerald-700 bg-white hover:bg-emerald-50 border border-slate-200 hover:border-emerald-300 transition-all cursor-pointer shadow-2xs"
                                            title="View Subcomponents"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                        @if($component->is_subcomponent)
                                            <!-- Configure Subcomponents Button -->
                                            <button 
                                                type="button" 
                                                @click='openManageSubcomponents(@json($component))'
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-purple-600 hover:text-purple-700 bg-white hover:bg-purple-50 border border-slate-200 hover:border-purple-300 transition-all cursor-pointer shadow-2xs"
                                                title="Add / Configure Subcomponents"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/>
                                                </svg>
                                            </button>
                                        @endif
                                        <!-- Manage Fields Button -->
                                        <button 
                                            type="button" 
                                            @click='openManageFields(@json($component))'
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-indigo-600 hover:text-indigo-700 bg-white hover:bg-indigo-50 border border-slate-200 hover:border-indigo-300 transition-all cursor-pointer shadow-2xs"
                                            title="Manage Fields ({{ $component->fields->count() }})"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                                            </svg>
                                        </button>
                                        <!-- Edit Component Button -->
                                        <button 
                                            @click='openEdit(@json($component))' 
                                            type="button" 
                                            class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-blue-600 hover:text-blue-700 bg-white hover:bg-blue-50 border border-slate-200 hover:border-blue-300 transition-all cursor-pointer shadow-2xs"
                                            title="Edit Component"
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

    <!-- Modal Dialog for Creating New Component -->
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
                        <h3 class="text-base font-bold text-slate-900" id="modal-title" x-text="isEdit ? 'Edit Component' : 'Create New Component'">
                            Create New Component
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
                        <!-- Component Name -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Component Name <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="component_name" 
                                x-model="componentName"
                                @input="generateSlug()"
                                value="{{ old('component_name') }}"
                                placeholder="e.g. Header Bar, Pricing Card, Call to Action" 
                                class="w-full px-3.5 py-2.5 rounded-xl border @error('component_name') border-rose-300 bg-rose-50/30 @else border-slate-300 @enderror text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all"
                                required
                            />
                            @error('component_name')
                                <p x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Component Title -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Component Title <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="component_title" 
                                x-model="componentTitle"
                                value="{{ old('component_title') }}"
                                placeholder="e.g. Modern Responsive Navbar" 
                                class="w-full px-3.5 py-2.5 rounded-xl border @error('component_title') border-rose-300 bg-rose-50/30 @else border-slate-300 @enderror text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all"
                                required
                            />
                            @error('component_title')
                                <p x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Component Slug / URL -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Component Slug <span class="text-rose-500">*</span>
                            </label>
                            <div class="flex rounded-xl border @error('component_slug') border-rose-300 @else border-slate-300 @enderror overflow-hidden focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/20">
                                <span class="inline-flex items-center px-3 bg-slate-100 text-slate-500 text-xs font-mono border-r border-slate-200">
                                    /components/
                                </span>
                                <input 
                                    type="text" 
                                    name="component_slug" 
                                    x-model="componentSlug"
                                    value="{{ old('component_slug') }}"
                                    placeholder="header-bar" 
                                    class="w-full px-3.5 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none"
                                    required
                                />
                            </div>
                            @error('component_slug')
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
                                placeholder="Enter component description or details (optional)..."
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

                        <!-- Subcomponents Toggle -->
                        <div class="flex items-center justify-between p-3.5 bg-slate-50 border border-slate-200/80 rounded-xl">
                            <div>
                                <span class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Subcomponents Container
                                </span>
                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    Can this component contain other components? (e.g. Card)
                                </p>
                            </div>
                            <input type="hidden" name="is_subcomponent" :value="isSubcomponent ? 1 : 0">
                            <button 
                                type="button" 
                                @click="toggleSubcomponent()"
                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                :class="isSubcomponent ? 'bg-blue-600' : 'bg-slate-300'"
                                role="switch" 
                                :aria-checked="isSubcomponent"
                            >
                                <span 
                                    aria-hidden="true" 
                                    class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                    :class="isSubcomponent ? 'translate-x-5' : 'translate-x-0'"
                                ></span>
                            </button>
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
                            <span x-text="isEdit ? 'Update Component' : 'Save Component'">Save Component</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Subcomponents Management Modal -->
    <div 
        x-show="openSubModal" 
        x-cloak
        style="display: none;"
        class="fixed inset-0 z-50 overflow-y-auto"
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
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-xl border border-slate-200/90"
            >
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 bg-slate-50/70">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-900">
                                Manage Subcomponents
                            </h3>
                            <p class="text-xs text-slate-500 font-medium">
                                For: <span class="font-bold text-slate-800" x-text="parentComponentName"></span>
                            </p>
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

                <!-- Subcomponents Form -->
                <form :action="'/components/' + parentComponentId + '/subcomponents'" method="POST">
                    @csrf
                    <div class="px-6 py-5 max-h-[60vh] overflow-y-auto space-y-4">
                        <p class="text-xs text-slate-500">
                            Select components to use as subcomponents inside <span class="font-bold text-slate-800" x-text="parentComponentName"></span> (e.g. image, paragraph, heading, button):
                        </p>

                        <div class="divide-y divide-slate-100 rounded-xl border border-slate-200/80 overflow-hidden">
                            @foreach($components as $availComp)
                                <div 
                                    x-show="parentComponentId !== {{ $availComp->id }}"
                                    class="flex items-center justify-between p-3.5 bg-white hover:bg-slate-50/70 transition-colors"
                                >
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-xs shrink-0">
                                            {{ strtoupper(substr($availComp->component_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h5 class="text-sm font-bold text-slate-900">{{ $availComp->component_name }}</h5>
                                            <p class="text-xs text-slate-400 font-mono">/{{ $availComp->component_slug }}</p>
                                        </div>
                                    </div>

                                    <!-- Checkbox / Toggle for Subcomponent -->
                                    <div class="flex items-center gap-2">
                                        <input 
                                            type="hidden" 
                                            name="subcomponents[{{ $availComp->id }}]" 
                                            :value="activeSubcomponents[{{ $availComp->id }}] ? 1 : 0"
                                        >
                                        <button 
                                            type="button" 
                                            @click="activeSubcomponents[{{ $availComp->id }}] = !activeSubcomponents[{{ $availComp->id }}]"
                                            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                            :class="activeSubcomponents[{{ $availComp->id }}] ? 'bg-emerald-600' : 'bg-slate-300'"
                                            role="switch" 
                                            :aria-checked="activeSubcomponents[{{ $availComp->id }}]"
                                        >
                                            <span 
                                                aria-hidden="true" 
                                                class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                                :class="activeSubcomponents[{{ $availComp->id }}] ? 'translate-x-5' : 'translate-x-0'"
                                            ></span>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
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
                            class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl text-xs font-bold transition-all shadow-sm hover:shadow cursor-pointer flex items-center gap-1.5"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Save Subcomponents</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Subcomponents Modal Dialog -->
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
                            <h3 class="text-base font-bold text-slate-900" x-text="viewComponentName ? 'Subcomponents: ' + viewComponentName : 'Subcomponents'">
                                Subcomponents
                            </h3>
                            <p class="text-xs text-slate-400 font-mono" x-show="viewComponentSlug" x-text="'/' + viewComponentSlug"></p>
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
                    <template x-if="viewSubcomponents.length === 0">
                        <div class="py-10 text-center flex flex-col items-center justify-center">
                            <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                </svg>
                            </div>
                            <p class="text-sm font-bold text-slate-700">No subcomponents assigned</p>
                            <p class="text-xs text-slate-400 mt-1 max-w-xs">
                                <span x-show="viewIsSubcomponentEnabled">This component has subcomponents enabled, but none are selected yet.</span>
                                <span x-show="!viewIsSubcomponentEnabled">This component does not have subcomponents enabled.</span>
                            </p>
                            <template x-if="viewIsSubcomponentEnabled">
                                <button 
                                    type="button" 
                                    @click="openViewModal = false; openManageSubcomponents(viewParentComp)"
                                    class="mt-4 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-purple-600 hover:bg-purple-700 transition-colors shadow-2xs cursor-pointer"
                                >
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    <span>Configure Subcomponents</span>
                                </button>
                            </template>
                        </div>
                    </template>

                    <template x-if="viewSubcomponents.length > 0">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between pb-2 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                <span>Subcomponents List</span>
                                <span class="px-2 py-0.5 rounded-full bg-purple-50 text-purple-700 font-mono text-[11px]" x-text="viewSubcomponents.length + ' Active'"></span>
                            </div>
                            <div class="divide-y divide-slate-100 rounded-xl border border-slate-200/80 overflow-hidden">
                                <template x-for="(sub, idx) in viewSubcomponents" :key="sub.id">
                                    <div class="flex items-center justify-between px-4 py-3 bg-white hover:bg-slate-50/70 transition-colors">
                                        <div class="flex items-center gap-3">
                                            <span class="w-6 h-6 rounded-md bg-purple-50 text-purple-600 flex items-center justify-center text-xs font-bold shrink-0" x-text="idx + 1"></span>
                                            <div>
                                                <h5 class="text-sm font-bold text-slate-900" x-text="sub.component_name"></h5>
                                                <p class="text-xs text-slate-400 font-mono" x-text="'/' + sub.component_slug"></p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Active
                                            </span>
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
                        <template x-if="viewIsSubcomponentEnabled && viewSubcomponents.length > 0">
                            <button 
                                type="button" 
                                @click="openViewModal = false; openManageSubcomponents(viewParentComp)"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-purple-700 bg-purple-50 border border-purple-200 hover:bg-purple-100 transition-colors cursor-pointer"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/>
                                </svg>
                                <span>Manage Subcomponents</span>
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

    <!-- Dynamic Component Fields Management Modal -->
    <div 
        x-show="openFieldsModal" 
        x-cloak
        style="display: none;"
        class="fixed inset-0 z-50 overflow-y-auto"
        role="dialog" 
        aria-modal="true"
        @keydown.escape.window="openFieldsModal = false"
    >
        <!-- Modal Backdrop -->
        <div 
            x-show="openFieldsModal"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
            @click="openFieldsModal = false"
        ></div>

        <!-- Modal Dialog Placement -->
        <div class="flex min-h-full items-center justify-center p-4 sm:p-6 md:p-8 text-center">
            <div 
                x-show="openFieldsModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                @click.away="openFieldsModal = false"
                class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-4xl border border-slate-200/90"
            >
                <!-- Top Accent Gradient Line -->
                <div class="h-1.5 w-full bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600"></div>

                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-slate-100 px-6 sm:px-8 py-5 sm:py-6 bg-gradient-to-r from-slate-50/90 via-white to-indigo-50/30">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-indigo-600 to-blue-600 text-white flex items-center justify-center font-bold shadow-md shadow-indigo-500/20 ring-4 ring-indigo-50 shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-3">
                                <h3 class="text-lg font-bold text-slate-900 tracking-tight">
                                    Field Definitions
                                </h3>
                                <span 
                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold transition-all"
                                    :class="fieldsList.length > 0 ? 'bg-indigo-50 text-indigo-700 border border-indigo-200/70' : 'bg-slate-100 text-slate-500 border border-slate-200'"
                                >
                                    <span class="w-1.5 h-1.5 rounded-full" :class="fieldsList.length > 0 ? 'bg-indigo-600 animate-pulse' : 'bg-slate-400'"></span>
                                    <span x-text="fieldsList.length + ' ' + (fieldsList.length === 1 ? 'field' : 'fields')"></span>
                                </span>
                            </div>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-xs text-slate-500 font-medium">Component:</span>
                                <span class="text-xs font-bold text-slate-800" x-text="fieldsComponentName"></span>
                                <span class="text-[11px] font-mono px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 border border-slate-200" x-show="fieldsComponentSlug" x-text="'/' + fieldsComponentSlug"></span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <button 
                            type="button" 
                            x-show="!isFieldFormOpen"
                            @click="openNewFieldForm()"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-500 hover:to-blue-500 shadow-sm shadow-indigo-500/20 active:scale-95 transition-all cursor-pointer"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>Add Field</span>
                        </button>
                        <button 
                            type="button" 
                            @click="openFieldsModal = false" 
                            class="w-9 h-9 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer"
                            aria-label="Close"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Modal Body with Generous Padding & Rhythm -->
                <div class="p-6 sm:p-8 max-h-[75vh] overflow-y-auto space-y-7">
                    <!-- Error Message Banner -->
                    <div x-show="fieldErrorMsg" x-cloak class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center justify-between shadow-2xs">
                        <div class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span x-text="fieldErrorMsg"></span>
                        </div>
                        <button @click="fieldErrorMsg = ''" type="button" class="text-rose-500 hover:text-rose-700 font-bold ml-2 cursor-pointer">&times;</button>
                    </div>

                    <!-- Collapsible Add/Edit Form -->
                    <div x-show="isFieldFormOpen" x-cloak class="p-6 sm:p-7 rounded-2xl bg-gradient-to-b from-slate-50/90 to-indigo-50/30 border border-indigo-100/90 space-y-6 shadow-xs">
                        <div class="flex items-center justify-between pb-4 border-b border-indigo-100/80">
                            <h4 class="text-xs font-bold text-indigo-950 uppercase tracking-wider flex items-center gap-2.5">
                                <span class="w-7 h-7 rounded-lg bg-indigo-600 text-white flex items-center justify-center text-xs shadow-2xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </span>
                                <span x-text="isEditingField ? 'Edit Field Definition' : 'Define New Component Field'"></span>
                            </h4>
                            <button 
                                type="button" 
                                @click="isFieldFormOpen = false" 
                                class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors cursor-pointer px-2 py-1 rounded-lg hover:bg-slate-100"
                            >
                                Cancel
                            </button>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 sm:gap-6">
                            <!-- Field Label -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">
                                    Field Label <span class="text-rose-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    x-model="fieldForm.field_label"
                                    @input="onFieldLabelChange()"
                                    placeholder="e.g. Button Text, Target URL, Avatar"
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 bg-white text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 font-medium transition-all"
                                    required
                                >
                            </div>

                            <!-- Field Name (Machine Key) -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2 flex items-center justify-between">
                                    <span>Database Field Key <span class="text-rose-500">*</span></span>
                                    <span class="text-[10px] text-slate-400 font-mono">auto-generated</span>
                                </label>
                                <input 
                                    type="text" 
                                    x-model="fieldForm.field_name"
                                    placeholder="e.g. button_text"
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 bg-white font-mono text-xs text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all"
                                    required
                                >
                            </div>

                            <!-- Field Type Selector -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">
                                    HTML Input / Form Type <span class="text-rose-500">*</span>
                                </label>
                                <select 
                                    x-model="fieldForm.field_type"
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 font-medium transition-all"
                                >
                                    <optgroup label="Standard Text & Contact">
                                        <option value="text">Text (Single-line input)</option>
                                        <option value="textarea">Textarea (Multi-line paragraph)</option>
                                        <option value="email">Email Address</option>
                                        <option value="password">Password (Masked)</option>
                                        <option value="number">Number (Numeric input)</option>
                                        <option value="tel">Telephone / Phone</option>
                                        <option value="url">URL (Link input)</option>
                                        <option value="search">Search Box</option>
                                    </optgroup>
                                    <optgroup label="Choice & Selection">
                                        <option value="select">Select (Dropdown options)</option>
                                        <option value="radio">Radio Buttons (Single choice)</option>
                                        <option value="checkbox">Checkbox (Toggle / Yes-No)</option>
                                        <option value="range">Range (Slider 0-100)</option>
                                        <option value="color">Color (Color Picker)</option>
                                    </optgroup>
                                    <optgroup label="Date & Time">
                                        <option value="date">Date (YYYY-MM-DD)</option>
                                        <option value="time">Time (HH:MM)</option>
                                        <option value="datetime-local">Date & Time</option>
                                        <option value="month">Month (YYYY-MM)</option>
                                        <option value="week">Week (YYYY-Www)</option>
                                    </optgroup>
                                    <optgroup label="Media & Uploads">
                                        <option value="image">Image (PNG, JPG, WEBP upload)</option>
                                        <option value="file">File (Single document)</option>
                                        <option value="multiple_file">Multiple Files (Batch attachment)</option>
                                        <option value="video">Video (MP4, WEBM upload)</option>
                                    </optgroup>
                                    <optgroup label="Buttons & Hidden">
                                        <option value="button">Button (Frontend Button label)</option>
                                        <option value="submit">Submit (Frontend Submit label)</option>
                                        <option value="reset">Reset (Frontend Reset label)</option>
                                        <option value="hidden">Hidden (Constant value)</option>
                                    </optgroup>
                                </select>
                            </div>

                            <!-- Sort Order -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">
                                    Display Order
                                </label>
                                <input 
                                    type="number" 
                                    x-model.number="fieldForm.sort_order"
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 font-medium transition-all"
                                >
                            </div>

                            <!-- Placeholder -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">
                                    Placeholder Text (Optional)
                                </label>
                                <input 
                                    type="text" 
                                    x-model="fieldForm.placeholder"
                                    placeholder="e.g. Enter value here..."
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all"
                                >
                            </div>

                            <!-- Default Value -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">
                                    Default Value (Optional)
                                </label>
                                <input 
                                    type="text" 
                                    x-model="fieldForm.default_value"
                                    placeholder="Default value if left blank"
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all"
                                >
                            </div>

                            <!-- Help Text -->
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 mb-2">
                                    Helper Note / Instructions (Optional)
                                </label>
                                <input 
                                    type="text" 
                                    x-model="fieldForm.help_text"
                                    placeholder="Hint displayed underneath this field in the Admin edit form"
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 bg-white text-xs text-slate-900 focus:outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 transition-all"
                                >
                            </div>

                            <!-- Required & Active Toggle Cards with Comfortable Padding -->
                            <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                                <label class="flex items-center gap-3.5 p-4 rounded-xl border cursor-pointer select-none transition-all shadow-2xs" :class="fieldForm.is_required ? 'bg-indigo-50/80 border-indigo-300 text-indigo-900' : 'bg-white border-slate-200 text-slate-700 hover:border-slate-300'">
                                    <input type="checkbox" x-model="fieldForm.is_required" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                    <div>
                                        <p class="text-xs font-bold">Required Field</p>
                                        <p class="text-[10px] text-slate-500 mt-0.5">Admin cannot leave this field empty</p>
                                    </div>
                                </label>
                                <label class="flex items-center gap-3.5 p-4 rounded-xl border cursor-pointer select-none transition-all shadow-2xs" :class="fieldForm.is_active ? 'bg-emerald-50/80 border-emerald-300 text-emerald-900' : 'bg-white border-slate-200 text-slate-700 hover:border-slate-300'">
                                    <input type="checkbox" x-model="fieldForm.is_active" class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                                    <div>
                                        <p class="text-xs font-bold">Active in Admin Form</p>
                                        <p class="text-[10px] text-slate-500 mt-0.5">Visible and editable in Section editor</p>
                                    </div>
                                </label>
                            </div>

                            <!-- Select & Radio Options Builder -->
                            <div x-show="['select', 'radio'].includes(fieldForm.field_type)" x-cloak class="sm:col-span-2 p-5 rounded-2xl bg-white border border-indigo-100 space-y-4 shadow-2xs">
                                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                                    <span class="text-xs font-bold text-indigo-950 uppercase tracking-wider" x-text="fieldForm.field_type === 'radio' ? 'Radio Choices' : 'Dropdown Options'"></span>
                                    <button 
                                        type="button" 
                                        @click="addOptionRow()"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-[11px] font-bold text-indigo-600 bg-indigo-50 hover:bg-indigo-100 transition-colors cursor-pointer"
                                    >
                                        + Add Option
                                    </button>
                                </div>
                                <div class="space-y-3">
                                    <template x-for="(opt, oIdx) in fieldForm.options" :key="oIdx">
                                        <div class="flex items-center gap-3">
                                            <input 
                                                type="text" 
                                                x-model="opt.value" 
                                                placeholder="Value (e.g. _self)" 
                                                class="flex-1 px-3.5 py-2 rounded-xl border border-slate-200 text-xs font-mono text-slate-800 focus:outline-none focus:border-indigo-500"
                                            >
                                            <input 
                                                type="text" 
                                                x-model="opt.label" 
                                                placeholder="Display Label (e.g. Same Window)" 
                                                class="flex-1 px-3.5 py-2 rounded-xl border border-slate-200 text-xs text-slate-800 focus:outline-none focus:border-indigo-500"
                                            >
                                            <button 
                                                type="button" 
                                                @click="removeOptionRow(oIdx)"
                                                class="w-8 h-8 rounded-xl text-rose-500 hover:bg-rose-50 flex items-center justify-center transition-colors cursor-pointer shrink-0"
                                                title="Remove Option"
                                            >
                                                &times;
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-indigo-100/70">
                            <button 
                                type="button" 
                                @click="isFieldFormOpen = false"
                                class="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-800 hover:bg-white transition-colors cursor-pointer"
                            >
                                Cancel
                            </button>
                            <button 
                                type="button" 
                                @click="saveField()"
                                :disabled="isSavingField"
                                class="px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-500 hover:to-blue-500 disabled:opacity-50 transition-all shadow-sm shadow-indigo-500/20 cursor-pointer flex items-center gap-2"
                            >
                                <span x-show="!isSavingField" x-text="isEditingField ? 'Update Field Definition' : 'Save Field Definition'"></span>
                                <span x-show="isSavingField">Saving Field...</span>
                            </button>
                        </div>
                    </div>

                    <!-- Fields List Table & Empty State -->
                    <div>
                        <!-- Loading State -->
                        <div x-show="isLoadingFields" class="py-14 text-center text-xs text-slate-400">
                            <div class="w-9 h-9 rounded-full border-2 border-indigo-600 border-t-transparent animate-spin mx-auto mb-3"></div>
                            <span>Loading field definitions...</span>
                        </div>

                        <!-- Visually Rich Empty State with Spacious Padding & Margins -->
                        <div x-show="!isLoadingFields && fieldsList.length === 0 && !isFieldFormOpen" class="relative overflow-hidden rounded-3xl border border-indigo-100 bg-gradient-to-b from-indigo-50/40 via-white to-slate-50/50 p-8 sm:p-12 md:p-14 text-center shadow-xs">
                            <!-- Ambient Glow Effect -->
                            <div class="absolute -top-16 left-1/2 -translate-x-1/2 w-96 h-48 bg-gradient-to-r from-blue-400/15 via-indigo-400/15 to-purple-400/15 rounded-full blur-3xl pointer-events-none"></div>

                            <div class="relative z-10 max-w-lg mx-auto">
                                <div class="w-20 h-20 rounded-3xl bg-gradient-to-tr from-indigo-600 to-blue-500 text-white flex items-center justify-center mx-auto mb-5 shadow-xl shadow-indigo-500/25 ring-8 ring-indigo-50/80">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                    </svg>
                                </div>
                                
                                <h4 class="text-lg font-bold text-slate-900 tracking-tight mb-2">No Fields Defined for <span class="text-indigo-600" x-text="fieldsComponentName"></span></h4>
                                <p class="text-xs text-slate-500 leading-relaxed mb-6">
                                    Define the dynamic inputs this component requires (e.g. text, link URL, image, dropdown). When Admins configure website sections, these exact fields will be generated automatically.
                                </p>

                                <!-- Quick Preset Template Chips with Generous Gap & Margins -->
                                <div class="mt-8 pt-6 border-t border-slate-200/70">
                                    <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-3.5">Quick Add Pre-configured Field</p>
                                    <div class="flex flex-wrap items-center justify-center gap-2.5 sm:gap-3">
                                        <button type="button" @click="quickAddPreset('text')" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-white hover:bg-indigo-50 hover:text-indigo-700 border border-slate-200 hover:border-indigo-300 shadow-2xs hover:scale-105 transition-all cursor-pointer">
                                            <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                            + Text Label
                                        </button>
                                        <button type="button" @click="quickAddPreset('url')" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-white hover:bg-indigo-50 hover:text-indigo-700 border border-slate-200 hover:border-indigo-300 shadow-2xs hover:scale-105 transition-all cursor-pointer">
                                            <span class="w-2 h-2 rounded-full bg-cyan-500"></span>
                                            + Link URL (href)
                                        </button>
                                        <button type="button" @click="quickAddPreset('textarea')" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-white hover:bg-indigo-50 hover:text-indigo-700 border border-slate-200 hover:border-indigo-300 shadow-2xs hover:scale-105 transition-all cursor-pointer">
                                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                            + Description Area
                                        </button>
                                        <button type="button" @click="quickAddPreset('image')" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-white hover:bg-indigo-50 hover:text-indigo-700 border border-slate-200 hover:border-indigo-300 shadow-2xs hover:scale-105 transition-all cursor-pointer">
                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                            + Image File
                                        </button>
                                        <button type="button" @click="quickAddPreset('select')" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-white hover:bg-indigo-50 hover:text-indigo-700 border border-slate-200 hover:border-indigo-300 shadow-2xs hover:scale-105 transition-all cursor-pointer">
                                            <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                            + Target Window
                                        </button>
                                    </div>
                                </div>

                                <div class="mt-8 mb-2 flex items-center justify-center gap-3">
                                    <button 
                                        type="button" 
                                        @click="openNewFieldForm()"
                                        class="inline-flex items-center gap-2.5 px-6 py-3 rounded-xl text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-500 hover:to-blue-500 shadow-md shadow-indigo-600/25 hover:shadow-indigo-600/35 transition-all cursor-pointer active:scale-95"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        <span>Create Custom Field</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Fields Table with Generous Padding -->
                        <div x-show="!isLoadingFields && fieldsList.length > 0" class="overflow-x-auto rounded-2xl border border-slate-200/90 shadow-2xs bg-white">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-[10px] font-bold tracking-wider">
                                    <tr>
                                        <th class="px-5 py-4 w-20">Order</th>
                                        <th class="px-5 py-4">Field Label</th>
                                        <th class="px-5 py-4">Field Key</th>
                                        <th class="px-5 py-4">Input Type</th>
                                        <th class="px-5 py-4">Required</th>
                                        <th class="px-5 py-4">Status</th>
                                        <th class="px-5 py-4 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <template x-for="(f, fIdx) in fieldsList" :key="f.id">
                                        <tr class="hover:bg-indigo-50/30 transition-colors">
                                            <td class="px-5 py-4">
                                                <input 
                                                    type="number" 
                                                    :value="f.sort_order" 
                                                    @change="updateFieldSortOrder(f.id, $event.target.value)"
                                                    class="w-16 px-2.5 py-1.5 rounded-lg border border-slate-200 text-center font-bold text-xs text-slate-700 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/30 transition-all"
                                                    title="Change order & press enter"
                                                >
                                            </td>
                                            <td class="px-5 py-4">
                                                <p class="font-bold text-slate-900 text-xs" x-text="f.field_label"></p>
                                                <p x-show="f.help_text" class="text-[10px] font-normal text-slate-400 mt-1" x-text="f.help_text"></p>
                                            </td>
                                            <td class="px-5 py-4 font-mono text-[11px] text-slate-600">
                                                <span class="px-2.5 py-1 rounded-md bg-slate-100 border border-slate-200" x-text="f.field_name"></span>
                                            </td>
                                            <td class="px-5 py-4">
                                                <span 
                                                    class="inline-flex items-center px-3 py-1 rounded-full font-bold uppercase text-[10px] shadow-2xs"
                                                    :class="{
                                                        'bg-blue-50 text-blue-700 border border-blue-200': ['text', 'textarea', 'number', 'password', 'email', 'search', 'tel'].includes(f.field_type),
                                                        'bg-emerald-50 text-emerald-700 border border-emerald-200': ['image', 'file', 'video', 'multiple_file'].includes(f.field_type),
                                                        'bg-amber-50 text-amber-700 border border-amber-200': ['select', 'radio'].includes(f.field_type),
                                                        'bg-purple-50 text-purple-700 border border-purple-200': ['checkbox', 'range', 'color'].includes(f.field_type),
                                                        'bg-cyan-50 text-cyan-700 border border-cyan-200': ['url', 'date', 'time', 'datetime-local', 'month', 'week'].includes(f.field_type),
                                                        'bg-slate-100 text-slate-700 border border-slate-200': ['button', 'submit', 'reset', 'hidden'].includes(f.field_type)
                                                    }"
                                                    x-text="f.field_type"
                                                ></span>
                                            </td>
                                            <td class="px-5 py-4">
                                                <span 
                                                    class="inline-flex items-center gap-1 font-bold text-[11px]"
                                                    :class="f.is_required ? 'text-rose-600' : 'text-slate-400'"
                                                >
                                                    <span x-text="f.is_required ? 'Required *' : 'Optional'"></span>
                                                </span>
                                            </td>
                                            <td class="px-5 py-4">
                                                <span 
                                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold"
                                                    :class="f.is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500'"
                                                >
                                                    <span class="w-1.5 h-1.5 rounded-full" :class="f.is_active ? 'bg-emerald-500' : 'bg-slate-400'"></span>
                                                    <span x-text="f.is_active ? 'Active' : 'Inactive'"></span>
                                                </span>
                                            </td>
                                            <td class="px-5 py-4 text-right">
                                                <div class="inline-flex items-center gap-2">
                                                    <button 
                                                        type="button" 
                                                        @click="editField(f)"
                                                        class="w-8 h-8 rounded-lg text-blue-600 hover:bg-blue-50 border border-slate-200 hover:border-blue-300 flex items-center justify-center transition-all cursor-pointer shadow-2xs"
                                                        title="Edit Field"
                                                    >
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                    </button>
                                                    <button 
                                                        type="button" 
                                                        @click="deleteField(f.id)"
                                                        class="w-8 h-8 rounded-lg text-rose-600 hover:bg-rose-50 border border-slate-200 hover:border-rose-300 flex items-center justify-center transition-all cursor-pointer shadow-2xs"
                                                        title="Delete Field"
                                                    >
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer with Comfortable Padding -->
                <div class="px-6 sm:px-8 py-4.5 sm:py-5 bg-slate-50/90 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span>Live Dynamic Schema • Configured fields reflect immediately in Admin Section forms</span>
                    </div>
                    <button 
                        type="button" 
                        @click="openFieldsModal = false"
                        class="px-6 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-100 hover:border-slate-400 transition-all cursor-pointer shadow-2xs self-end sm:self-auto"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection