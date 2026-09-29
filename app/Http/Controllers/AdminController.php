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
        $sectionComponentsMultipleMap = [];
        foreach ($allSecCompMappings as $mapping) {
            $sectionComponentsMap[$mapping->section_id][$mapping->component_id] = (bool) $mapping->status;
            $sectionComponentsMultipleMap[$mapping->section_id][$mapping->component_id] = (bool) $mapping->is_multiple;
        }

        return view('superadmin.pages.assignsection', compact('admins', 'sections', 'components', 'adminSectionsMap', 'sectionComponentsMap', 'sectionComponentsMultipleMap'));
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

        // Load active components configured for this section with their subcomponents and fields
        $section->load(['components' => function ($q) {
            $q->wherePivot('status', 1)->with(['fields', 'subcomponents.fields']);
        }]);

        // Attach effective subcomponents for each component
        foreach ($section->components as $comp) {
            $secSubComps = SectionComponentSubcomponent::with(['subComponent' => function($q) {
                    $q->with('fields');
                }])
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

        // Load existing content data from database, separating field-based data and legacy data
        $allData = SectionComponentData::where('section_id', $section->id)->orderBy('instance_index', 'asc')->get();
        $contentData = [];
        $fieldData = [];
        $multiFieldData = [];
        $multiContentData = [];
        foreach ($allData as $item) {
            $key = $item->sub_component_id ? ($item->component_id . '_' . $item->sub_component_id) : (string)$item->component_id;
            $idx = (int)($item->instance_index ?? 0);

            if ($idx === 0) {
                if ($item->component_field_id) {
                    $fieldData[$key][$item->component_field_id] = $item;
                } else {
                    $contentData[$key] = $item;
                }
            }

            if ($item->component_field_id) {
                $multiFieldData[$key][$idx][$item->component_field_id] = $item;
            } else {
                $multiContentData[$key][$idx] = $item;
            }
        }

        return view('admin.section_view', compact('section', 'contentData', 'fieldData', 'multiFieldData', 'multiContentData'));
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
        if (!$activeComponentId && count($submittedComponents) === 1) {
            $activeComponentId = array_key_first($submittedComponents);
        }

        // Load active components for this section
        $activeComponents = $section->components()->wherePivot('status', 1)->with('fields')->get();

        // ----------------------------------------------------
        // PHASE 1: DYNAMIC VALIDATION GENERATION & EXECUTION
        // ----------------------------------------------------
        $rules = [];
        $messages = [];

        foreach ($activeComponents as $comp) {
            if ($activeComponentId && (int)$activeComponentId !== (int)$comp->id) {
                continue;
            }

            // Check if this component has subcomponents
            $secSubComps = SectionComponentSubcomponent::with(['subComponent' => function($q) {
                    $q->with('fields');
                }])
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

            if ($secSubComps->isNotEmpty()) {
                // Container with Subcomponents
                foreach ($secSubComps as $subComp) {
                    $subFields = $subComp->fields()->where('is_active', true)->orderBy('sort_order', 'asc')->get();

                    $isSubMultiple = (bool)($subComp->pivot->is_multiple ?? $subComp->is_multiple ?? false);
                    foreach ($subFields as $field) {
                        $isFile = in_array($field->field_type, ['image', 'video', 'file'], true);
                        $key = $isFile
                            ? ($isSubMultiple ? "components.{$comp->id}.subcomponents.{$subComp->id}.instances.*.files.{$field->id}" : "components.{$comp->id}.subcomponents.{$subComp->id}.files.{$field->id}")
                            : ($isSubMultiple ? "components.{$comp->id}.subcomponents.{$subComp->id}.instances.*.fields.{$field->id}" : "components.{$comp->id}.subcomponents.{$subComp->id}.fields.{$field->id}");

                        $existingData = SectionComponentData::where('section_id', $section->id)
                            ->where('component_id', $comp->id)
                            ->where('sub_component_id', $subComp->id)
                            ->where('component_field_id', $field->id)
                            ->first();

                        $fieldRules = [];

                        if ($isFile) {
                            if ($field->is_required && (!$existingData || !$existingData->file_path)) {
                                $fieldRules[] = 'required';
                            } else {
                                $fieldRules[] = 'nullable';
                            }

                            if ($field->field_type === 'image') {
                                $fieldRules[] = 'image';
                                $fieldRules[] = 'max:10240'; // 10MB
                            } elseif ($field->field_type === 'video') {
                                $fieldRules[] = 'mimes:mp4,mov,avi,webm,wmv';
                                $fieldRules[] = 'max:51200'; // 50MB
                            } elseif ($field->field_type === 'file') {
                                $fieldRules[] = 'file';
                                $fieldRules[] = 'max:20480'; // 20MB
                            }
                        } else {
                            if ($field->is_required) {
                                $fieldRules[] = 'required';
                            } else {
                                $fieldRules[] = 'nullable';
                            }

                            if ($field->field_type === 'url') {
                                $fieldRules[] = 'string';
                                $fieldRules[] = 'max:2048';
                            } elseif ($field->field_type === 'number') {
                                $fieldRules[] = 'numeric';
                            }
                        }

                        if (!empty($field->validation_rules)) {
                            $customParts = explode('|', $field->validation_rules);
                            foreach ($customParts as $cp) {
                                $cp = trim($cp);
                                if (!empty($cp) && !in_array($cp, $fieldRules, true)) {
                                    $fieldRules[] = $cp;
                                }
                            }
                        }

                        $rules[$key] = $fieldRules;
                        $messages["{$key}.required"] = "The {$field->field_label} field is required.";
                        $messages["{$key}.string"]   = "The {$field->field_label} must be valid text.";
                        $messages["{$key}.numeric"]  = "The {$field->field_label} must be a number.";
                        $messages["{$key}.image"]    = "The {$field->field_label} must be an image.";
                        $messages["{$key}.mimes"]    = "The {$field->field_label} must be a valid file type.";
                    }
                }
            } else {
                // Top-Level Component
                $compFields = $comp->fields()->where('is_active', true)->orderBy('sort_order', 'asc')->get();
                $isCompMultiple = (bool)($comp->pivot->is_multiple ?? $comp->is_multiple ?? false);

                foreach ($compFields as $field) {
                    $isFile = in_array($field->field_type, ['image', 'video', 'file'], true);
                    if ($isCompMultiple) {
                        $key = $isFile
                            ? "components.{$comp->id}.instances.*.files.{$field->id}"
                            : "components.{$comp->id}.instances.*.fields.{$field->id}";
                    } else {
                        if ($isFile) {
                            $key = $request->hasFile("components.{$comp->id}.files.{$field->field_name}")
                                ? "components.{$comp->id}.files.{$field->field_name}"
                                : "components.{$comp->id}.files.{$field->id}";
                        } else {
                            $key = $request->has("components.{$comp->id}.fields.{$field->field_name}")
                                ? "components.{$comp->id}.fields.{$field->field_name}"
                                : "components.{$comp->id}.fields.{$field->id}";
                        }
                    }

                    $existingData = SectionComponentData::where('section_id', $section->id)
                        ->where('component_id', $comp->id)
                        ->whereNull('sub_component_id')
                        ->where('component_field_id', $field->id)
                        ->first();

                    $fieldRules = [];

                    if ($isFile) {
                        if ($field->is_required && (!$existingData || !$existingData->file_path)) {
                            $fieldRules[] = 'required';
                        } else {
                            $fieldRules[] = 'nullable';
                        }

                        if ($field->field_type === 'image') {
                            $fieldRules[] = 'image';
                            $fieldRules[] = 'max:10240'; // 10MB
                        } elseif ($field->field_type === 'video') {
                            $fieldRules[] = 'mimes:mp4,mov,avi,webm,wmv';
                            $fieldRules[] = 'max:51200'; // 50MB
                        } elseif ($field->field_type === 'file') {
                            $fieldRules[] = 'file';
                            $fieldRules[] = 'max:20480'; // 20MB
                        }
                    } else {
                        if ($field->is_required) {
                            $fieldRules[] = 'required';
                        } else {
                            $fieldRules[] = 'nullable';
                        }

                        if ($field->field_type === 'url') {
                            $fieldRules[] = 'string';
                            $fieldRules[] = 'max:2048';
                        } elseif (in_array($field->field_type, ['number', 'range'], true)) {
                            $fieldRules[] = 'numeric';
                        } elseif ($field->field_type === 'email') {
                            $fieldRules[] = 'email';
                        } elseif (in_array($field->field_type, ['date', 'datetime-local'], true)) {
                            $fieldRules[] = 'date';
                        }
                    }

                    if (!empty($field->validation_rules)) {
                        $customParts = explode('|', $field->validation_rules);
                        foreach ($customParts as $cp) {
                            $cp = trim($cp);
                            if (!empty($cp) && !in_array($cp, $fieldRules, true)) {
                                $fieldRules[] = $cp;
                            }
                        }
                    }

                    $rules[$key] = $fieldRules;
                    $messages["{$key}.required"] = "The {$field->field_label} field is required.";
                    $messages["{$key}.string"]   = "The {$field->field_label} must be valid text.";
                    $messages["{$key}.numeric"]  = "The {$field->field_label} must be a number.";
                    $messages["{$key}.email"]    = "The {$field->field_label} must be a valid email address.";
                    $messages["{$key}.date"]     = "The {$field->field_label} must be a valid date.";
                    $messages["{$key}.image"]    = "The {$field->field_label} must be an image.";
                    $messages["{$key}.mimes"]    = "The {$field->field_label} must be a valid file type.";
                }
            }
        }

        if (!empty($rules)) {
            $request->validate($rules, $messages);
        }

        // ----------------------------------------------------
        // PHASE 2: DYNAMIC DATA STORAGE WITH REPEATER SUPPORT
        // ----------------------------------------------------
        foreach ($activeComponents as $comp) {
            if ($activeComponentId && (int)$activeComponentId !== (int)$comp->id) {
                continue;
            }

            // Check if this component has subcomponents
            $secSubComps = SectionComponentSubcomponent::with(['subComponent' => function($q) {
                    $q->with('fields');
                }])
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

            // Case 1: Top-level Repeater (instances submitted, e.g. multiple Images, Buttons, Cards)
            if (isset($compData['instances']) && is_array($compData['instances'])) {
                $instances = $compData['instances'];
                $processedIndices = [];
                $seqIdx = 0;

                foreach ($instances as $instIdx => $instData) {
                    $idx = $seqIdx++;
                    $processedIndices[] = $idx;

                    if ($secSubComps->isNotEmpty()) {
                        // Multi-instance container (e.g. multiple Cards/Slides)
                        $subDataList = $instData['subcomponents'] ?? [];
                        foreach ($secSubComps as $subComp) {
                            $subFields = $subComp->fields()->where('is_active', true)->orderBy('sort_order', 'asc')->get();
                            foreach ($subFields as $field) {
                                $isFile = in_array($field->field_type, ['image', 'video', 'file'], true);
                                if ($isFile) {
                                    $fileKey = "components.{$comp->id}.instances.{$idx}.subcomponents.{$subComp->id}.files.{$field->id}";
                                    $existingFileKey = "components.{$comp->id}.instances.{$idx}.subcomponents.{$subComp->id}.existing_files.{$field->id}";
                                    if ($request->hasFile($fileKey)) {
                                        $file = $request->file($fileKey);
                                        $ext = $file->getClientOriginalExtension();
                                        $filename = 'sec_' . $section->id . '_comp_' . $comp->id . '_inst_' . $idx . '_sub_' . $subComp->id . '_f_' . $field->id . '_' . time() . '.' . $ext;
                                        $dest = public_path('uploads/sections');
                                        if (!file_exists($dest)) mkdir($dest, 0755, true);
                                        $file->move($dest, $filename);
                                        $filePath = 'uploads/sections/' . $filename;

                                        SectionComponentData::updateOrCreate(
                                            [
                                                'section_id'         => $section->id,
                                                'component_id'       => $comp->id,
                                                'sub_component_id'   => $subComp->id,
                                                'component_field_id' => $field->id,
                                                'instance_index'     => $idx,
                                            ],
                                            [
                                                'field_name'    => $field->field_name,
                                                'file_path'     => $filePath,
                                                'content_value' => $file->getClientOriginalName(),
                                            ]
                                        );
                                    } elseif ($request->filled($existingFileKey)) {
                                        SectionComponentData::updateOrCreate(
                                            [
                                                'section_id'         => $section->id,
                                                'component_id'       => $comp->id,
                                                'sub_component_id'   => $subComp->id,
                                                'component_field_id' => $field->id,
                                                'instance_index'     => $idx,
                                            ],
                                            [
                                                'field_name'    => $field->field_name,
                                                'file_path'     => $request->input($existingFileKey),
                                                'content_value' => basename($request->input($existingFileKey)),
                                            ]
                                        );
                                    }
                                } else {
                                    if (isset($subDataList[$subComp->id]['fields']) && array_key_exists($field->id, $subDataList[$subComp->id]['fields'])) {
                                        $val = $subDataList[$subComp->id]['fields'][$field->id];
                                        SectionComponentData::updateOrCreate(
                                            [
                                                'section_id'         => $section->id,
                                                'component_id'       => $comp->id,
                                                'sub_component_id'   => $subComp->id,
                                                'component_field_id' => $field->id,
                                                'instance_index'     => $idx,
                                            ],
                                            [
                                                'field_name'    => $field->field_name,
                                                'content_value' => $val,
                                            ]
                                        );
                                    }
                                }
                            }
                        }
                    } else {
                        // Multi-instance top component (e.g. Gallery Images or Buttons)
                        $compFields = $comp->fields()->where('is_active', true)->orderBy('sort_order', 'asc')->get();
                        foreach ($compFields as $field) {
                            $isFile = in_array($field->field_type, ['image', 'video', 'file'], true);
                            if ($isFile) {
                                $fileKey = "components.{$comp->id}.instances.{$idx}.files.{$field->id}";
                                $existingFileKey = "components.{$comp->id}.instances.{$idx}.existing_files.{$field->id}";
                                if ($request->hasFile($fileKey)) {
                                    $file = $request->file($fileKey);
                                    $ext = $file->getClientOriginalExtension();
                                    $filename = 'sec_' . $section->id . '_comp_' . $comp->id . '_inst_' . $idx . '_f_' . $field->id . '_' . time() . '.' . $ext;
                                    $dest = public_path('uploads/sections');
                                    if (!file_exists($dest)) mkdir($dest, 0755, true);
                                    $file->move($dest, $filename);
                                    $filePath = 'uploads/sections/' . $filename;

                                    SectionComponentData::updateOrCreate(
                                        [
                                            'section_id'         => $section->id,
                                            'component_id'       => $comp->id,
                                            'sub_component_id'   => null,
                                            'component_field_id' => $field->id,
                                            'instance_index'     => $idx,
                                        ],
                                        [
                                            'field_name'    => $field->field_name,
                                            'file_path'     => $filePath,
                                            'content_value' => $file->getClientOriginalName(),
                                        ]
                                    );
                                } elseif ($request->filled($existingFileKey)) {
                                    SectionComponentData::updateOrCreate(
                                        [
                                            'section_id'         => $section->id,
                                            'component_id'       => $comp->id,
                                            'sub_component_id'   => null,
                                            'component_field_id' => $field->id,
                                            'instance_index'     => $idx,
                                        ],
                                        [
                                            'field_name'    => $field->field_name,
                                            'file_path'     => $request->input($existingFileKey),
                                            'content_value' => basename($request->input($existingFileKey)),
                                        ]
                                    );
                                }
                            } else {
                                if (isset($instData['fields']) && array_key_exists($field->id, $instData['fields'])) {
                                    $val = $instData['fields'][$field->id];
                                    SectionComponentData::updateOrCreate(
                                        [
                                            'section_id'         => $section->id,
                                            'component_id'       => $comp->id,
                                            'sub_component_id'   => null,
                                            'component_field_id' => $field->id,
                                            'instance_index'     => $idx,
                                        ],
                                        [
                                            'field_name'    => $field->field_name,
                                            'content_value' => $val,
                                        ]
                                    );
                                }
                            }
                        }
                    }
                }

                // Delete any removed instances
                SectionComponentData::where('section_id', $section->id)
                    ->where('component_id', $comp->id)
                    ->whereNotIn('instance_index', $processedIndices)
                    ->delete();

                continue;
            }

            // Case 2: Container with Subcomponents
            if ($secSubComps->isNotEmpty()) {
                $subDataList = $compData['subcomponents'] ?? [];

                foreach ($secSubComps as $subComp) {
                    $subFields = $subComp->fields()->where('is_active', true)->orderBy('sort_order', 'asc')->get();

                    if (isset($subDataList[$subComp->id]['instances']) && is_array($subDataList[$subComp->id]['instances'])) {
                        // Subcomponent has multiple instances! (e.g. Nav Links / Anchors)
                        $subInstances = $subDataList[$subComp->id]['instances'];
                        $subProcessed = [];
                        $seqIdx = 0;

                        foreach ($subInstances as $sIdx => $sInstData) {
                            $idx = $seqIdx++;
                            $subProcessed[] = $idx;

                            foreach ($subFields as $field) {
                                $isFile = in_array($field->field_type, ['image', 'video', 'file'], true);
                                if ($isFile) {
                                    $fileKey = "components.{$comp->id}.subcomponents.{$subComp->id}.instances.{$idx}.files.{$field->id}";
                                    $existingFileKey = "components.{$comp->id}.subcomponents.{$subComp->id}.instances.{$idx}.existing_files.{$field->id}";
                                    if ($request->hasFile($fileKey)) {
                                        $file = $request->file($fileKey);
                                        $ext = $file->getClientOriginalExtension();
                                        $filename = 'sec_' . $section->id . '_comp_' . $comp->id . '_sub_' . $subComp->id . '_inst_' . $idx . '_f_' . $field->id . '_' . time() . '.' . $ext;
                                        $dest = public_path('uploads/sections');
                                        if (!file_exists($dest)) mkdir($dest, 0755, true);
                                        $file->move($dest, $filename);
                                        $filePath = 'uploads/sections/' . $filename;

                                        SectionComponentData::updateOrCreate(
                                            [
                                                'section_id'         => $section->id,
                                                'component_id'       => $comp->id,
                                                'sub_component_id'   => $subComp->id,
                                                'component_field_id' => $field->id,
                                                'instance_index'     => $idx,
                                            ],
                                            [
                                                'field_name'    => $field->field_name,
                                                'file_path'     => $filePath,
                                                'content_value' => $file->getClientOriginalName(),
                                            ]
                                        );
                                    } elseif ($request->filled($existingFileKey)) {
                                        SectionComponentData::updateOrCreate(
                                            [
                                                'section_id'         => $section->id,
                                                'component_id'       => $comp->id,
                                                'sub_component_id'   => $subComp->id,
                                                'component_field_id' => $field->id,
                                                'instance_index'     => $idx,
                                            ],
                                            [
                                                'field_name'    => $field->field_name,
                                                'file_path'     => $request->input($existingFileKey),
                                                'content_value' => basename($request->input($existingFileKey)),
                                            ]
                                        );
                                    }
                                } else {
                                    if (isset($sInstData['fields']) && array_key_exists($field->id, $sInstData['fields'])) {
                                        $val = $sInstData['fields'][$field->id];
                                        SectionComponentData::updateOrCreate(
                                            [
                                                'section_id'         => $section->id,
                                                'component_id'       => $comp->id,
                                                'sub_component_id'   => $subComp->id,
                                                'component_field_id' => $field->id,
                                                'instance_index'     => $idx,
                                            ],
                                            [
                                                'field_name'    => $field->field_name,
                                                'content_value' => $val,
                                            ]
                                        );
                                    }
                                }
                            }
                        }

                        // Delete removed instances for this subcomponent
                        SectionComponentData::where('section_id', $section->id)
                            ->where('component_id', $comp->id)
                            ->where('sub_component_id', $subComp->id)
                            ->whereNotIn('instance_index', $subProcessed)
                            ->delete();

                        continue;
                    }

                    // Otherwise single subcomponent instance (idx = 0)
                    if ($subFields->isNotEmpty()) {
                        foreach ($subFields as $field) {
                            $isFile = in_array($field->field_type, ['image', 'video', 'file'], true);
                            if ($isFile) {
                                if ($request->hasFile("components.{$comp->id}.subcomponents.{$subComp->id}.files.{$field->id}")) {
                                    $file = $request->file("components.{$comp->id}.subcomponents.{$subComp->id}.files.{$field->id}");
                                    $extension = $file->getClientOriginalExtension();
                                    $filename = 'sec_' . $section->id . '_comp_' . $comp->id . '_sub_' . $subComp->id . '_f_' . $field->id . '_' . time() . '.' . $extension;
                                    $dest = public_path('uploads/sections');
                                    if (!file_exists($dest)) mkdir($dest, 0755, true);
                                    $file->move($dest, $filename);
                                    $filePath = 'uploads/sections/' . $filename;

                                    SectionComponentData::updateOrCreate(
                                        [
                                            'section_id'         => $section->id,
                                            'component_id'       => $comp->id,
                                            'sub_component_id'   => $subComp->id,
                                            'component_field_id' => $field->id,
                                            'instance_index'     => 0,
                                        ],
                                        [
                                            'field_name'    => $field->field_name,
                                            'file_path'     => $filePath,
                                            'content_value' => $file->getClientOriginalName(),
                                        ]
                                    );
                                }
                            } else {
                                if (isset($subDataList[$subComp->id]['fields']) && array_key_exists($field->id, $subDataList[$subComp->id]['fields'])) {
                                    $val = $subDataList[$subComp->id]['fields'][$field->id];
                                    SectionComponentData::updateOrCreate(
                                        [
                                            'section_id'         => $section->id,
                                            'component_id'       => $comp->id,
                                            'sub_component_id'   => $subComp->id,
                                            'component_field_id' => $field->id,
                                            'instance_index'     => 0,
                                        ],
                                        [
                                            'field_name'    => $field->field_name,
                                            'content_value' => $val,
                                        ]
                                    );
                                }
                            }
                        }
                    } else {
                        // Fallback: Legacy subcomponent handling
                        $subInput = $subDataList[$subComp->id] ?? [];
                        $subContentValue = isset($subInput['value']) ? $subInput['value'] : null;
                        $subExtraValue = isset($subInput['extra']) ? $subInput['extra'] : null;

                        $existing = SectionComponentData::where('section_id', $section->id)
                                                        ->where('component_id', $comp->id)
                                                        ->where('sub_component_id', $subComp->id)
                                                        ->whereNull('component_field_id')
                                                        ->first();

                        $filePath = $existing?->file_path;

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

                        if (array_key_exists($subComp->id, $subDataList) || $request->hasFile("components.{$comp->id}.subcomponents.{$subComp->id}.file")) {
                            SectionComponentData::updateOrCreate(
                                [
                                    'section_id'         => $section->id,
                                    'component_id'       => $comp->id,
                                    'sub_component_id'   => $subComp->id,
                                    'component_field_id' => null,
                                    'instance_index'     => 0,
                                ],
                                [
                                    'content_value' => $subContentValue,
                                    'extra_value'   => $subExtraValue,
                                    'file_path'     => $filePath,
                                ]
                            );
                        }
                    }
                }
            } else {
                // Top-Level Component (Single, idx = 0)
                $compFields = $comp->fields()->where('is_active', true)->orderBy('sort_order', 'asc')->get();

                if ($compFields->isNotEmpty()) {
                    foreach ($compFields as $field) {
                        $isFile = in_array($field->field_type, ['image', 'video', 'file'], true);
                        if ($isFile) {
                            $fileKey = $request->hasFile("components.{$comp->id}.files.{$field->field_name}")
                                ? "components.{$comp->id}.files.{$field->field_name}"
                                : "components.{$comp->id}.files.{$field->id}";

                            if ($request->hasFile($fileKey)) {
                                $file = $request->file($fileKey);
                                $extension = $file->getClientOriginalExtension();
                                $filename = 'sec_' . $section->id . '_comp_' . $comp->id . '_f_' . $field->id . '_' . time() . '.' . $extension;
                                $dest = public_path('uploads/sections');
                                if (!file_exists($dest)) mkdir($dest, 0755, true);
                                $file->move($dest, $filename);
                                $filePath = 'uploads/sections/' . $filename;

                                SectionComponentData::updateOrCreate(
                                    [
                                        'section_id'         => $section->id,
                                        'component_id'       => $comp->id,
                                        'sub_component_id'   => null,
                                        'component_field_id' => $field->id,
                                        'instance_index'     => 0,
                                    ],
                                    [
                                        'field_name'    => $field->field_name,
                                        'file_path'     => $filePath,
                                        'content_value' => $file->getClientOriginalName(),
                                    ]
                                );
                            }
                        } else {
                            $hasVal = false;
                            $val = null;
                            if (isset($compData['fields'])) {
                                if (array_key_exists($field->id, $compData['fields'])) {
                                    $hasVal = true;
                                    $val = $compData['fields'][$field->id];
                                } elseif (array_key_exists($field->field_name, $compData['fields'])) {
                                    $hasVal = true;
                                    $val = $compData['fields'][$field->field_name];
                                }
                            }

                            if ($hasVal) {
                                SectionComponentData::updateOrCreate(
                                    [
                                        'section_id'         => $section->id,
                                        'component_id'       => $comp->id,
                                        'sub_component_id'   => null,
                                        'component_field_id' => $field->id,
                                        'instance_index'     => 0,
                                    ],
                                    [
                                        'field_name'    => $field->field_name,
                                        'content_value' => $val,
                                    ]
                                );
                            }
                        }
                    }
                } else {
                    // Fallback: Legacy top-level component handling
                    if (!array_key_exists($comp->id, $submittedComponents) && !$request->hasFile("components.{$comp->id}.file")) {
                        continue;
                    }

                    $contentValue = isset($compData['value']) ? $compData['value'] : null;
                    $extraValue = isset($compData['extra']) ? $compData['extra'] : null;

                    $existing = SectionComponentData::where('section_id', $section->id)
                                                    ->where('component_id', $comp->id)
                                                    ->whereNull('sub_component_id')
                                                    ->whereNull('component_field_id')
                                                    ->first();

                    $filePath = $existing?->file_path;

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
                            'section_id'         => $section->id,
                            'component_id'       => $comp->id,
                            'sub_component_id'   => null,
                            'component_field_id' => null,
                            'instance_index'     => 0,
                        ],
                        [
                            'content_value' => $contentValue,
                            'extra_value'   => $extraValue,
                            'file_path'     => $filePath,
                        ]
                    );
                }
            }
        }

        return redirect()->route('admin.section.view', $section->id)
                         ->with('success', 'Content saved to database successfully!');
    }

    /**
     * Reorder repeater instances directly from table or modal via AJAX.
     */
    public function reorderInstances(Request $request, Section $section)
    {
        $user = Auth::user();

        if ($user->role !== 'Super Admin') {
            $isAssigned = $user->sections()
                ->wherePivot('status', 1)
                ->where('sections.id', $section->id)
                ->exists();

            if (!$isAssigned) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
            }
        }

        $validated = $request->validate([
            'component_id'     => 'required|integer',
            'sub_component_id' => 'nullable|integer',
            'ordered_indices'  => 'required|array',
            'ordered_indices.*'=> 'integer',
        ]);

        $componentId = (int)$validated['component_id'];
        $subComponentId = !empty($validated['sub_component_id']) ? (int)$validated['sub_component_id'] : null;
        $orderedIndices = $validated['ordered_indices'];

        \Illuminate\Support\Facades\DB::transaction(function () use ($section, $componentId, $subComponentId, $orderedIndices) {
            // Step 1: Temporarily shift all affected rows to offset 10000 + newIdx to prevent any collision
            foreach ($orderedIndices as $newIdx => $oldIdx) {
                $q = SectionComponentData::where('section_id', $section->id)
                    ->where('component_id', $componentId)
                    ->where('instance_index', (int)$oldIdx);

                if ($subComponentId) {
                    $q->where('sub_component_id', $subComponentId);
                } else {
                    $q->whereNull('sub_component_id');
                }

                $q->update(['instance_index' => 10000 + (int)$newIdx]);
            }

            // Step 2: Set final instance_index from 0 to N-1
            foreach ($orderedIndices as $newIdx => $oldIdx) {
                $q = SectionComponentData::where('section_id', $section->id)
                    ->where('component_id', $componentId)
                    ->where('instance_index', 10000 + (int)$newIdx);

                if ($subComponentId) {
                    $q->where('sub_component_id', $subComponentId);
                } else {
                    $q->whereNull('sub_component_id');
                }

                $q->update(['instance_index' => (int)$newIdx]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Order updated successfully.'
        ]);
    }
}
