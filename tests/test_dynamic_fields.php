<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Component;
use App\Models\ComponentField;
use App\Models\Section;
use App\Models\SectionComponent;
use App\Models\SectionComponentData;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ComponentFieldController;

echo "=== STARTING COMPREHENSIVE TESTS FOR DYNAMIC FIELD SYSTEM ===\n\n";

$passCount = 0;
$failCount = 0;

function assertCondition($testName, $condition, $details = '') {
    global $passCount, $failCount;
    if ($condition) {
        echo " [PASS] {$testName}\n";
        if ($details) echo "        -> {$details}\n";
        $passCount++;
    } else {
        echo "![FAIL] {$testName}\n";
        if ($details) echo "        -> {$details}\n";
        $failCount++;
    }
}

// Setup or retrieve super admin user
$adminUser = User::where('role', 'Super Admin')->first() ?? User::where('email', 'superadmin@gmail.com')->first();
if (!$adminUser) {
    $adminUser = User::first();
    $adminUser->role = 'Super Admin';
    $adminUser->save();
}
Auth::login($adminUser);

// Ensure we have a test section
$section = Section::firstOrCreate(
    ['section_slug' => 'test-dynamic-section'],
    ['section_name' => 'Dynamic Test Section', 'section_title' => 'Dynamic Test Section Title', 'status' => 1]
);

\App\Models\AdminSection::firstOrCreate(
    ['admin_id' => $adminUser->id, 'section_id' => $section->id],
    ['status' => 1]
);

// -------------------------------------------------------------
// TEST 1: Heading Component with heading -> text, required = true
// -------------------------------------------------------------
echo "--- TEST 1: Heading Component ---\n";
$headingComp = Component::firstOrCreate(
    ['component_name' => 'Heading'],
    ['component_title' => 'Heading Block', 'component_slug' => 'heading', 'status' => 1]
);
$headingField = ComponentField::updateOrCreate(
    ['component_id' => $headingComp->id, 'field_name' => 'heading'],
    [
        'field_label' => 'Heading',
        'field_type' => 'text',
        'is_required' => true,
        'sort_order' => 1,
        'is_active' => true,
    ]
);

$headingHtml = View::make('components.dynamic-field', [
    'field' => $headingField,
    'component' => $headingComp,
    'value' => null,
])->render();

assertCondition(
    "Test 1: Heading field renders text input",
    strpos($headingHtml, 'type="text"') !== false && strpos($headingHtml, "components[{$headingComp->id}][fields][{$headingField->id}]") !== false,
    "Rendered HTML contains type=\"text\" and component field name"
);
assertCondition(
    "Test 1: Required indicator rendered",
    strpos($headingHtml, 'text-rose-500') !== false,
    "Rendered HTML includes required indicator (*)"
);

// -------------------------------------------------------------
// TEST 2: Description Component with description -> textarea
// -------------------------------------------------------------
echo "\n--- TEST 2: Description Component ---\n";
$descComp = Component::firstOrCreate(
    ['component_name' => 'Description'],
    ['component_title' => 'Description Block', 'component_slug' => 'description', 'status' => 1]
);
$descField = ComponentField::updateOrCreate(
    ['component_id' => $descComp->id, 'field_name' => 'description'],
    [
        'field_label' => 'Description',
        'field_type' => 'textarea',
        'is_required' => false,
        'sort_order' => 1,
        'is_active' => true,
    ]
);

$descHtml = View::make('components.dynamic-field', [
    'field' => $descField,
    'component' => $descComp,
    'value' => null,
])->render();

assertCondition(
    "Test 2: Description renders textarea",
    strpos($descHtml, '<textarea') !== false && strpos($descHtml, "components[{$descComp->id}][fields][{$descField->id}]") !== false,
    "Rendered HTML contains <textarea and component field name"
);

