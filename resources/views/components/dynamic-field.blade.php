@props([
    'field',
    'comp' => null,
    'component' => null,
    'subComp' => null,
    'value' => null,
    'filePath' => null,
    'disabledCondition' => null,
    'instanceIndex' => null,
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
@endphp

<div class="space-y-1.5">
    <!-- Field Label -->
    <div class="flex items-center justify-between">
        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
            {{ $field->field_label }}
            @if($field->is_required)
                <span class="text-rose-500 font-bold">*</span>
            @endif
        </label>
        <span class="text-[10px] font-mono text-slate-400 bg-slate-100 px-1.5 py-0.5 rounded">
            {{ $field->field_type }}
        </span>
    </div>

    <!-- Dynamic Input rendering by field_type -->
    @switch($field->field_type)
        @case('text')
            <input 
                type="text" 
                name="{{ $inputName }}" 
                value="{{ $currentVal }}" 
                placeholder="{{ $field->placeholder ?: 'Enter ' . strtolower($field->field_label) . '...' }}"
                {!! $disabledAttr !!}
                class="w-full px-3.5 py-2.5 rounded-xl border @if($hasError) border-rose-300 bg-rose-50/20 @else border-slate-300 bg-white @endif text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all font-medium"
            >
            @break

        @case('password')
            <input 
                type="password" 
                name="{{ $inputName }}" 
                value="{{ $currentVal }}" 
                placeholder="{{ $field->placeholder ?: '••••••••' }}"
                {!! $disabledAttr !!}
                class="w-full px-3.5 py-2.5 rounded-xl border @if($hasError) border-rose-300 bg-rose-50/20 @else border-slate-300 bg-white @endif text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all font-mono"
            >
            @break

        @case('email')
            <input 
                type="email" 
                name="{{ $inputName }}" 
                value="{{ $currentVal }}" 
                placeholder="{{ $field->placeholder ?: 'name@example.com' }}"
                {!! $disabledAttr !!}
                class="w-full px-3.5 py-2.5 rounded-xl border @if($hasError) border-rose-300 bg-rose-50/20 @else border-slate-300 bg-white @endif text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all"
            >
            @break

        @case('search')
            <input 
                type="search" 
                name="{{ $inputName }}" 
                value="{{ $currentVal }}" 
                placeholder="{{ $field->placeholder ?: 'Search ' . strtolower($field->field_label) . '...' }}"
                {!! $disabledAttr !!}
                class="w-full px-3.5 py-2.5 rounded-xl border @if($hasError) border-rose-300 bg-rose-50/20 @else border-slate-300 bg-white @endif text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all"
            >
            @break

        @case('tel')
            <input 
                type="tel" 
                name="{{ $inputName }}" 
                value="{{ $currentVal }}" 
                placeholder="{{ $field->placeholder ?: '+1 (555) 000-0000' }}"
                {!! $disabledAttr !!}
                class="w-full px-3.5 py-2.5 rounded-xl border @if($hasError) border-rose-300 bg-rose-50/20 @else border-slate-300 bg-white @endif text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all font-mono text-xs"
            >
            @break

        @case('textarea')
            <textarea 
                name="{{ $inputName }}" 
                rows="4" 
                placeholder="{{ $field->placeholder ?: 'Enter ' . strtolower($field->field_label) . '...' }}"
                {!! $disabledAttr !!}
                class="w-full px-3.5 py-2.5 rounded-xl border @if($hasError) border-rose-300 bg-rose-50/20 @else border-slate-300 bg-white @endif text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all leading-relaxed"
            >{{ $currentVal }}</textarea>
            @break

        @case('url')
            <input 
                type="text" 
                name="{{ $inputName }}" 
                value="{{ $currentVal }}" 
                placeholder="{{ $field->placeholder ?: 'e.g. #contact, /about, or https://example.com' }}"
                {!! $disabledAttr !!}
                class="w-full px-3.5 py-2.5 rounded-xl border @if($hasError) border-rose-300 bg-rose-50/20 @else border-slate-300 bg-white @endif text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all font-mono text-xs"
            >
            @break

        @case('number')
            <input 
                type="number" 
                step="any"
                name="{{ $inputName }}" 
                value="{{ $currentVal }}" 
                placeholder="{{ $field->placeholder ?: '0' }}"
                {!! $disabledAttr !!}
                class="w-full px-3.5 py-2.5 rounded-xl border @if($hasError) border-rose-300 bg-rose-50/20 @else border-slate-300 bg-white @endif text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all font-medium"
            >
            @break

        @case('range')
            <div class="flex items-center gap-3" x-data="{ rVal: '{{ $currentVal ?: ($field->default_value ?: 50) }}' }">
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
                <span class="w-12 text-center text-xs font-bold font-mono px-2 py-1 bg-slate-100 rounded-md border border-slate-200 text-slate-700" x-text="rVal"></span>
            </div>
            @break

        @case('color')
            <div class="flex items-center gap-3">
                <input 
                    type="color" 
                    name="{{ $inputName }}" 
                    value="{{ $currentVal ?: ($field->default_value ?: '#3b82f6') }}" 
                    {!! $disabledAttr !!}
                    class="w-10 h-10 p-0.5 rounded-xl border border-slate-300 cursor-pointer"
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
                class="w-full px-3.5 py-2.5 rounded-xl border @if($hasError) border-rose-300 bg-rose-50/20 @else border-slate-300 bg-white @endif text-sm text-slate-900 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all font-mono"
            >
            @break

        @case('time')
            <input 
                type="time" 
                name="{{ $inputName }}" 
                value="{{ $currentVal }}" 
                {!! $disabledAttr !!}
                class="w-full px-3.5 py-2.5 rounded-xl border @if($hasError) border-rose-300 bg-rose-50/20 @else border-slate-300 bg-white @endif text-sm text-slate-900 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all font-mono"
            >
            @break

        @case('datetime-local')
            <input 
                type="datetime-local" 
                name="{{ $inputName }}" 
                value="{{ $currentVal }}" 
                {!! $disabledAttr !!}
                class="w-full px-3.5 py-2.5 rounded-xl border @if($hasError) border-rose-300 bg-rose-50/20 @else border-slate-300 bg-white @endif text-sm text-slate-900 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all font-mono"
            >
            @break

        @case('month')
            <input 
                type="month" 
                name="{{ $inputName }}" 
                value="{{ $currentVal }}" 
                {!! $disabledAttr !!}
                class="w-full px-3.5 py-2.5 rounded-xl border @if($hasError) border-rose-300 bg-rose-50/20 @else border-slate-300 bg-white @endif text-sm text-slate-900 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all font-mono"
            >
            @break

        @case('week')
            <input 
                type="week" 
                name="{{ $inputName }}" 
                value="{{ $currentVal }}" 
                {!! $disabledAttr !!}
                class="w-full px-3.5 py-2.5 rounded-xl border @if($hasError) border-rose-300 bg-rose-50/20 @else border-slate-300 bg-white @endif text-sm text-slate-900 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all font-mono"
            >
            @break

        @case('select')
            <select 
                name="{{ $inputName }}" 
                {!! $disabledAttr !!}
                class="w-full px-3.5 py-2.5 rounded-xl border @if($hasError) border-rose-300 bg-rose-50/20 @else border-slate-300 bg-white @endif text-sm text-slate-900 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all"
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
                    class="w-full px-3.5 py-2.5 rounded-xl border @if($hasError) border-rose-300 bg-rose-50/20 @else border-slate-300 bg-white @endif text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all font-medium"
                >
                <div class="flex items-center gap-2">
                    <span class="text-[11px] text-slate-400">Button Preview:</span>
                    <button type="button" class="px-4 py-1.5 rounded-lg text-xs font-bold text-white bg-blue-600 shadow-2xs select-none cursor-default">
                        {{ $currentVal ?: ($field->default_value ?: $field->field_label) }}
                    </button>
                    <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-slate-100 text-slate-500 border border-slate-200">&lt;button type="{{ $field->field_type }}"&gt;</span>
                </div>
            </div>
            @break

        @case('image')
            <div class="space-y-2">
                <input 
                    type="file" 
                    accept="image/*" 
                    name="{{ $fileInputName }}" 
                    {!! $disabledAttr !!}
                    class="w-full text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer rounded-xl border @if($hasError) border-rose-300 bg-rose-50/20 @else border-slate-300 bg-white @endif p-1.5"
                >
                @if($currentFilePath && file_exists(public_path($currentFilePath)))
                    <div class="flex items-center gap-2.5 p-2 bg-slate-50 rounded-xl border border-slate-200">
                        <img src="{{ asset($currentFilePath) }}" alt="Preview" class="w-10 h-10 object-cover rounded-lg border border-slate-200 shadow-2xs">
                        <div class="text-xs min-w-0">
                            <span class="font-bold text-slate-700 block truncate max-w-[220px]">{{ basename($currentFilePath) }}</span>
                            <span class="text-[10px] text-emerald-600 font-semibold">Current image saved</span>
                        </div>
                    </div>
                @endif
            </div>
            @break

        @case('video')
            <div class="space-y-2">
                <input 
                    type="file" 
                    accept="video/*" 
                    name="{{ $fileInputName }}" 
                    {!! $disabledAttr !!}
                    class="w-full text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 cursor-pointer rounded-xl border @if($hasError) border-rose-300 bg-rose-50/20 @else border-slate-300 bg-white @endif p-1.5"
                >
                @if($currentFilePath && file_exists(public_path($currentFilePath)))
                    <div class="flex items-center gap-2.5 p-2 bg-slate-50 rounded-xl border border-slate-200">
                        <video src="{{ asset($currentFilePath) }}" class="w-14 h-9 object-cover rounded-lg border border-slate-200 shadow-2xs"></video>
                        <div class="text-xs min-w-0">
                            <span class="font-bold text-slate-700 block truncate max-w-[220px]">{{ basename($currentFilePath) }}</span>
                            <span class="text-[10px] text-purple-600 font-semibold">Current video saved</span>
                        </div>
                    </div>
                @endif
            </div>
            @break

        @case('file')
            <div class="space-y-2">
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

    <!-- Help Text -->
    @if($field->help_text)
        <p class="text-[11px] text-slate-400">{{ $field->help_text }}</p>
    @endif

    <!-- Validation Error Feedback -->
    @if($errorsBag->has($errorKey))
        <p class="text-rose-500 text-xs font-semibold">{{ $errorsBag->first($errorKey) }}</p>
    @endif
    @if($errorsBag->has($fileErrorKey))
        <p class="text-rose-500 text-xs font-semibold">{{ $errorsBag->first($fileErrorKey) }}</p>
    @endif
</div>
