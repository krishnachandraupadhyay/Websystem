<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ComponentController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ComponentFieldController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    $user = Illuminate\Support\Facades\Auth::user();
    if ($user && $user->role === 'Super Admin') {
        $totalUsers = \App\Models\User::count();
        $totalSections = \App\Models\Section::count();
        $totalComponents = \App\Models\Component::count();
        $totalAdmins = \App\Models\User::where('role', 'Admin')->count();
        return view('superadmin.dashboard', compact('totalUsers', 'totalSections', 'totalComponents', 'totalAdmins'));
    } else {
        // Admin: Load only active assigned sections and their active components
        $assignedSections = $user ? $user->sections()->wherePivot('status', 1)->with(['components' => function($q) {
            $q->wherePivot('status', 1);
        }])->get() : collect();
        return view('admin.dashboard', compact('assignedSections'));
    }
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin accessible assigned section view & content save
    Route::get('/admin/section/{section}', [AdminController::class, 'viewAssignedSection'])->name('admin.section.view');
    Route::post('/admin/section/{section}/content', [AdminController::class, 'saveSectionContent'])->name('admin.section.saveContent');
});

// Super Admin Only Protected Routes
Route::middleware(['auth', 'role:Super Admin'])->group(function () {
    // Pages
    Route::get('/Superadmin.addpages', [PageController::class, 'index'])->name('Superadmin.addpages');
    Route::get('/addpage', [PageController::class, 'index'])->name('addpage');
    Route::get('/superadmin/pages/addpages', [PageController::class, 'index'])->name('superadmin.pages.addpages');
    Route::post('/pages', [PageController::class, 'store'])->name('pages.store');
    Route::put('/pages/{page}', [PageController::class, 'update'])->name('pages.update');
    Route::patch('/pages/{page}/order', [PageController::class, 'updateOrder'])->name('pages.updateOrder');
    Route::patch('/pages/{page}/toggle-status', [PageController::class, 'toggleStatus'])->name('pages.toggleStatus');
    Route::delete('/pages/{page}', [PageController::class, 'destroy'])->name('pages.destroy');

    // Sections
    Route::get('/Superadmin.addsection', [SectionController::class, 'index'])->name('Superadmin.addsection');
    Route::get('/superadmin/sections/addsection', [SectionController::class, 'index'])->name('superadmin.sections.addsection');
    Route::post('/sections', [SectionController::class, 'store'])->name('sections.store');
    Route::put('/sections/{section}', [SectionController::class, 'update'])->name('sections.update');
    Route::patch('/sections/{section}/toggle-status', [SectionController::class, 'toggleStatus'])->name('sections.toggleStatus');
    Route::delete('/sections/{section}', [SectionController::class, 'destroy'])->name('sections.destroy');
    Route::post('/sections/{section}/subsections', [SectionController::class, 'saveSubsections'])->name('sections.saveSubsections');
    Route::post('/subsections/{subsection}/components', [SectionController::class, 'assignSubsectionComponents'])->name('subsections.assignComponents');
    // Per-section, per-component sub-component configuration (independent per section)
    Route::get('/sections/{section}/components/{component}/subcomponents', [SectionController::class, 'getSectionComponentSubcomponents'])->name('sections.components.subcomponents.get');
    Route::post('/sections/{section}/components/{component}/subcomponents', [SectionController::class, 'assignSectionComponentSubcomponents'])->name('sections.components.subcomponents.assign');
    Route::get('/Superadmin.managesection', [SectionController::class, 'manage'])->name('Superadmin.managesection');
    Route::post('/Superadmin.managesection', [SectionController::class, 'assignComponents'])->name('Superadmin.managesection.store');
    Route::post('/superadmin/sections/manage', [SectionController::class, 'assignComponents'])->name('superadmin.sections.manage.store');

    // Components
    Route::get('/Superadmin.addcomponent', [ComponentController::class, 'index'])->name('Superadmin.addcomponent');
    Route::get('/superadmin/components/addcomponent', [ComponentController::class, 'index'])->name('superadmin.components.addcomponent');
    Route::post('/components', [ComponentController::class, 'store'])->name('components.store');
    Route::put('/components/{component}', [ComponentController::class, 'update'])->name('components.update');
    Route::put('/superadmin/components/{component}', [ComponentController::class, 'update']);
    Route::post('/components/{component}/subcomponents', [ComponentController::class, 'assignSubcomponents'])->name('components.assignSubcomponents');
    Route::patch('/components/{component}/toggle-status', [ComponentController::class, 'toggleStatus'])->name('components.toggleStatus');
    Route::delete('/components/{component}', [ComponentController::class, 'destroy'])->name('components.destroy');

    // Component Dynamic Fields
    Route::get('/components/{component}/fields', [ComponentFieldController::class, 'index'])->name('components.fields.index');
    Route::post('/components/{component}/fields', [ComponentFieldController::class, 'store'])->name('components.fields.store');
    Route::put('/components/{component}/fields/{field}', [ComponentFieldController::class, 'update'])->name('components.fields.update');
    Route::delete('/components/{component}/fields/{field}', [ComponentFieldController::class, 'destroy'])->name('components.fields.destroy');
    Route::match(['post', 'patch'], '/components/{component}/fields/order', [ComponentFieldController::class, 'updateOrder'])->name('components.fields.order');

    // Admin & Assign Section
    Route::get('/Superadmin.manageadmin', [AdminController::class, 'index'])->name('Superadmin.manageadmin');
    Route::post('/admins', [AdminController::class, 'store'])->name('admins.store');
    Route::put('/admins/{admin}', [AdminController::class, 'update'])->name('admins.update');
    Route::delete('/admins/{admin}', [AdminController::class, 'destroy'])->name('admins.destroy');
    Route::get('/Superadmin.assignsection', [AdminController::class, 'assignSectionIndex'])->name('Superadmin.assignsection');
    Route::post('/Superadmin.assignsection', [AdminController::class, 'assignSectionStore'])->name('Superadmin.assignsection.store');
});

require __DIR__.'/auth.php';