// -------------------------------------------------------------
// TEST 3: Background Image Component with background_image -> image
// -------------------------------------------------------------
echo "\n--- TEST 3: Background Image Component ---\n";
$bgComp = Component::firstOrCreate(
    ['component_name' => 'Background Image'],
    ['component_title' => 'Background Image Block', 'component_slug' => 'background-image', 'status' => 1]
);
$bgField = ComponentField::updateOrCreate(
    ['component_id' => $bgComp->id, 'field_name' => 'background_image'],
    [
        'field_label' => 'Background Image',
        'field_type' => 'image',
        'is_required' => false,
        'sort_order' => 1,
        'is_active' => true,
    ]
);

$bgHtml = View::make('components.dynamic-field', [
    'field' => $bgField,
    'component' => $bgComp,
    'value' => null,
])->render();

assertCondition(
    "Test 3: Background Image renders image upload input",
    strpos($bgHtml, 'type="file"') !== false && strpos($bgHtml, 'accept="image/*"') !== false && strpos($bgHtml, "components[{$bgComp->id}][files][{$bgField->id}]") !== false,
    "Rendered HTML contains type=\"file\" accept=\"image/*\" and file input name"
);

// -------------------------------------------------------------
// TEST 4: Button Component with button_text -> text, button_url -> url, target -> select
// -------------------------------------------------------------
echo "\n--- TEST 4: Button Component ---\n";
$buttonComp = Component::firstOrCreate(
    ['component_name' => 'Button'],
    ['component_title' => 'Button Block', 'component_slug' => 'button', 'status' => 1]
);
$btnTextField = ComponentField::updateOrCreate(
    ['component_id' => $buttonComp->id, 'field_name' => 'button_text'],
    ['field_label' => 'Button Text', 'field_type' => 'text', 'is_required' => true, 'sort_order' => 1, 'is_active' => true]
);
$btnUrlField = ComponentField::updateOrCreate(
    ['component_id' => $buttonComp->id, 'field_name' => 'button_url'],
    ['field_label' => 'Button URL', 'field_type' => 'url', 'is_required' => false, 'sort_order' => 2, 'is_active' => true]
);
$btnTargetField = ComponentField::updateOrCreate(
    ['component_id' => $buttonComp->id, 'field_name' => 'target'],
    [
        'field_label' => 'Target',
        'field_type' => 'select',
        'is_required' => false,
        'sort_order' => 3,
        'is_active' => true,
        'options' => [
            ['value' => '_self', 'label' => 'Same Tab'],
            ['value' => '_blank', 'label' => 'New Tab'],
        ]
    ]
);

$btnTextHtml = View::make('components.dynamic-field', ['field' => $btnTextField, 'component' => $buttonComp, 'value' => null])->render();
$btnUrlHtml = View::make('components.dynamic-field', ['field' => $btnUrlField, 'component' => $buttonComp, 'value' => null])->render();
$btnTargetHtml = View::make('components.dynamic-field', ['field' => $btnTargetField, 'component' => $buttonComp, 'value' => null])->render();

assertCondition(
    "Test 4: Button Text renders as text input",
    strpos($btnTextHtml, 'type="text"') !== false && strpos($btnTextHtml, "components[{$buttonComp->id}][fields][{$btnTextField->id}]") !== false,
    "button_text field rendered"
);
assertCondition(
    "Test 4: Button URL renders as url input",
    strpos($btnUrlHtml, 'type="url"') !== false && strpos($btnUrlHtml, "components[{$buttonComp->id}][fields][{$btnUrlField->id}]") !== false,
    "button_url field rendered"
);
assertCondition(
    "Test 4: Target renders as select dropdown with options",
    strpos($btnTargetHtml, '<select') !== false && strpos($btnTargetHtml, 'value="_self"') !== false && strpos($btnTargetHtml, 'New Tab') !== false,
    "target select rendered with Same Tab & New Tab options"
);

// -------------------------------------------------------------
// TEST 5: Save & Edit existing Section Component data
// -------------------------------------------------------------
echo "\n--- TEST 5: Data Storage & Hydration ---\n";
// Assign button component to section
$secComp = SectionComponent::firstOrCreate(
    ['section_id' => $section->id, 'component_id' => $buttonComp->id],
    ['status' => 1]
);

