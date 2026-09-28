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

echo "=== VERIFYING COMPLETE HTML INPUT & FORM TYPES SUPPORT ===\n\n";

$pass = 0;
$fail = 0;

function check($title, $condition, $details = '') {
    global $pass, $fail;
    if ($condition) {
        echo " [OK] {$title}\n";
        $pass++;
    } else {
        echo "![FAIL] {$title} -> {$details}\n";
        $fail++;
    }
}

// Temporary test component
$comp = Component::firstOrCreate(
    ['component_name' => 'AllTypesComponent'],
    ['component_title' => 'All Types Testing Block', 'component_slug' => 'all-types-component', 'status' => 1]
);

$typesToTest = [
    'text'           => ['tag' => 'input', 'type' => 'text'],
    'password'       => ['tag' => 'input', 'type' => 'password'],
    'email'          => ['tag' => 'input', 'type' => 'email'],
    'number'         => ['tag' => 'input', 'type' => 'number'],
    'search'         => ['tag' => 'input', 'type' => 'search'],
    'tel'            => ['tag' => 'input', 'type' => 'tel'],
    'url'            => ['tag' => 'input', 'type' => 'url'],
    'date'           => ['tag' => 'input', 'type' => 'date'],
    'time'           => ['tag' => 'input', 'type' => 'time'],
    'datetime-local' => ['tag' => 'input', 'type' => 'datetime-local'],
    'month'          => ['tag' => 'input', 'type' => 'month'],
    'week'           => ['tag' => 'input', 'type' => 'week'],
    'checkbox'       => ['tag' => 'input', 'type' => 'checkbox'],
    'radio'          => ['tag' => 'input', 'type' => 'radio'],
    'range'          => ['tag' => 'input', 'type' => 'range'],
    'color'          => ['tag' => 'input', 'type' => 'color'],
    'file'           => ['tag' => 'input', 'type' => 'file'],
    'multiple_file'  => ['tag' => 'input', 'type' => 'file', 'multiple' => true],
    'image'          => ['tag' => 'input', 'type' => 'file', 'accept' => 'image/*'],
    'video'          => ['tag' => 'input', 'type' => 'file', 'accept' => 'video/*'],
    'hidden'         => ['tag' => 'input', 'type' => 'hidden'],
    'textarea'       => ['tag' => 'textarea'],
    'select'         => ['tag' => 'select'],
    'button'         => ['tag' => 'button', 'type' => 'button'],
    'submit'         => ['tag' => 'button', 'type' => 'submit'],
    'reset'          => ['tag' => 'button', 'type' => 'reset'],
];

foreach ($typesToTest as $type => $expect) {
    $field = ComponentField::updateOrCreate(
        ['component_id' => $comp->id, 'field_name' => "test_{$type}"],
        [
            'field_label'   => "Test " . ucfirst($type),
            'field_type'    => $type,
            'is_required'   => false,
            'is_active'     => true,
            'sort_order'    => 1,
            'options'       => in_array($type, ['select', 'radio']) ? [
                ['value' => 'opt1', 'label' => 'Option 1'],
                ['value' => 'opt2', 'label' => 'Option 2'],
            ] : null,
        ]
    );

    $html = View::make('components.dynamic-field', [
        'field' => $field,
        'component' => $comp,
        'value' => null,
    ])->render();

    $hasTag = strpos($html, "<{$expect['tag']}") !== false;
    $hasType = isset($expect['type']) ? strpos($html, "type=\"{$expect['type']}\"") !== false : true;
    $hasMultiple = isset($expect['multiple']) ? strpos($html, "multiple") !== false : true;
    $hasAccept = isset($expect['accept']) ? strpos($html, "accept=\"{$expect['accept']}\"") !== false : true;

    $passed = $hasTag && $hasType && $hasMultiple && $hasAccept;
    check(
        "Field type '{$type}' renders <{$expect['tag']}> with correct attributes",
        $passed,
        "HTML output snippet: " . substr(strip_tags($html, '<input><select><textarea><button>'), 0, 120)
    );
}

// Clean up
ComponentField::where('component_id', $comp->id)->delete();
$comp->delete();

echo "\n=============================================================\n";
echo "RESULT: {$pass} PASSED, {$fail} FAILED\n";
echo "=============================================================\n";

exit($fail > 0 ? 1 : 0);
