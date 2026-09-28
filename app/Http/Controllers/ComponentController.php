<?php

namespace App\Http\Controllers;

use App\Models\Component;
use Illuminate\Http\Request;

class ComponentController extends Controller
{
    /**
     * Display a listing of the components.
     */
    public function index()
    {
        $components = Component::with('subcomponents')->latest()->get();
        $componentSubcomponentsMap = [];
        foreach ($components as $comp) {
            $componentSubcomponentsMap[$comp->id] = $comp->subcomponents->pluck('pivot.status', 'id')->toArray();
        }
        return view('superadmin.pages.addcomponent', compact('components', 'componentSubcomponentsMap'));
    }

    /**
     * Store a newly created component in storage.
     */
    public function store(Request $request)
    {
        if ($request->has('status')) {
            $request->merge([
                'status' => in_array($request->input('status'), [1, '1', true, 'true'], true) ? '1' : '0'
            ]);
        }

        $validated = $request->validate([
            'component_name'  => 'required|string|max:255|unique:components,component_name',
            'component_title' => 'required|string|max:255',
            'component_slug'  => 'required|string|max:255|unique:components,component_slug',
            'description'     => 'nullable|string',
            'status'          => 'required|in:0,1',
            'is_subcomponent' => 'nullable|boolean',
        ], [
            'component_name.unique' => 'Component is already exist.',
            'component_slug.unique' => 'Component is already exist.',
        ]);

        $validated['is_subcomponent'] = $request->boolean('is_subcomponent');

        Component::create($validated);

        return redirect()->route('superadmin.components.addcomponent')->with('success', 'Component "' . $validated['component_name'] . '" created successfully!');
    }

    /**
     * Update the specified component in storage.
     */
    public function update(Request $request, Component $component)
    {
        if ($request->has('status')) {
            $request->merge([
                'status' => in_array($request->input('status'), [1, '1', true, 'true'], true) ? '1' : '0'
            ]);
        }

        $validated = $request->validate([
            'component_name'  => 'required|string|max:255|unique:components,component_name,' . $component->id,
            'component_title' => 'required|string|max:255',
            'component_slug'  => 'required|string|max:255|unique:components,component_slug,' . $component->id,
            'description'     => 'nullable|string',
            'status'          => 'required|in:0,1',
            'is_subcomponent' => 'nullable|boolean',
        ], [
            'component_name.unique' => 'Component is already exist.',
            'component_slug.unique' => 'Component is already exist.',
        ]);

        $validated['is_subcomponent'] = $request->boolean('is_subcomponent');

        $component->update($validated);

        return redirect()->route('superadmin.components.addcomponent')->with('success', 'Component "' . $component->component_name . '" updated successfully!');
    }

    /**
     * Assign subcomponents to a component.
     */
    public function assignSubcomponents(Request $request, Component $component)
    {
        $subcomponents = $request->input('subcomponents', []);
        
        $syncData = [];
        foreach ($subcomponents as $subId => $val) {
            if ((int)$subId !== (int)$component->id && !empty($val)) {
                $syncData[$subId] = ['status' => 1];
            }
        }

        $component->subcomponents()->sync($syncData);

        return back()->with('success', 'Subcomponents updated for "' . $component->component_name . '" successfully!');
    }

    /**
     * Remove the specified component from storage.
     */
    public function destroy(Component $component)
    {
        $componentName = $component->component_name;
        $component->delete();

        return redirect()->route('superadmin.components.addcomponent')->with('success', 'Component "' . $componentName . '" deleted successfully!');
    }

    /**
     * Toggle the status of the specified component.
     */
    public function toggleStatus(Component $component)
    {
        $newStatus = !$component->status;
        $component->update(['status' => $newStatus]);

        $label = $newStatus ? 'Active' : 'Inactive';
        return back()->with('success', 'Component "' . $component->component_name . '" status changed to ' . $label . ' successfully!');
    }
}