$adminCtrl = new AdminController();
$request = Request::create("/admin/section/{$section->id}/content", 'POST', [
    'components' => [
        $buttonComp->id => [
            'fields' => [
                'button_text' => 'Apply Now',
                'button_url' => 'https://example.com/admission',
                'target' => '_blank',
            ]
        ]
    ]
]);

$response = $adminCtrl->saveSectionContent($request, $section);

// Check if records were stored in section_component_data
$savedText = SectionComponentData::where('section_id', $section->id)
    ->where('component_id', $buttonComp->id)
    ->where('field_name', 'button_text')
    ->first();
$savedUrl = SectionComponentData::where('section_id', $section->id)
    ->where('component_id', $buttonComp->id)
    ->where('field_name', 'button_url')
    ->first();
$savedTarget = SectionComponentData::where('section_id', $section->id)
    ->where('component_id', $buttonComp->id)
    ->where('field_name', 'target')
    ->first();

assertCondition(
    "Test 5: SectionComponentData stores button_text properly",
    $savedText && $savedText->content_value === 'Apply Now' && $savedText->component_field_id === $btnTextField->id,
    "Stored 'Apply Now' with component_field_id=" . ($savedText ? $savedText->component_field_id : 'null')
);
assertCondition(
    "Test 5: SectionComponentData stores button_url and target",
    $savedUrl && $savedUrl->content_value === 'https://example.com/admission' && $savedTarget && $savedTarget->content_value === '_blank',
    "Stored URL and target"
);

// Verify hydration in dynamic-field
$hydratedHtml = View::make('components.dynamic-field', [
    'field' => $btnTextField,
    'component' => $buttonComp,
    'value' => $savedText ? $savedText->content_value : null,
])->render();

assertCondition(
    "Test 5: Existing value populated in dynamic input",
    strpos($hydratedHtml, 'value="Apply Now"') !== false,
    "Input populated with value=\"Apply Now\""
);

// -------------------------------------------------------------
// TEST 6: Server-side validation for required fields
// -------------------------------------------------------------
echo "\n--- TEST 6: Dynamic Server-Side Validation ---\n";
$emptyRequest = Request::create("/admin/section/{$section->id}/content", 'POST', [
    'components' => [
        $buttonComp->id => [
            'fields' => [
                'button_text' => '', // required!
                'button_url' => 'not-a-valid-url', // invalid url format
            ]
        ]
    ]
]);

try {
    $adminCtrl->saveSectionContent($emptyRequest, $section);
    assertCondition("Test 6: Validation throws error on empty required field", false, "Validation unexpectedly passed");
} catch (\Illuminate\Validation\ValidationException $e) {
    $errors = $e->validator->errors()->toArray();
    $hasRequiredError = isset($errors["components.{$buttonComp->id}.fields.button_text"]) || isset($errors["components.{$buttonComp->id}.fields.{$btnTextField->id}"]);
    $hasUrlError = isset($errors["components.{$buttonComp->id}.fields.button_url"]) || isset($errors["components.{$buttonComp->id}.fields.{$btnUrlField->id}"]);
    assertCondition(
        "Test 6: Validation caught required empty button_text",
        $hasRequiredError,
        "Required rule triggered successfully"
    );
    assertCondition(
        "Test 6: Validation caught invalid url format",
        $hasUrlError,
        "URL rule triggered successfully"
    );
}

// -------------------------------------------------------------
// TEST 7: Field ordering (sort_order)
// -------------------------------------------------------------
echo "\n--- TEST 7: Field Ordering ---\n";
// Re-order button fields so target is 1, button_text is 2, button_url is 3
$btnTargetField->update(['sort_order' => 1]);
$btnTextField->update(['sort_order' => 2]);
$btnUrlField->update(['sort_order' => 3]);

$orderedFields = $buttonComp->activeFields()->get();
$firstFieldName = $orderedFields[0]->field_name ?? '';
$secondFieldName = $orderedFields[1]->field_name ?? '';
$thirdFieldName = $orderedFields[2]->field_name ?? '';

