<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\SubSection;
use App\Models\SectionComponentSubcomponent;
use Illuminate\Http\Request;
use App\Models\Component;
use App\Models\SectionComponent;

class SectionController extends Controller
{
    /**
     * Display a listing of the sections.
     */
    public function index()
    {
        $sections = Section::with('subsections.components')->latest()->get();
        $components = Component::where('status', 1)->orderBy('component_name')->get();
        return view('superadmin.pages.addsection', compact('sections', 'components'));
    }

    /**
     * Store a newly created section in storage.
     */
    public function store(Request $request)
    {
        if ($request->has('status')) {
            $request->merge([
                'status' => in_array($request->input('status'), [1, '1', true, 'true'], true) ? '1' : '0'
            ]);
        }

        $validated = $request->validate([
            'section_name'  => 'required|string|max:255|unique:sections,section_name',
            'section_title' => 'required|string|max:255',
            'section_slug'  => 'required|string|max:255|unique:sections,section_slug',
            'description'   => 'nullable|string',
            'status'          => 'required|in:0,1',
            'is_subsection' => 'nullable|boolean',
        ], [
            'section_name.unique' => 'Section is already exist.',
            'section_slug.unique' => 'Section is already exist.',
        ]);

        $validated['is_subsection'] = $request->boolean('is_subsection');

        $section = Section::create($validated);

        if ($validated['is_subsection'] && $request->has('subsections')) {
            $this->syncSubsectionsList($section, $request->input('subsections', []));
        }

        return redirect()->route('superadmin.sections.addsection')->with('success', 'Section "' . $validated['section_name'] . '" created successfully!');
    }

    /**
     * Update the specified section in storage.
     */
    public function update(Request $request, Section $section)
    {
        if ($request->has('status')) {
            $request->merge([
                'status' => in_array($request->input('status'), [1, '1', true, 'true'], true) ? '1' : '0'
            ]);
        }

        $validated = $request->validate([
            'section_name'  => 'required|string|max:255|unique:sections,section_name,' . $section->id,
            'section_title' => 'required|string|max:255',
            'section_slug'  => 'required|string|max:255|unique:sections,section_slug,' . $section->id,
            'description'   => 'nullable|string',
            'status'        => 'required|in:0,1',
            'is_subsection' => 'nullable|boolean',
        ], [
            'section_name.unique' => 'Section is already exist.',
            'section_slug.unique' => 'Section is already exist.',
        ]);

        $validated['is_subsection'] = $request->boolean('is_subsection');

        $section->update($validated);

        if ($validated['is_subsection'] && $request->has('subsections')) {
            $this->syncSubsectionsList($section, $request->input('subsections', []));
        }

        return redirect()->route('superadmin.sections.addsection')->with('success', 'Section "' . $section->section_name . '" updated successfully!');
    }

    /**
     * Save/sync subsections for a section via modal.
     */
    public function saveSubsections(Request $request, Section $section)
    {
        $subsections = $request->input('subsections', []);
        $this->syncSubsectionsList($section, $subsections);

        return back()->with('success', 'Subsections for "' . $section->section_name . '" updated successfully!');
    }

    /**
     * Helper to sync subsections array for a given section.
     */
    protected function syncSubsectionsList(Section $section, $subsectionsData)
    {
        if (!is_array($subsectionsData)) {
            return;
        }

        $existingIds = [];
        $order = 1;
        foreach ($subsectionsData as $item) {
            if (empty($item['subsection_name'])) {
                continue;
            }

            $id = $item['id'] ?? null;
            $name = trim($item['subsection_name']);
            $title = trim($item['subsection_title'] ?? $name);
            $slug = !empty($item['subsection_slug']) 
                ? \Illuminate\Support\Str::slug($item['subsection_slug']) 
                : \Illuminate\Support\Str::slug($name);
            $status = isset($item['status']) ? (bool)$item['status'] : true;
            $desc = $item['description'] ?? null;

            if ($id) {
                $sub = $section->subsections()->find($id);
                if ($sub) {
                    $sub->update([
                        'subsection_name'  => $name,
                        'subsection_title' => $title,
                        'subsection_slug'  => $slug,
                        'description'      => $desc,
                        'status'           => $status,
                        'order'            => $order++,
                    ]);
                    $existingIds[] = $sub->id;
                    continue;
                }
            }

            $newSub = $section->subsections()->create([
                'subsection_name'  => $name,
                'subsection_title' => $title,
                'subsection_slug'  => $slug,
                'description'      => $desc,
                'status'           => $status,
                'order'            => $order++,
            ]);
            $existingIds[] = $newSub->id;
        }

        // Delete any removed subsections for this section
        $section->subsections()->whereNotIn('id', $existingIds)->delete();
    }

    /**
     * Remove the specified section from storage.
     */
    public function destroy(Section $section)
    {
        $sectionName = $section->section_name;
        $section->delete();

        return redirect()->route('superadmin.sections.addsection')->with('success', 'Section "' . $sectionName . '" deleted successfully!');
    }

    /**
     * Show manage section components page.
     */
    public function manage(Request $request)
    {
        $components = Component::latest()->get();
        $sections = Section::with(['components', 'subsections.components'])->latest()->get();

        $selectedSectionId = (string)$request->query('section_id', $sections->first()?->id ?? '');

        // Fetch all section-component mappings
        $allMappings = SectionComponent::all();
        $sectionComponentsMap = [];
        $sectionComponentsMultipleMap = [];
        foreach ($allMappings as $mapping) {
            $sectionComponentsMap[$mapping->section_id][$mapping->component_id] = (bool) $mapping->status;
            $sectionComponentsMultipleMap[$mapping->section_id][$mapping->component_id] = (bool) $mapping->is_multiple;
        }

        return view('superadmin.pages.managesection', compact('sections', 'components', 'selectedSectionId', 'sectionComponentsMap', 'sectionComponentsMultipleMap'));
    }

