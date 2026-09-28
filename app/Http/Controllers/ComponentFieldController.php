<?php

namespace App\Http\Controllers;

use App\Models\Component;
use App\Models\ComponentField;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ComponentFieldController extends Controller
{
    /**
     * Get all fields for a component (JSON).
     */
    public function index(Component $component)
    {
        $fields = $component->fields()->orderBy('sort_order', 'asc')->get();
        return response()->json([
            'success' => true,
            'fields'  => $fields,
        ]);
    }

    /**
     * Store a newly created field definition.
     */
    public function store(Request $request, Component $component)
    {
        // Sanitize field_name if empty or provided
        $fieldName = $request->input('field_name');
        if (empty($fieldName) && $request->filled('field_label')) {
            $fieldName = Str::snake(Str::slug($request->input('field_label'), '_'));
        } else {
            $fieldName = Str::snake(Str::slug($fieldName, '_'));
        }
        $request->merge(['field_name' => $fieldName]);

        $validated = $request->validate([
            'field_label'      => 'required|string|max:255',
            'field_name'       => 'required|string|max:255',
            'field_type'       => 'required|in:' . implode(',', ComponentField::ALLOWED_TYPES),
            'placeholder'      => 'nullable|string|max:255',
            'default_value'    => 'nullable|string',
            'help_text'        => 'nullable|string|max:500',
            'is_required'      => 'nullable|boolean',
            'is_active'        => 'nullable|boolean',
            'sort_order'       => 'nullable|integer',
            'options'          => 'nullable',
            'validation_rules' => 'nullable|string|max:255',
        ]);

        // Check uniqueness of field_name within this component
        if ($component->fields()->where('field_name', $validated['field_name'])->exists()) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'A field with the name "' . $validated['field_name'] . '" already exists for this component.',
                ], 422);
            }
            return back()->withErrors(['field_name' => 'Field name already exists for this component.'])->withInput();
        }

        $validated['is_required'] = $request->boolean('is_required', false);
        $validated['is_active']   = $request->boolean('is_active', true);

        if (!isset($validated['sort_order']) || $validated['sort_order'] === null || $validated['sort_order'] === '') {
            $maxOrder = $component->fields()->max('sort_order') ?? 0;
            $validated['sort_order'] = $maxOrder + 1;
        }

        // Process options for select
        if (isset($validated['options'])) {
            if (is_string($validated['options'])) {
                $decoded = json_decode($validated['options'], true);
                $validated['options'] = $decoded ?? $validated['options'];
            }
        }

        $field = $component->fields()->create($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Field "' . $field->field_label . '" created successfully.',
                'field'   => $field,
                'fields'  => $component->fields()->orderBy('sort_order', 'asc')->get(),
            ]);
        }

        return back()->with('success', 'Field "' . $field->field_label . '" added to "' . $component->component_name . '" successfully!');
    }

    /**
     * Update an existing field definition.
     */
    public function update(Request $request, Component $component, ComponentField $field)
    {
        // Security check: verify field belongs to component
        if ((int)$field->component_id !== (int)$component->id) {
            abort(403, 'Unauthorized field action.');
        }

        // Sanitize field_name if changed
        if ($request->filled('field_name')) {
            $request->merge(['field_name' => Str::snake(Str::slug($request->input('field_name'), '_'))]);
        }

        $validated = $request->validate([
            'field_label'      => 'required|string|max:255',
            'field_name'       => 'required|string|max:255',
            'field_type'       => 'required|in:' . implode(',', ComponentField::ALLOWED_TYPES),
            'placeholder'      => 'nullable|string|max:255',
            'default_value'    => 'nullable|string',
            'help_text'        => 'nullable|string|max:500',
            'is_required'      => 'nullable|boolean',
            'is_active'        => 'nullable|boolean',
            'sort_order'       => 'nullable|integer',
            'options'          => 'nullable',
            'validation_rules' => 'nullable|string|max:255',
        ]);

        // Check uniqueness of field_name within this component (excluding current field)
        $nameExists = $component->fields()
            ->where('field_name', $validated['field_name'])
            ->where('id', '!=', $field->id)
            ->exists();

        if ($nameExists) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'A field with the name "' . $validated['field_name'] . '" already exists for this component.',
                ], 422);
            }
            return back()->withErrors(['field_name' => 'Field name already exists for this component.'])->withInput();
        }

        $validated['is_required'] = $request->boolean('is_required', false);
        $validated['is_active']   = $request->boolean('is_active', true);

        // Process options for select
        if (isset($validated['options'])) {
            if (is_string($validated['options'])) {
                $decoded = json_decode($validated['options'], true);
                $validated['options'] = $decoded ?? $validated['options'];
            }
        }

        $field->update($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Field "' . $field->field_label . '" updated successfully.',
                'field'   => $field,
                'fields'  => $component->fields()->orderBy('sort_order', 'asc')->get(),
            ]);
        }

        return back()->with('success', 'Field "' . $field->field_label . '" updated successfully!');
    }

    /**
     * Delete a field definition.
     */
    public function destroy(Request $request, Component $component, ComponentField $field)
    {
        // Security check: verify field belongs to component
        if ((int)$field->component_id !== (int)$component->id) {
            abort(403, 'Unauthorized field action.');
        }

        $label = $field->field_label;
        $field->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Field "' . $label . '" deleted successfully.',
                'fields'  => $component->fields()->orderBy('sort_order', 'asc')->get(),
            ]);
        }

        return back()->with('success', 'Field "' . $label . '" deleted successfully!');
    }

    /**
     * Update sort orders of fields for a component.
     */
    public function updateOrder(Request $request, Component $component)
    {
        $orders = $request->input('orders', []); // [field_id => sort_order]

        foreach ($orders as $fieldId => $order) {
            $component->fields()
                      ->where('id', $fieldId)
                      ->update(['sort_order' => (int)$order]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Field order updated successfully.',
                'fields'  => $component->fields()->orderBy('sort_order', 'asc')->get(),
            ]);
        }

        return back()->with('success', 'Field orders updated successfully!');
    }
}
