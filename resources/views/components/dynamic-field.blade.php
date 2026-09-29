@props([
    'field',
    'comp' => null,
    'component' => null,
    'subComp' => null,
    'value' => null,
    'filePath' => null,
    'disabledCondition' => null,
    'instanceIndex' => null,
    'hideLabel' => false,
    'class' => '',
])

@php
    $comp = $comp ?? $component;
    $isSub = !is_null($subComp);
    $hasInstance = !is_null($instanceIndex);
    
    // Naming for normal fields vs file fields
    if ($isSub && $comp) {
        if ($hasInstance) {
            $inputName = "components[{$comp->id}][subcomponents][{$subComp->id}][instances][{$instanceIndex}][fields][{$field->id}]";
            $fileInputName = "components[{$comp->id}][subcomponents][{$subComp->id}][instances][{$instanceIndex}][files][{$field->id}]";
            $errorKey = "components.{$comp->id}.subcomponents.{$subComp->id}.instances.{$instanceIndex}.fields.{$field->id}";
            $fileErrorKey = "components.{$comp->id}.subcomponents.{$subComp->id}.instances.{$instanceIndex}.files.{$field->id}";
            $currentVal = old("components.{$comp->id}.subcomponents.{$subComp->id}.instances.{$instanceIndex}.fields.{$field->id}", $value ?? $field->default_value);
        } else {
            $inputName = "components[{$comp->id}][subcomponents][{$subComp->id}][fields][{$field->id}]";
            $fileInputName = "components[{$comp->id}][subcomponents][{$subComp->id}][files][{$field->id}]";
            $errorKey = "components.{$comp->id}.subcomponents.{$subComp->id}.fields.{$field->id}";
            $fileErrorKey = "components.{$comp->id}.subcomponents.{$subComp->id}.files.{$field->id}";
            $currentVal = old("components.{$comp->id}.subcomponents.{$subComp->id}.fields.{$field->id}", $value ?? $field->default_value);
        }
    } elseif ($comp) {
        if ($hasInstance) {
            $inputName = "components[{$comp->id}][instances][{$instanceIndex}][fields][{$field->id}]";
            $fileInputName = "components[{$comp->id}][instances][{$instanceIndex}][files][{$field->id}]";
            $errorKey = "components.{$comp->id}.instances.{$instanceIndex}.fields.{$field->id}";
            $fileErrorKey = "components.{$comp->id}.instances.{$instanceIndex}.files.{$field->id}";
            $currentVal = old("components.{$comp->id}.instances.{$instanceIndex}.fields.{$field->id}", $value ?? $field->default_value);
        } else {
            $inputName = "components[{$comp->id}][fields][{$field->id}]";
            $fileInputName = "components[{$comp->id}][files][{$field->id}]";
            $errorKey = "components.{$comp->id}.fields.{$field->id}";
            $fileErrorKey = "components.{$comp->id}.files.{$field->id}";
            $currentVal = old("components.{$comp->id}.fields.{$field->id}", $value ?? $field->default_value);
        }
    } else {
        $inputName = "fields[{$field->field_name}]";
        $fileInputName = "files[{$field->field_name}]";
        $errorKey = "fields.{$field->field_name}";
        $fileErrorKey = "files.{$field->field_name}";
        $currentVal = old("fields.{$field->field_name}", $value ?? $field->default_value);
    }

    $currentFilePath = $filePath;
    $disabledAttr = $disabledCondition ? ':disabled="' . $disabledCondition . '"' : '';
    $errorsBag = isset($errors) ? $errors : new \Illuminate\Support\ViewErrorBag();
    $hasError = $errorsBag->has($errorKey) || $errorsBag->has($fileErrorKey);

    $typeBadgeClasses = [
        'text' => 'bg-blue-50 text-blue-700 border-blue-200/70',
        'textarea' => 'bg-amber-50 text-amber-700 border-amber-200/70',
        'image' => 'bg-emerald-50 text-emerald-700 border-emerald-200/70',
        'video' => 'bg-purple-50 text-purple-700 border-purple-200/70',
        'url' => 'bg-indigo-50 text-indigo-700 border-indigo-200/70',
        'select' => 'bg-violet-50 text-violet-700 border-violet-200/70',
        'number' => 'bg-sky-50 text-sky-700 border-sky-200/70',
        'date' => 'bg-rose-50 text-rose-700 border-rose-200/70',
        'color' => 'bg-pink-50 text-pink-700 border-pink-200/70',
        'checkbox' => 'bg-teal-50 text-teal-700 border-teal-200/70',
        'radio' => 'bg-teal-50 text-teal-700 border-teal-200/70',
        'email' => 'bg-cyan-50 text-cyan-700 border-cyan-200/70',
        'tel' => 'bg-emerald-50 text-emerald-700 border-emerald-200/70',
        'password' => 'bg-slate-100 text-slate-700 border-slate-200',
    ];
    $badgeStyle = $typeBadgeClasses[$field->field_type] ?? 'bg-slate-100 text-slate-600 border-slate-200';
    $fName = strtolower($field->field_name ?? '');
    $isHalfField = in_array($fName, ['button_text', 'button_url', 'anchor_text', 'anchor_url']) && !$hideLabel;
    $fieldColSpan = $class ?: ($isHalfField ? 'sm:col-span-1' : 'sm:col-span-2');