    /**
     * Save component assignments for a section.
     */
    public function assignComponents(Request $request)
    {
        $validated = $request->validate([
            'section_id'   => 'required|exists:sections,id',
            'components'   => 'nullable|array',
        ]);

        $sectionId = $validated['section_id'];
        $submittedComponents = $request->input('components', []);
        $submittedMultiple   = $request->input('is_multiple', []);

        // Save status and is_multiple for all available components
        $allComponents = Component::all();
        foreach ($allComponents as $comp) {
            $status     = isset($submittedComponents[$comp->id]) && (string)$submittedComponents[$comp->id] === '1';
            $isMultiple = isset($submittedMultiple[$comp->id]) && (string)$submittedMultiple[$comp->id] === '1';

            SectionComponent::updateOrCreate(
                [
                    'section_id'   => $sectionId,
                    'component_id' => $comp->id,
                ],
                [
                    'status'      => $status,
                    'is_multiple' => $isMultiple,
                ]
            );
        }

        $section = Section::find($sectionId);
        $sectionName = $section ? $section->section_name : 'Section';

        if ($request->input('redirect_to') === 'assignsection') {
            return redirect()->route('Superadmin.assignsection')
                             ->with('success', 'Components for section "' . $sectionName . '" updated successfully!');
        }

        return redirect()->route('Superadmin.managesection', ['section_id' => $sectionId])
                         ->with('success', 'Components for section "' . $sectionName . '" saved successfully!');
    }

    /**
     * Toggle the status of the specified section.
     */
    public function toggleStatus(Section $section)
    {
        $newStatus = !$section->status;
        $section->update(['status' => $newStatus]);

        $label = $newStatus ? 'Active' : 'Inactive';
        return back()->with('success', 'Section "' . $section->section_name . '" status changed to ' . $label . ' successfully!');
    }

    /**
     * Assign / toggle components for a specific subsection.
     */
    public function assignSubsectionComponents(Request $request, SubSection $subsection)
    {
        $request->validate([
            'component_ids'   => 'nullable|array',
            'component_ids.*' => 'exists:components,id',
        ]);

        $ids = $request->input('component_ids', []);
        // Sync: attach selected, detach unselected (status always true on assignment)
        $syncData = [];
        foreach ($ids as $compId) {
            $syncData[(int)$compId] = ['status' => true];
        }
        $subsection->components()->sync($syncData);

        return response()->json([
            'success'    => true,
            'message'    => 'Components updated for subsection "' . $subsection->subsection_name . '".',
            'components' => $subsection->components()->get(['components.id', 'component_name', 'component_slug']),
        ]);
    }

    /**
     * Save sub-component assignments for a specific section + component pair.
     * Each section can independently choose which sub-components its "Card" uses.
     *
     * POST /sections/{section}/components/{component}/subcomponents
     */
    public function assignSectionComponentSubcomponents(Request $request, Section $section, Component $component)
    {
        $request->validate([
            'sub_component_ids'   => 'nullable|array',
            'sub_component_ids.*' => 'exists:components,id',
        ]);

        $ids = $request->input('sub_component_ids', []);
        $multipleMap = $request->input('is_multiple', []);

        // Delete existing entries for this section+component pair
        SectionComponentSubcomponent::where('section_id', $section->id)
            ->where('component_id', $component->id)
            ->delete();

        // Insert new ones
        $order = 1;
        foreach ($ids as $subCompId) {
            if ((int)$subCompId === $component->id) {
                continue; // skip self-reference
            }
            $isMultiple = !empty($multipleMap[$subCompId]);
            SectionComponentSubcomponent::create([
                'section_id'       => $section->id,
                'component_id'     => $component->id,
                'sub_component_id' => (int)$subCompId,
                'status'           => true,
                'order'            => $order++,
                'is_multiple'      => $isMultiple,
            ]);
        }

        $subComponents = SectionComponentSubcomponent::with('subComponent')
            ->where('section_id', $section->id)
            ->where('component_id', $component->id)
            ->orderBy('order')
            ->get()
            ->map(fn($r) => [
                'id'             => $r->sub_component_id,
                'component_name' => $r->subComponent->component_name,
                'component_slug' => $r->subComponent->component_slug,
            ]);

        return response()->json([
            'success'        => true,
            'message'        => 'Sub-components updated for "' . $component->component_name . '" in section "' . $section->section_name . '".',
            'sub_components' => $subComponents,
        ]);
    }

    /**
     * Get sub-components for a specific section + component pair (JSON).
     *
     * GET /sections/{section}/components/{component}/subcomponents
     */
    public function getSectionComponentSubcomponents(Section $section, Component $component)
    {
        $subComponents = SectionComponentSubcomponent::with('subComponent')
            ->where('section_id', $section->id)
            ->where('component_id', $component->id)
            ->where('status', true)
            ->orderBy('order')
            ->get()
            ->map(fn($r) => [
                'id'             => $r->sub_component_id,
                'component_name' => $r->subComponent->component_name,
                'component_slug' => $r->subComponent->component_slug,
            ]);

        return response()->json($subComponents);
    }
}
