<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Section;
use App\Models\AdminSection;
use App\Models\SectionComponentData;
use App\Models\SectionComponentSubcomponent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Display a listing of Admin users.
     */
    public function index()
    {
        // Fetch all Admins (and Super Admins for management)
        $admins = User::whereIn('role', ['Admin', 'Super Admin'])->latest()->get();
        return view('superadmin.pages.manageadmin', compact('admins'));
    }

    /**
     * Store a newly created Admin user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:Admin,Super Admin',
        ], [
            'email.unique' => 'Admin with this email is already exist.',
        ]);

        $validated['email_verified_at'] = now();

        User::create($validated);

        return redirect()->route('Superadmin.manageadmin')
                         ->with('success', 'Admin "' . $validated['name'] . '" created successfully!');
    }

    /**
     * Remove the specified Admin user.
     */
    public function destroy(User $admin)
    {
        if ($admin->id === Auth::id()) {
            return redirect()->route('Superadmin.manageadmin')
                             ->with('error', 'You cannot delete your own logged-in account!');
        }

        if ($admin->role === 'Super Admin' && User::where('role', 'Super Admin')->count() <= 1) {
            return redirect()->route('Superadmin.manageadmin')
                             ->with('error', 'Cannot delete the primary Super Admin!');
        }

        $adminName = $admin->name;
        $admin->delete();

        return redirect()->route('Superadmin.manageadmin')
                         ->with('success', 'Admin "' . $adminName . '" deleted successfully!');
    }

    /**
     * Update the specified admin account.
     */
    public function update(Request $request, User $admin)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users,email,' . $admin->id,
            'role'     => 'required|in:Admin,Super Admin',
            'password' => 'nullable|string|min:6',
        ], [
            'email.unique' => 'Admin with this email is already exist.',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $admin->update($validated);

        return redirect()->route('Superadmin.manageadmin')
                         ->with('success', 'Admin "' . $admin->name . '" updated successfully!');
    }

    /**
     * Display Assign Section page (same to same layout as section_component).
     */
    public function assignSectionIndex()
    {
        $admins = User::whereIn('role', ['Admin', 'Super Admin'])->with('sections')->latest()->get();
        $sections = Section::latest()->get();

        // Mappings: [admin_id => [section_id => status]]
        $allMappings = AdminSection::all();
        $adminSectionsMap = [];
        foreach ($allMappings as $mapping) {
            $adminSectionsMap[$mapping->admin_id][$mapping->section_id] = (bool) $mapping->status;
        }

        // Available components and section-component mappings
        $components = \App\Models\Component::latest()->get();
        $allSecCompMappings = \App\Models\SectionComponent::all();
        $sectionComponentsMap = [];
        foreach ($allSecCompMappings as $mapping) {
            $sectionComponentsMap[$mapping->section_id][$mapping->component_id] = (bool) $mapping->status;
        }

        return view('superadmin.pages.assignsection', compact('admins', 'sections', 'components', 'adminSectionsMap', 'sectionComponentsMap'));
    }

    /**
     * Save assigned sections for an admin (toggle status save).
     */
    public function assignSectionStore(Request $request)
    {
        $validated = $request->validate([
            'admin_id' => 'required|exists:users,id',
            'sections' => 'nullable|array',
        ]);

        $adminId = $validated['admin_id'];
        $submittedSections = $request->input('sections', []);

        $allSections = Section::all();
        foreach ($allSections as $sec) {
            $status = isset($submittedSections[$sec->id]) && (string)$submittedSections[$sec->id] === '1';
            AdminSection::updateOrCreate(
                [
                    'admin_id'   => $adminId,
                    'section_id' => $sec->id,
                ],
                [
                    'status' => $status,
                ]
            );
        }

        $admin = User::find($adminId);
        $adminName = $admin ? $admin->name : 'Admin';

        return redirect()->route('Superadmin.assignsection')
                         ->with('success', 'Sections for Admin "' . $adminName . '" saved successfully!');
    }

    /**
     * View a specific assigned section for an Admin.
     */
    public function viewAssignedSection(Section $section)
    {
        $user = Auth::user();

        if ($user->role !== 'Super Admin') {
            $isAssigned = $user->sections()
                               ->wherePivot('status', 1)
                               ->where('sections.id', $section->id)
                               ->exists();

            if (!$isAssigned) {
                return redirect()->route('dashboard')
                                 ->with('error', 'You do not have access to this section.');
            }
        }

        // Load active components configured for this section with their subcomponents
        $section->load(['components' => function ($q) {
            $q->wherePivot('status', 1)->with('subcomponents');
        }]);

        // Attach effective subcomponents for each component
        foreach ($section->components as $comp) {
            $secSubComps = SectionComponentSubcomponent::with('subComponent')
                ->where('section_id', $section->id)
                ->where('component_id', $comp->id)
                ->where('status', true)
                ->orderBy('order')
                ->get()
                ->pluck('subComponent')
                ->filter()
                ->values();

            if ($secSubComps->isNotEmpty()) {
                $comp->effective_subcomponents = $secSubComps;
            } elseif ($comp->is_subcomponent && $comp->subcomponents->isNotEmpty()) {
                $comp->effective_subcomponents = $comp->subcomponents;
            } else {
                $comp->effective_subcomponents = collect();
            }
        }

        // Load existing content data from database, keyed by "comp_id" for top-level and "comp_id_sub_id" for subcomponents
        $allData = SectionComponentData::where('section_id', $section->id)->get();
        $contentData = [];
        foreach ($allData as $item) {
            if ($item->sub_component_id) {
                $contentData[$item->component_id . '_' . $item->sub_component_id] = $item;
            } else {
                $contentData[$item->component_id] = $item;
            }
        }

        return view('admin.section_view', compact('section', 'contentData'));
    }

    /**
     * Save/update content for active components of a section into database.
     */
    public function saveSectionContent(Request $request, Section $section)
    {
        $user = Auth::user();

        if ($user->role !== 'Super Admin') {
            $isAssigned = $user->sections()
                               ->wherePivot('status', 1)
                               ->where('sections.id', $section->id)
                               ->exists();

            if (!$isAssigned) {
                return redirect()->route('dashboard')
                                 ->with('error', 'You do not have access to this section.');
            }
        }

        $submittedComponents = $request->input('components', []);
        $activeComponentId = $request->input('active_component_id');

        // Load active components for this section
        $activeComponents = $section->components()->wherePivot('status', 1)->get();

        foreach ($activeComponents as $comp) {
            // If active_component_id is passed, only edit that specific component!
            if ($activeComponentId && (int)$activeComponentId !== (int)$comp->id) {
                continue;
            }

            // Check if this component has subcomponents (per-section or fallback global)
            $secSubComps = SectionComponentSubcomponent::with('subComponent')
                ->where('section_id', $section->id)
                ->where('component_id', $comp->id)
                ->where('status', true)
                ->orderBy('order')
                ->get()
                ->pluck('subComponent')
                ->filter()
                ->values();

            if ($secSubComps->isEmpty() && $comp->is_subcomponent && $comp->subcomponents->isNotEmpty()) {
                $secSubComps = $comp->subcomponents;
            }

            $compData = $submittedComponents[$comp->id] ?? [];

            // 1. If component has subcomponents, process each subcomponent independently
            if ($secSubComps->isNotEmpty()) {
                $subDataList = $compData['subcomponents'] ?? [];

                foreach ($secSubComps as $subComp) {
                    $subInput = $subDataList[$subComp->id] ?? [];
                    $subContentValue = isset($subInput['value']) ? $subInput['value'] : null;
                    $subExtraValue = isset($subInput['extra']) ? $subInput['extra'] : null;

                    $existing = SectionComponentData::where('section_id', $section->id)
                                                    ->where('component_id', $comp->id)
                                                    ->where('sub_component_id', $subComp->id)
                                                    ->first();

                    $filePath = $existing?->file_path;

                    // Handle file upload for subcomponent (image or video)
                    if ($request->hasFile("components.{$comp->id}.subcomponents.{$subComp->id}.file")) {
                        $file = $request->file("components.{$comp->id}.subcomponents.{$subComp->id}.file");
                        $extension = $file->getClientOriginalExtension();
                        $filename = 'sec_' . $section->id . '_comp_' . $comp->id . '_sub_' . $subComp->id . '_' . time() . '.' . $extension;

                        $destinationPath = public_path('uploads/sections');
                        if (!file_exists($destinationPath)) {
                            mkdir($destinationPath, 0755, true);
                        }

                        $file->move($destinationPath, $filename);
                        $filePath = 'uploads/sections/' . $filename;
                    }

                    // Save or update subcomponent data if submitted or file uploaded
                    if (array_key_exists($subComp->id, $subDataList) || $request->hasFile("components.{$comp->id}.subcomponents.{$subComp->id}.file")) {
                        SectionComponentData::updateOrCreate(
                            [
                                'section_id'       => $section->id,
                                'component_id'     => $comp->id,
                                'sub_component_id' => $subComp->id,
                            ],
                            [
                                'content_value'    => $subContentValue,
                                'extra_value'      => $subExtraValue,
                                'file_path'        => $filePath,
                            ]
                        );
                    }
                }
            } else {
                // 2. Standard top-level component (no subcomponents)
                if (!array_key_exists($comp->id, $submittedComponents) && !$request->hasFile("components.{$comp->id}.file")) {
                    continue;
                }

                $contentValue = isset($compData['value']) ? $compData['value'] : null;
                $extraValue = isset($compData['extra']) ? $compData['extra'] : null;

                $existing = SectionComponentData::where('section_id', $section->id)
                                                ->where('component_id', $comp->id)
                                                ->whereNull('sub_component_id')
                                                ->first();

                $filePath = $existing?->file_path;

                // Handle file upload (image or video)
                if ($request->hasFile("components.{$comp->id}.file")) {
                    $file = $request->file("components.{$comp->id}.file");
                    $extension = $file->getClientOriginalExtension();
                    $filename = 'sec_' . $section->id . '_comp_' . $comp->id . '_' . time() . '.' . $extension;

                    $destinationPath = public_path('uploads/sections');
                    if (!file_exists($destinationPath)) {
                        mkdir($destinationPath, 0755, true);
                    }

                    $file->move($destinationPath, $filename);
                    $filePath = 'uploads/sections/' . $filename;
                }

                SectionComponentData::updateOrCreate(
                    [
                        'section_id'       => $section->id,
                        'component_id'     => $comp->id,
                        'sub_component_id' => null,
                    ],
                    [
                        'content_value' => $contentValue,
                        'extra_value'   => $extraValue,
                        'file_path'     => $filePath,
                    ]
                );
            }
        }

        return redirect()->route('admin.section.view', $section->id)
                         ->with('success', 'Content saved to database successfully!');
    }
}