@endphp

<div class="space-y-1.5 {{ $fieldColSpan }}">
    @if(!$hideLabel)
        <!-- Field Label -->
        <div class="flex items-center justify-between mb-1">
            <label class="block text-xs font-bold text-slate-700 tracking-wide">
                {{ $field->field_label }}
                @if($field->is_required)
                    <span class="text-rose-500 font-bold">*</span>
                @endif
            </label>
            <span class="text-[10px] font-bold uppercase tracking-wider {{ $badgeStyle }} px-2 py-0.5 rounded-full border">
                {{ $field->field_type }}
            </span>
        </div>
    @endif

    <!-- Dynamic Input rendering by field_type -->
    @switch($field->field_type)
        @case('text')
            <input 
                type="text" 
                name="{{ $inputName }}" 
                value="{{ $currentVal }}" 
                placeholder="{{ $field->placeholder ?: 'Enter ' . strtolower($field->field_label) . '...' }}"
                {!! $disabledAttr !!}
                class="w-full px-3.5 py-2.5 rounded-xl border @if($hasError) border-rose-300 bg-rose-50/20 text-rose-900 @else border-slate-200/90 bg-slate-50/50 hover:bg-white focus:bg-white text-slate-800 @endif text-sm placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all font-medium shadow-2xs"
            >
            @break

        @case('password')
            <input 
                type="password" 
                name="{{ $inputName }}" 
                value="{{ $currentVal }}" 
                placeholder="{{ $field->placeholder ?: '••••••••' }}"
                {!! $disabledAttr !!}
                class="w-full px-3.5 py-2.5 rounded-xl border @if($hasError) border-rose-300 bg-rose-50/20 text-rose-900 @else border-slate-200/90 bg-slate-50/50 hover:bg-white focus:bg-white text-slate-800 @endif text-sm placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all font-mono"
            >
            @break

        @case('email')
            <input 
                type="email" 
                name="{{ $inputName }}" 
                value="{{ $currentVal }}" 
                placeholder="{{ $field->placeholder ?: 'name@example.com' }}"
                {!! $disabledAttr !!}
                class="w-full px-3.5 py-2.5 rounded-xl border @if($hasError) border-rose-300 bg-rose-50/20 text-rose-900 @else border-slate-200/90 bg-slate-50/50 hover:bg-white focus:bg-white text-slate-800 @endif text-sm placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all font-medium shadow-2xs"
            >
            @break          @case('search')
            <input 
                type="search" 
                name="{{ $inputName }}" 
                value="{{ $currentVal }}" 
                placeholder="{{ $field->placeholder ?: 'Search ' . strtolower($field->field_label) . '...' }}"
                {!! $disabledAttr !!}
                class="w-full px-3.5 py-2.5 rounded-xl border @if($hasError) border-rose-300 bg-rose-50/20 text-rose-900 @else border-slate-200/90 bg-slate-50/50 hover:bg-white focus:bg-white text-slate-800 @endif text-sm placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all font-medium shadow-2xs"
            >
            @break

        @case('tel')
            <input 
                type="tel" 
                name="{{ $inputName }}" 
                value="{{ $currentVal }}" 
                placeholder="{{ $field->placeholder ?: '+1 (555) 000-0000' }}"
                {!! $disabledAttr !!}
                class="w-full px-3.5 py-2.5 rounded-xl border @if($hasError) border-rose-300 bg-rose-50/20 text-rose-900 @else border-slate-200/90 bg-slate-50/50 hover:bg-white focus:bg-white text-slate-800 @endif text-sm placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all font-mono text-xs shadow-2xs"
            >
            @break

        @case('textarea')
            <textarea 
                name="{{ $inputName }}" 
                rows="3" 
                placeholder="{{ $field->placeholder ?: 'Enter ' . strtolower($field->field_label) . '...' }}"
                {!! $disabledAttr !!}
                class="w-full px-3.5 py-2.5 rounded-xl border @if($hasError) border-rose-300 bg-rose-50/20 text-rose-900 @else border-slate-200/90 bg-slate-50/50 hover:bg-white focus:bg-white text-slate-800 @endif text-sm placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all font-medium shadow-2xs leading-relaxed"
            >{{ $currentVal }}</textarea>
            @break

        @case('url')
            <div class="relative rounded-xl shadow-2xs">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                </div>
                <input 
                    type="text" 
                    name="{{ $inputName }}" 
                    value="{{ $currentVal }}" 
                    placeholder="{{ $field->placeholder ?: 'e.g. #contact, /about, or https://example.com' }}"
                    {!! $disabledAttr !!}
                    class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border @if($hasError) border-rose-300 bg-rose-50/20 text-rose-900 @else border-slate-200/90 bg-slate-50/50 hover:bg-white focus:bg-white text-slate-800 @endif text-sm placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all font-mono text-xs"
                >
            </div>
            @break

        @case('number')
            <input 
                type="number" 
                step="any" 
                name="{{ $inputName }}" 
                value="{{ $currentVal }}" 
                placeholder="{{ $field->placeholder ?: '0' }}"
                {!! $disabledAttr !!}
                class="w-full px-3.5 py-2.5 rounded-xl border @if($hasError) border-rose-300 bg-rose-50/20 text-rose-900 @else border-slate-200/90 bg-slate-50/50 hover:bg-white focus:bg-white text-slate-800 @endif text-sm placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all font-medium shadow-2xs"
            >
            @break

        @case('range')
            <div class="flex items-center gap-3 p-2 bg-slate-50/60 rounded-xl border border-slate-200/80" x-data="{ rVal: '{{ $currentVal ?: ($field->default_value ?: 50) }}' }">
                <input 
                    type="range" 
                    min="0" 
                    max="100" 
                    step="1" 
                    name="{{ $inputName }}" 
                    x-model="rVal" 
                    {!! $disabledAttr !!}
                    class="w-full h-2 bg-slate-200 rounded-lg appearance-none cursor-pointer accent-blue-600"
                >
                <span class="w-12 text-center text-xs font-bold font-mono px-2 py-1 bg-white rounded-lg border border-slate-200 text-slate-700 shadow-2xs" x-text="rVal"></span>
            </div>
            @break

        @case('color')
            <div class="flex items-center gap-3 p-2 bg-slate-50/60 rounded-xl border border-slate-200/80">
                <input 
                    type="color" 
                    name="{{ $inputName }}" 
                    value="{{ $currentVal ?: ($field->default_value ?: '#3b82f6') }}" 
                    {!! $disabledAttr !!}
                    class="w-10 h-10 p-0.5 rounded-xl border border-slate-300 cursor-pointer shadow-2xs"
                >
                <span class="text-xs font-mono text-slate-700 font-bold uppercase">{{ $currentVal ?: ($field->default_value ?: '#3b82f6') }}</span>
            </div>
            @break

        @case('date')
            <input 
                type="date" 
                name="{{ $inputName }}" 
                value="{{ $currentVal }}" 
                {!! $disabledAttr !!}
                class="w-full px-3.5 py-2.5 rounded-xl border @if($hasError) border-rose-300 bg-rose-50/20 text-rose-900 @else border-slate-200/90 bg-slate-50/50 hover:bg-white focus:bg-white text-slate-800 @endif text-sm focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all font-mono shadow-2xs"
            >
            @break

        @case('time')
            <input 
                type="time" 
                name="{{ $inputName }}" 
                value="{{ $currentVal }}" 
                {!! $disabledAttr !!}
                class="w-full px-3.5 py-2.5 rounded-xl border @if($hasError) border-rose-300 bg-rose-50/20 text-rose-900 @else border-slate-200/90 bg-slate-50/50 hover:bg-white focus:bg-white text-slate-800 @endif text-sm focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all font-mono shadow-2xs"
            >
            @break

        @case('datetime-local')
            <input 
                type="datetime-local" 
                name="{{ $inputName }}" 
                value="{{ $currentVal }}" 
                {!! $disabledAttr !!}
                class="w-full px-3.5 py-2.5 rounded-xl border @if($hasError) border-rose-300 bg-rose-50/20 text-rose-900 @else border-slate-200/90 bg-slate-50/50 hover:bg-white focus:bg-white text-slate-800 @endif text-sm focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all font-mono shadow-2xs"
            >
            @break

        @case('month')
            <input 
                type="month" 
                name="{{ $inputName }}" 
                value="{{ $currentVal }}" 
                {!! $disabledAttr !!}
                class="w-full px-3.5 py-2.5 rounded-xl border @if($hasError) border-rose-300 bg-rose-50/20 text-rose-900 @else border-slate-200/90 bg-slate-50/50 hover:bg-white focus:bg-white text-slate-800 @endif text-sm focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all font-mono shadow-2xs"
            >
            @break

        @case('week')
            <input 
                type="week" 
                name="{{ $inputName }}" 
                value="{{ $currentVal }}" 
                {!! $disabledAttr !!}
                class="w-full px-3.5 py-2.5 rounded-xl border @if($hasError) border-rose-300 bg-rose-50/20 text-rose-900 @else border-slate-200/90 bg-slate-50/50 hover:bg-white focus:bg-white text-slate-800 @endif text-sm focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all font-mono shadow-2xs"
            >
            @break

        @case('select')
            <select 
                name="{{ $inputName }}" 
                {!! $disabledAttr !!}
                class="w-full px-3.5 py-2.5 rounded-xl border @if($hasError) border-rose-300 bg-rose-50/20 text-rose-900 @else border-slate-200/90 bg-slate-50/50 hover:bg-white focus:bg-white text-slate-800 @endif text-sm focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all font-medium shadow-2xs cursor-pointer"
            >
                <option value="">{{ $field->placeholder ?: '-- Select ' . $field->field_label . ' --' }}</option>
                @foreach($field->formatted_options as $opt)
                    <option value="{{ $opt['value'] }}" {{ (string)$currentVal === (string)$opt['value'] ? 'selected' : '' }}>
                        {{ $opt['label'] }}
                    </option>
                @endforeach
            </select>
            @break

        @case('radio')
            <div class="space-y-2 pt-1">
                @forelse($field->formatted_options as $opt)
                    <label class="flex items-center gap-2.5 cursor-pointer select-none">
                        <input 
                            type="radio" 
                            name="{{ $inputName }}" 
                            value="{{ $opt['value'] }}" 
                            {{ (string)$currentVal === (string)$opt['value'] ? 'checked' : '' }}
                            {!! $disabledAttr !!}
                            class="w-4 h-4 text-blue-600 border-slate-300 focus:ring-blue-500 cursor-pointer"
                        >
                        <span class="text-xs font-semibold text-slate-700">{{ $opt['label'] }}</span>
                    </label>
                @empty
                    <p class="text-[11px] text-slate-400 italic">No radio options configured</p>
                @endforelse
            </div>
            @break

        @case('checkbox')
            <div class="pt-1">
                <input type="hidden" name="{{ $inputName }}" value="0">
                <label class="inline-flex items-center gap-2.5 cursor-pointer select-none">
                    <input 
                        type="checkbox" 
                        name="{{ $inputName }}" 
                        value="1" 
                        {!! $disabledAttr !!}
                        {{ ($currentVal === '1' || $currentVal === 1 || $currentVal === true || (empty($currentVal) && $field->default_value == '1')) ? 'checked' : '' }}
                        class="w-4 h-4 rounded text-blue-600 border-slate-300 focus:ring-blue-500 focus:ring-2 cursor-pointer"
                    >
                    <span class="text-xs font-semibold text-slate-700">
                        {{ $field->placeholder ?: 'Active / Yes' }}
                    </span>
                </label>
            </div>
            @break

        @case('hidden')
            <div class="p-2.5 rounded-xl bg-slate-100 border border-slate-200 text-xs font-mono text-slate-600 flex items-center justify-between">
                <span>Hidden Field (<code class="text-slate-800">{{ $field->field_name }}</code>):</span>
                <input type="hidden" name="{{ $inputName }}" value="{{ $currentVal ?: $field->default_value }}">
                <span class="font-bold text-slate-800">{{ $currentVal ?: ($field->default_value ?: '(empty)') }}</span>
            </div>
            @break

        @case('button')
        @case('submit')
        @case('reset')
            <div class="space-y-2">
                <input 
                    type="text" 
                    name="{{ $inputName }}" 
                    value="{{ $currentVal ?: ($field->default_value ?: $field->field_label) }}" 
                    placeholder="Enter button label..."
                    {!! $disabledAttr !!}
                    class="w-full px-3.5 py-2.5 rounded-xl border @if($hasError) border-rose-300 bg-rose-50/20 text-rose-900 @else border-slate-200/90 bg-slate-50/50 hover:bg-white focus:bg-white text-slate-800 @endif text-sm placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition-all font-medium shadow-2xs"
                >
                <div class="flex items-center gap-2">
                    <span class="text-[11px] text-slate-400">Preview:</span>
                    <button type="button" class="px-4 py-1.5 rounded-lg text-xs font-bold text-white bg-blue-600 shadow-2xs select-none cursor-default">
                        {{ $currentVal ?: ($field->default_value ?: $field->field_label) }}
                    </button>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-slate-100 text-slate-500 border border-slate-200">&lt;button type="{{ $field->field_type }}"&gt;</span>
                </div>
            </div>
            @break

        @case('image')
            <div class="space-y-2.5" x-data="{ fileName: '' }">
                @if($currentFilePath)
                    <input type="hidden" name="{{ str_replace('[files]', '[existing_files]', $fileInputName) }}" value="{{ $currentFilePath }}">
                @endif

                @if($currentFilePath && file_exists(public_path($currentFilePath)))
                    <div class="flex items-center justify-between p-3 bg-slate-50/80 rounded-xl border border-slate-200 shadow-2xs">
                        <div class="flex items-center gap-3 min-w-0">
                            <img src="{{ asset($currentFilePath) }}" alt="Preview" class="w-12 h-12 object-cover rounded-lg border border-slate-200 shadow-2xs shrink-0 bg-white">
                            <div class="text-xs min-w-0">
                                <span class="font-bold text-slate-800 block truncate max-w-[240px]">{{ basename($currentFilePath) }}</span>
                                <span class="inline-flex items-center gap-1 text-[10px] text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200/60 mt-0.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Current Saved Image
                                </span>
                            </div>
                        </div>
                        <label class="px-3.5 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 font-bold text-xs cursor-pointer shadow-2xs transition-colors shrink-0">
                            <span>Change Image</span>
                            <input 
                                type="file" 
                                accept="image/*" 
                                name="{{ $fileInputName }}" 
                                {!! $disabledAttr !!}
                                class="hidden"
                                @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                            >
                        </label>
                    </div>
                    <div x-show="fileName" x-transition class="p-2 bg-blue-50/80 rounded-lg border border-blue-200 text-xs text-blue-700 flex items-center justify-between">
                        <span class="truncate">Selected new image: <b x-text="fileName"></b></span>
                        <span class="text-[10px] font-bold text-blue-600">Pending Save</span>
                    </div>
                @else
                    <label class="flex items-center justify-between p-3.5 border-2 border-dashed border-slate-300 hover:border-emerald-400 bg-slate-50/50 hover:bg-emerald-50/30 rounded-xl cursor-pointer transition-all group">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-lg bg-white border border-slate-200 text-slate-500 group-hover:text-emerald-600 group-hover:border-emerald-300 flex items-center justify-center shadow-2xs transition-colors shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-700 group-hover:text-emerald-700">Choose Image File</p>
                                <p class="text-[11px] text-slate-400 font-medium">PNG, JPG, WEBP, SVG up to 5MB</p>
                            </div>
                        </div>
                        <span class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-xs font-bold text-slate-700 shadow-2xs group-hover:border-emerald-300 group-hover:text-emerald-700 transition-colors shrink-0">
                            Browse
                        </span>
                        <input 
                            type="file" 
                            accept="image/*" 
                            name="{{ $fileInputName }}" 
                            {!! $disabledAttr !!}
                            class="hidden"
                            @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                        >
                    </label>
                    <div x-show="fileName" x-transition class="p-2 bg-blue-50/80 rounded-lg border border-blue-200 text-xs text-blue-700 flex items-center justify-between">
                        <span class="truncate">Selected file: <b x-text="fileName"></b></span>
                        <span class="text-[10px] font-bold text-blue-600">Ready</span>
                    </div>
                @endif
            </div>
            @break

        @case('video')
            <div class="space-y-2.5" x-data="{ fileName: '' }">
                @if($currentFilePath)
                    <input type="hidden" name="{{ str_replace('[files]', '[existing_files]', $fileInputName) }}" value="{{ $currentFilePath }}">
                @endif

                @if($currentFilePath && file_exists(public_path($currentFilePath)))
                    <div class="flex items-center justify-between p-3 bg-slate-50/80 rounded-xl border border-slate-200 shadow-2xs">
                        <div class="flex items-center gap-3 min-w-0">
                            <video src="{{ asset($currentFilePath) }}" class="w-14 h-10 object-cover rounded-lg border border-slate-200 shadow-2xs shrink-0 bg-black"></video>
                            <div class="text-xs min-w-0">
                                <span class="font-bold text-slate-800 block truncate max-w-[240px]">{{ basename($currentFilePath) }}</span>
                                <span class="inline-flex items-center gap-1 text-[10px] text-purple-700 font-bold bg-purple-50 px-2 py-0.5 rounded-full border border-purple-200/60 mt-0.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                                    Current Saved Video
                                </span>
                            </div>
                        </div>
                        <label class="px-3.5 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 font-bold text-xs cursor-pointer shadow-2xs transition-colors shrink-0">
                            <span>Change Video</span>
                            <input 
                                type="file" 
                                accept="video/*" 
                                name="{{ $fileInputName }}" 
                                {!! $disabledAttr !!}
                                class="hidden"
                                @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                            >
                        </label>
                    </div>
                    <div x-show="fileName" x-transition class="p-2 bg-purple-50/80 rounded-lg border border-purple-200 text-xs text-purple-700 flex items-center justify-between">
                        <span class="truncate">Selected new video: <b x-text="fileName"></b></span>
                        <span class="text-[10px] font-bold text-purple-600">Pending Save</span>
                    </div>
                @else
                    <label class="flex items-center justify-between p-3.5 border-2 border-dashed border-slate-300 hover:border-purple-400 bg-slate-50/50 hover:bg-purple-50/30 rounded-xl cursor-pointer transition-all group">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-lg bg-white border border-slate-200 text-slate-500 group-hover:text-purple-600 group-hover:border-purple-300 flex items-center justify-center shadow-2xs transition-colors shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-700 group-hover:text-purple-700">Choose Video File</p>
                                <p class="text-[11px] text-slate-400 font-medium">MP4, WEBM up to 20MB</p>
                            </div>
                        </div>
                        <span class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 text-xs font-bold text-slate-700 shadow-2xs group-hover:border-purple-300 group-hover:text-purple-700 transition-colors shrink-0">
                            Browse
                        </span>
                        <input 
                            type="file" 
                            accept="video/*" 
                            name="{{ $fileInputName }}" 
                            {!! $disabledAttr !!}
                            class="hidden"
                            @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''"
                        >
                    </label>
                    <div x-show="fileName" x-transition class="p-2 bg-purple-50/80 rounded-lg border border-purple-200 text-xs text-purple-700 flex items-center justify-between">
                        <span class="truncate">Selected video: <b x-text="fileName"></b></span>
                        <span class="text-[10px] font-bold text-purple-600">Ready</span>
                    </div>
                @endif
            </div>
            @break          @break

        @case('file')
            <div class="space-y-2">
                @if($currentFilePath)
                    <input type="hidden" name="{{ str_replace('[files]', '[existing_files]', $fileInputName) }}" value="{{ $currentFilePath }}">
                @endif
                <input 
                    type="file" 
                    name="{{ $fileInputName }}" 
                    {!! $disabledAttr !!}
                    class="w-full text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer rounded-xl border @if($hasError) border-rose-300 bg-rose-50/20 @else border-slate-300 bg-white @endif p-1.5"
                >
                @if($currentFilePath && file_exists(public_path($currentFilePath)))
                    <div class="flex items-center gap-2.5 p-2 bg-slate-50 rounded-xl border border-slate-200">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="text-xs min-w-0">
                            <span class="font-bold text-slate-700 block truncate max-w-[220px]">{{ basename($currentFilePath) }}</span>
                            <a href="{{ asset($currentFilePath) }}" target="_blank" class="text-[10px] text-blue-600 hover:underline font-semibold">Download / View</a>
                        </div>
                    </div>
                @endif
            </div>
            @break

        @case('multiple_file')
            <div class="space-y-2">
                <input 
                    type="file" 
                    multiple
                    name="{{ $fileInputName }}[]" 
                    {!! $disabledAttr !!}
                    class="w-full text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer rounded-xl border @if($hasError) border-rose-300 bg-rose-50/20 @else border-slate-300 bg-white @endif p-1.5"
                >
                @if($currentFilePath)
                    <div class="p-2 bg-slate-50 rounded-xl border border-slate-200 text-xs">
                        <span class="font-bold text-slate-700">Uploaded:</span>
                        <span class="font-mono text-slate-600">{{ basename($currentFilePath) }}</span>
                    </div>
                @endif
            </div>
            @break

        @default
            <input 
                type="text" 
                name="{{ $inputName }}" 
                value="{{ $currentVal }}" 
                placeholder="{{ $field->placeholder ?: 'Enter value...' }}"
                {!! $disabledAttr !!}
                class="w-full px-3.5 py-2.5 rounded-xl border @if($hasError) border-rose-300 bg-rose-50/20 @else border-slate-300 bg-white @endif text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all font-medium"
            >
    @endswitch


    <!-- Validation Error Feedback -->
    @if($errorsBag->has($errorKey))
        <p class="text-rose-500 text-xs font-semibold">{{ $errorsBag->first($errorKey) }}</p>
    @endif
    @if($errorsBag->has($fileErrorKey))
        <p class="text-rose-500 text-xs font-semibold">{{ $errorsBag->first($fileErrorKey) }}</p>
    @endif
</div>