assertCondition(
    "Test 7: Fields follow custom sort_order",
    $firstFieldName === 'target' && $secondFieldName === 'button_text' && $thirdFieldName === 'button_url',
    "Order is: {$firstFieldName} (1), {$secondFieldName} (2), {$thirdFieldName} (3)"
);

// -------------------------------------------------------------
// TEST 8: Completely New Component Without Any Hard-coding
// -------------------------------------------------------------
echo "\n--- TEST 8: Completely New Dynamic Component (Principal Profile) ---\n";
// Create a new component 'Principal Profile'
$principalComp = Component::firstOrCreate(
    ['component_name' => 'Principal Profile'],
    ['component_title' => 'Principal Profile Card', 'component_slug' => 'principal-profile', 'status' => 1]
);

// Define its 4 fields dynamically: name, designation, photo, description
$f1 = ComponentField::create([
    'component_id' => $principalComp->id,
    'field_label' => 'Principal Name',
    'field_name' => 'principal_name',
    'field_type' => 'text',
    'is_required' => true,
    'sort_order' => 1,
    'is_active' => true,
]);
$f2 = ComponentField::create([
    'component_id' => $principalComp->id,
    'field_label' => 'Designation',
    'field_name' => 'designation',
    'field_type' => 'text',
    'is_required' => false,
    'sort_order' => 2,
    'is_active' => true,
]);
$f3 = ComponentField::create([
    'component_id' => $principalComp->id,
    'field_label' => 'Photo',
    'field_name' => 'photo',
    'field_type' => 'image',
    'is_required' => false,
    'sort_order' => 3,
    'is_active' => true,
]);
$f4 = ComponentField::create([
    'component_id' => $principalComp->id,
    'field_label' => 'Biography / Description',
    'field_name' => 'biography',
    'field_type' => 'textarea',
    'is_required' => false,
    'sort_order' => 4,
    'is_active' => true,
]);

// Assign to section
SectionComponent::firstOrCreate(
    ['section_id' => $section->id, 'component_id' => $principalComp->id],
    ['status' => 1]
);

// Admin enters data for this completely new component
$principalReq = Request::create("/admin/section/{$section->id}/content", 'POST', [
    'components' => [
        $principalComp->id => [
            'fields' => [
                'principal_name' => 'Dr. Jane Doe',
                'designation' => 'Senior Principal & Director',
                'biography' => 'Over 20 years of leadership in education and academic excellence.',
            ]
        ]
    ]
]);

$adminCtrl->saveSectionContent($principalReq, $section);

$savedPrincipalName = SectionComponentData::where('section_id', $section->id)
    ->where('component_id', $principalComp->id)
    ->where('field_name', 'principal_name')
    ->first();
$savedDesignation = SectionComponentData::where('section_id', $section->id)
    ->where('component_id', $principalComp->id)
    ->where('field_name', 'designation')
    ->first();
$savedBio = SectionComponentData::where('section_id', $section->id)
    ->where('component_id', $principalComp->id)
    ->where('field_name', 'biography')
    ->first();

assertCondition(
    "Test 8: Principal Profile saved with zero new controller or view code",
    $savedPrincipalName && $savedPrincipalName->content_value === 'Dr. Jane Doe' &&
    $savedDesignation && $savedDesignation->content_value === 'Senior Principal & Director' &&
    $savedBio && strpos($savedBio->content_value, '20 years') !== false,
    "All fields saved: Name='{$savedPrincipalName->content_value}', Designation='{$savedDesignation->content_value}'"
);

// Clean up Principal Profile test data
SectionComponentData::where('component_id', $principalComp->id)->delete();
SectionComponent::where('component_id', $principalComp->id)->delete();
ComponentField::where('component_id', $principalComp->id)->delete();
$principalComp->delete();

echo "\n=============================================================\n";
echo "TEST RESULTS: {$passCount} PASSED, {$failCount} FAILED\n";
echo "=============================================================\n";

exit($failCount > 0 ? 1 : 0);
