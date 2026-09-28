<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\SubSection;
use App\Models\SectionComponentSubcomponent;
use Illuminate\Http\Request;
use App\Models\Component;
use App\Models\SectionComponent;
use App\Models\SectionComponentData;

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
        $sectionComponentsItemCountMap = [];
        foreach ($allMappings as $mapping) {
            $sectionComponentsMap[$mapping->section_id][$mapping->component_id] = (bool) $mapping->status;
            $sectionComponentsMultipleMap[$mapping->section_id][$mapping->component_id] = (bool) $mapping->is_multiple;
            $sectionComponentsItemCountMap[$mapping->section_id][$mapping->component_id] = $mapping->item_count;
        }

        return view('superadmin.pages.managesection', compact('sections', 'components', 'selectedSectionId', 'sectionComponentsMap', 'sectionComponentsMultipleMap', 'sectionComponentsItemCountMap'));
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
        $submittedItemCount  = $request->input('item_count', []);

        $allComponents = Component::all();
        $targetSection = Section::find($sectionId);

        foreach ($allComponents as $comp) {
            $status     = isset($submittedComponents[$comp->id]) && (string)$submittedComponents[$comp->id] === '1';
            $isMultiple = isset($submittedMultiple[$comp->id]) && (string)$submittedMultiple[$comp->id] === '1';
            $itemCount  = (!empty($submittedItemCount[$comp->id]) && (int)$submittedItemCount[$comp->id] > 0) ? (int)$submittedItemCount[$comp->id] : null;

            SectionComponent::updateOrCreate(
                [
                    'section_id'   => $sectionId,
                    'component_id' => $comp->id,
                ],
                [
                    'status'      => $status,
                    'is_multiple' => $isMultiple,
                    'item_count'  => $itemCount,
                ]
            );

            // Auto-seed initial instances if multiple & itemCount is configured
            if ($status && $isMultiple && $itemCount && $targetSection) {
                self::autoSeedInstances($targetSection, $comp, null, $itemCount);
            }
        }

        $sectionName = $targetSection ? $targetSection->section_name : 'Section';

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
     * Toggle the status of the specified subsection or set it as the single active variant.
     */
    public function toggleSubsectionStatus(Request $request, SubSection $subsection)
    {
        $mode = $request->input('mode', 'toggle'); // 'toggle' or 'single'

        if ($mode === 'single') {
            // Activate this subsection and deactivate all others in the parent section
            SubSection::where('section_id', $subsection->section_id)->update(['status' => false]);
            SubSection::where('id', $subsection->id)->update(['status' => true]);
            $subsection->refresh();
        } else {
            $newStatus = !$subsection->status;
            $subsection->status = $newStatus;
            $subsection->save();
        }

        $allSubsections = SubSection::with('components')
            ->where('section_id', $subsection->section_id)
            ->orderBy('order')
            ->get();

        if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'     => true,
                'status'      => (bool)$subsection->status,
                'subsections' => $allSubsections,
                'message'     => 'Subsection "' . $subsection->subsection_name . '" status updated successfully!',
            ]);
        }

        $label = $subsection->status ? 'Active' : 'Inactive';
        return back()->with('success', 'Subsection "' . $subsection->subsection_name . '" status changed to ' . $label . ' successfully!');
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
        $itemCountMap = $request->input('item_count', []);

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
            $itemCount  = (!empty($itemCountMap[$subCompId]) && (int)$itemCountMap[$subCompId] > 0) ? (int)$itemCountMap[$subCompId] : null;

            SectionComponentSubcomponent::create([
                'section_id'       => $section->id,
                'component_id'     => $component->id,
                'sub_component_id' => (int)$subCompId,
                'status'           => true,
                'order'            => $order++,
                'is_multiple'      => $isMultiple,
                'item_count'       => $itemCount,
            ]);

            if ($isMultiple && $itemCount) {
                $subCompObj = Component::find($subCompId);
                if ($subCompObj) {
                    self::autoSeedInstances($section, $component, $subCompObj, $itemCount);
                }
            }
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
                'is_multiple'    => (bool)$r->is_multiple,
                'item_count'     => $r->item_count,
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
                'is_multiple'    => (bool)$r->is_multiple,
                'item_count'     => $r->item_count,
            ]);

        return response()->json($subComponents);
    }

    /**
     * Ensure that at least $targetCount instances exist for a repeater component or subcomponent.
     * Preserves existing instances and only generates missing instances.
     */
    public static function autoSeedInstances(Section $section, Component $comp, ?Component $subComp = null, int $targetCount = 0)
    {
        if ($targetCount <= 0) return;

        $componentId = $comp->id;
        $subComponentId = $subComp?->id;

        $q = SectionComponentData::where('section_id', $section->id)
            ->where('component_id', $componentId);

        if ($subComponentId) {
            $q->where('sub_component_id', $subComponentId);
        } else {
            $q->whereNull('sub_component_id');
        }

        $existingIndices = $q->distinct()->pluck('instance_index')->toArray();
        $currentCount = count($existingIndices);

        if ($currentCount >= $targetCount) {
            return;
        }

        // Check if $comp has subcomponents (e.g. Card with Student Name, Image, Package, etc.) and $subComp is null
        $secSubRows = SectionComponentSubcomponent::with(['subComponent.fields'])
            ->where('section_id', $section->id)
            ->where('component_id', $componentId)
            ->where('status', true)
            ->orderBy('order')
            ->get();

        $subCompEntities = $secSubRows->pluck('subComponent')->filter();

        if ($subCompEntities->isEmpty() && $comp->is_subcomponent && $comp->subcomponents()->exists()) {
            $subCompEntities = $comp->subcomponents()->with('fields')->get();
        }

        if (!$subComponentId && $subCompEntities->isNotEmpty()) {
            // Multi-instance container (e.g. Card 1, Card 2, Card 3, Card 4)
            for ($idx = $currentCount; $idx < $targetCount; $idx++) {
                $displayNum = $idx + 1;
                $cardLabel = $comp->component_name . ' ' . $displayNum;

                foreach ($subCompEntities as $subEntity) {
                    if (!$subEntity) continue;
                    $subFields = $subEntity->fields()->where('is_active', true)->orderBy('sort_order', 'asc')->get();

                    if ($subFields->isNotEmpty()) {
                        foreach ($subFields as $fIdx => $field) {
                            $fNameLower = strtolower($field->field_name);
                            $isPrimary = in_array($fNameLower, ['title', 'heading', 'name', 'student_name', 'text', 'label'], true)
                                || ($fIdx === 0 && !in_array($field->field_type, ['image', 'video', 'file'], true));

                            $val = $isPrimary ? ($cardLabel . ' - ' . $field->field_label) : null;
                            if ($fNameLower === 'url' || $fNameLower === 'link') {
                                $val = '#' . \Illuminate\Support\Str::slug($cardLabel);
                            }

                            SectionComponentData::create([
                                'section_id'         => $section->id,
                                'component_id'       => $componentId,
                                'sub_component_id'   => $subEntity->id,
                                'component_field_id' => $field->id,
                                'instance_index'     => $idx,
                                'field_name'         => $field->field_name,
                                'content_value'      => $val,
                            ]);
                        }
                    } else {
                        SectionComponentData::create([
                            'section_id'         => $section->id,
                            'component_id'       => $componentId,
                            'sub_component_id'   => $subEntity->id,
                            'component_field_id' => null,
                            'instance_index'     => $idx,
                            'field_name'         => $subEntity->component_slug,
                            'content_value'      => $cardLabel . ' ' . $subEntity->component_name,
                        ]);
                    }
                }
            }
        } else {
            // Single repeater component or subcomponent (e.g. Anchor in Nav, or Button, etc.)
            $targetEntity = $subComp ?: $comp;
            $fields = $targetEntity->fields()->where('is_active', true)->orderBy('sort_order', 'asc')->get();

            for ($idx = $currentCount; $idx < $targetCount; $idx++) {
                $displayNum = $idx + 1;
                $label = $targetEntity->component_name . ' ' . $displayNum;

                if ($fields->isNotEmpty()) {
                    foreach ($fields as $fIdx => $field) {
                        $fNameLower = strtolower($field->field_name);
                        $isPrimary = in_array($fNameLower, ['anchor_text', 'text', 'title', 'heading', 'name', 'label', 'button_text'], true)
                            || ($fIdx === 0 && !in_array($field->field_type, ['image', 'video', 'file'], true));

                        $val = $isPrimary ? $label : null;

                        if ($fNameLower === 'anchor_url' || $fNameLower === 'url' || $fNameLower === 'link' || $fNameLower === 'href') {
                            $val = '#' . \Illuminate\Support\Str::slug($label);
                        }

                        SectionComponentData::create([
                            'section_id'         => $section->id,
                            'component_id'       => $componentId,
                            'sub_component_id'   => $subComponentId,
                            'component_field_id' => $field->id,
                            'instance_index'     => $idx,
                            'field_name'         => $field->field_name,
                            'content_value'      => $val,
                        ]);
                    }
                } else {
                    SectionComponentData::create([
                        'section_id'         => $section->id,
                        'component_id'       => $componentId,
                        'sub_component_id'   => $subComponentId,
                        'component_field_id' => null,
                        'instance_index'     => $idx,
                        'field_name'         => $targetEntity->component_slug,
                        'content_value'      => $label,
                    ]);
                }
            }
        }
    }
}
