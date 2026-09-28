@extends('superadmin.layout')

@section('title', 'Admin Management')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="{ 
    openModal: {{ (isset($errors) && $errors->any()) ? 'true' : 'false' }},
    isEdit: false,
    formAction: '{{ route('admins.store') }}',
    name: '{{ old('name', '') }}',
    email: '{{ old('email', '') }}',
    role: '{{ old('role', 'Admin') }}',
    openCreate() {
        this.isEdit = false;
        this.formAction = '{{ route('admins.store') }}';
        this.name = '';
        this.email = '';
        this.role = 'Admin';
        this.openModal = true;
    },
    openEdit(item) {
        this.isEdit = true;
        this.formAction = '/superadmin/admins/' + item.id;
        this.name = item.name || '';
        this.email = item.email || '';
        this.role = item.role || 'Admin';
        this.openModal = true;
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

    <!-- Admins Table Card -->
    <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-2.5">
                <h2 class="text-base font-bold text-slate-800">System Administrators</h2>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-500 font-semibold font-mono">{{ $admins->count() }}</span>
            </div>
            <button 
                @click="openCreate()" 
                type="button" 
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition-all shadow-sm shadow-blue-500/20 cursor-pointer flex items-center gap-1.5"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/>
                </svg>
                <span>Add Admin</span>
            </button>
        </div>

        @if($admins->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-200 py-16 flex flex-col items-center justify-center text-center">
                <p class="text-sm font-semibold text-slate-500">No administrators created yet.</p>
                <button 
                    @click="openCreate()"
                    class="text-xs text-blue-600 font-bold mt-2 hover:underline cursor-pointer"
                >
                    + Add New Admin
                </button>
            </div>
        @else
            <div class="overflow-x-auto rounded-xl border border-slate-200/80">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-[11px] font-bold tracking-wider">
                        <tr>
                            <th class="px-5 py-3.5">#</th>
                            <th class="px-5 py-3.5">Admin Name</th>
                            <th class="px-5 py-3.5">Email</th>
                            <th class="px-5 py-3.5">Role</th>
                            <th class="px-5 py-3.5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($admins as $index => $admin)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="px-5 py-4 text-xs font-semibold text-slate-400">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs shrink-0">
                                            {{ strtoupper(substr($admin->name, 0, 1)) }}
                                        </div>
                                        <span class="font-bold text-slate-900 text-sm">
                                            {{ $admin->name }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-slate-600 text-sm font-medium">
                                    {{ $admin->email }}
                                </td>
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
                                <td class="px-5 py-4 text-right">
                                    <button 
                                        @click='openEdit(@json($admin))' 
                                        type="button" 
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-blue-600 hover:text-blue-700 bg-white hover:bg-blue-50 border border-slate-200 hover:border-blue-300 transition-all cursor-pointer shadow-2xs"
                                        title="Edit Admin"
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

    <!-- Modal Dialog for Creating New Admin -->
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-bold text-slate-900" id="modal-title" x-text="isEdit ? 'Edit Admin' : 'Create New Admin'">
                            Create New Admin
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
                        <!-- Admin Name -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Full Name <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="name" 
                                x-model="name"
                                value="{{ old('name') }}"
                                placeholder="e.g. John Doe" 
                                class="w-full px-3.5 py-2.5 rounded-xl border @error('name') border-rose-300 bg-rose-50/30 @else border-slate-300 @enderror text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all"
                                required
                            />
                            @error('name')
                                <p x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Admin Email -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Email Address <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="email" 
                                name="email" 
                                x-model="email"
                                value="{{ old('email') }}"
                                placeholder="e.g. admin@example.com" 
                                class="w-full px-3.5 py-2.5 rounded-xl border @error('email') border-rose-300 bg-rose-50/30 @else border-slate-300 @enderror text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all"
                                required
                            />
                            @error('email')
                                <p x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Password <span x-show="!isEdit" class="text-rose-500">*</span> <span x-show="isEdit" class="text-slate-400 font-normal normal-case">(leave blank to keep current)</span>
                            </label>
                            <input 
                                type="password" 
                                name="password" 
                                :required="!isEdit"
                                placeholder="Minimum 6 characters" 
                                class="w-full px-3.5 py-2.5 rounded-xl border @error('password') border-rose-300 bg-rose-50/30 @else border-slate-300 @enderror text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all"
                            />
                            @error('password')
                                <p x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Role -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Role <span class="text-rose-500">*</span>
                            </label>
                            <select 
                                name="role" 
                                x-model="role"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm text-slate-900 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all bg-white"
                                required
                            >
                                <option value="Admin" {{ old('role', 'Admin') === 'Admin' ? 'selected' : '' }}>Admin</option>
                                <option value="Super Admin" {{ old('role') === 'Super Admin' ? 'selected' : '' }}>Super Admin</option>
                            </select>
                            @error('role')
                                <p x-data="{ show: true }" x-init="setTimeout(() => show = false, 5000)" x-show="show" class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                            @enderror
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
                            <span x-text="isEdit ? 'Update Admin' : 'Save Admin'">Save Admin</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
