<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Section;
use App\Models\Component;
use App\Models\AdminSection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_login_and_see_master_menus(): void
    {
        $superAdmin = User::create([
            'name' => 'Superadmin',
            'email' => 'superadmin@gmail.com',
            'password' => '12345678',
            'role' => 'Super Admin',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($superAdmin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('System Control Panel');
        $response->assertSee('Superadmin');
        $response->assertSee('Assign Section');
        $response->assertSee('Add Component');

        // Can access superadmin pages
        $adminPageResponse = $this->actingAs($superAdmin)->get('/Superadmin.manageadmin');
        $adminPageResponse->assertStatus(200);
    }

    public function test_admin_can_login_and_see_only_assigned_sections(): void
    {
        $admin = User::create([
            'name' => 'Rohan Admin',
            'email' => 'admin@gmail.com',
            'password' => '12345678',
            'role' => 'Admin',
            'email_verified_at' => now(),
        ]);

        $section1 = Section::create([
            'section_name' => 'Hero Banner Section',
            'section_title' => 'hero',
            'section_slug' => 'hero-banner',
            'status' => true,
        ]);

        $section2 = Section::create([
            'section_name' => 'Secret Unassigned Section',
            'section_title' => 'secret',
            'section_slug' => 'secret-section',
            'status' => true,
        ]);

        $comp = Component::create([
            'component_name' => 'Primary CTA Button',
            'component_title' => 'button',
            'component_slug' => 'primary-cta',
            'status' => true,
        ]);

        // Attach component to Section 1
        $section1->components()->attach($comp->id, ['status' => 1]);

        // Assign only Section 1 to Admin
        AdminSection::create([
            'admin_id' => $admin->id,
            'section_id' => $section1->id,
            'status' => 1,
        ]);

        // Login to dashboard
        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Admin Workspace');
        $response->assertSee('My Assigned Sections');

        // Master menus must NOT be visible to admin
        $response->assertDontSee('Component Master');
        $response->assertDontSee('Add pages');

        // Assigned section and its component SHOULD be visible
        $response->assertSee('Hero Banner Section');
        $response->assertSee('Primary CTA Button');

        // Unassigned section must NOT be visible
        $response->assertDontSee('Secret Unassigned Section');

        // Admin CAN view assigned section
        $sectionResponse = $this->actingAs($admin)->get('/admin/section/' . $section1->id);
        $sectionResponse->assertStatus(200);
        $sectionResponse->assertSee('Hero Banner Section');
        $sectionResponse->assertSee('Primary CTA Button');

        // Admin CANNOT view unassigned section (redirects to dashboard)
        $unassignedResponse = $this->actingAs($admin)->get('/admin/section/' . $section2->id);
        $unassignedResponse->assertRedirect('/dashboard');
        $unassignedResponse->assertSessionHas('error', 'You do not have access to this section.');

        // Admin CANNOT access Super Admin routes
        $forbiddenResponse = $this->actingAs($admin)->get('/Superadmin.manageadmin');
        $forbiddenResponse->assertRedirect('/dashboard');
        $forbiddenResponse->assertSessionHas('error', 'You do not have permission to access that page.');
    }

    public function test_assign_section_page_renders_with_components_and_section_pills(): void
    {
        $superAdmin = User::create([
            'name' => 'Superadmin',
            'email' => 'superadmin@gmail.com',
            'password' => '12345678',
            'role' => 'Super Admin',
            'email_verified_at' => now(),
        ]);

        $admin = User::create([
            'name' => 'Sub Admin',
            'email' => 'subadmin@gmail.com',
            'password' => '12345678',
            'role' => 'Admin',
            'email_verified_at' => now(),
        ]);

        $section = Section::create([
            'section_name' => 'Header',
            'section_title' => 'header',
            'section_slug' => 'header',
            'status' => true,
        ]);

        $comp1 = Component::create([
            'component_name' => 'Navbar',
            'component_title' => 'navbar',
            'component_slug' => 'navbar',
            'status' => true,
        ]);

        $comp2 = Component::create([
            'component_name' => 'SearchBox',
            'component_title' => 'searchbox',
            'component_slug' => 'searchbox',
            'status' => true,
        ]);

        // Active component for section
        $section->components()->attach($comp1->id, ['status' => 1]);
        // Inactive component for section
        $section->components()->attach($comp2->id, ['status' => 0]);

        // Assign section to Sub Admin
        AdminSection::create([
            'admin_id' => $admin->id,
            'section_id' => $section->id,
            'status' => 1,
        ]);

        $response = $this->actingAs($superAdmin)->get('/Superadmin.assignsection');
        $response->assertStatus(200);

        // Section pill button should be rendered
        $response->assertSee('openSectionComponents');
        $response->assertSee('Header');

        // Modal should contain both active and inactive components
        $response->assertSee('Navbar');
        $response->assertSee('SearchBox');
        $response->assertSee('Components for Section:');
        $response->assertSee('Save Components');
    }

    public function test_admin_section_view_renders_dynamic_inputs_and_saves_to_database(): void
    {
        $admin = User::create([
            'name' => 'Editor Admin',
            'email' => 'editor@gmail.com',
            'password' => '12345678',
            'role' => 'Admin',
            'email_verified_at' => now(),
        ]);

        $section = Section::create([
            'section_name' => 'Hero Banner',
            'section_title' => 'hero',
            'section_slug' => 'hero-banner',
            'status' => true,
        ]);

        $headingComp = Component::create([
            'component_name' => 'Main Heading',
            'component_title' => 'heading',
            'component_slug' => 'heading',
            'status' => true,
        ]);

        $subHeadingComp = Component::create([
            'component_name' => 'SubHeading Text',
            'component_title' => 'subheading',
            'component_slug' => 'subheading',
            'status' => true,
        ]);

        $btnComp = Component::create([
            'component_name' => 'Action Button',
            'component_title' => 'button',
            'component_slug' => 'button',
            'status' => true,
        ]);

        $paraComp = Component::create([
            'component_name' => 'Description Paragraph',
            'component_title' => 'paragraph',
            'component_slug' => 'paragraph',
            'status' => true,
        ]);

        $imgComp = Component::create([
            'component_name' => 'BackgroundImage',
            'component_title' => 'backgroundimage',
            'component_slug' => 'backgroundimage',
            'status' => true,
        ]);

        $videoComp = Component::create([
            'component_name' => 'Backgroundvideo',
            'component_title' => 'backgroundvideo',
            'component_slug' => 'backgroundvideo',
            'status' => true,
        ]);

        // Attach all to section as active
        $section->components()->attach($headingComp->id, ['status' => 1]);
        $section->components()->attach($subHeadingComp->id, ['status' => 1]);
        $section->components()->attach($btnComp->id, ['status' => 1]);
        $section->components()->attach($paraComp->id, ['status' => 1]);
        $section->components()->attach($imgComp->id, ['status' => 1]);
        $section->components()->attach($videoComp->id, ['status' => 1]);

        // Assign section to Admin
        AdminSection::create([
            'admin_id' => $admin->id,
            'section_id' => $section->id,
            'status' => 1,
        ]);

        // GET section view
        $viewResponse = $this->actingAs($admin)->get('/admin/section/' . $section->id);
        $viewResponse->assertStatus(200);

        // Check each input type is rendered
        $viewResponse->assertSee('input type="text"', false);
        $viewResponse->assertSee('&lt;textarea&gt;', false);
        $viewResponse->assertSee('input type="button"', false);
        $viewResponse->assertSee('input type="file" accept="image/*"', false);
        $viewResponse->assertSee('input type="file" accept="video/*"', false);

        // POST content to database
        $postData = [
            'components' => [
                $headingComp->id => ['value' => 'Welcome to Antigravity IDE'],
                $subHeadingComp->id => ['value' => 'Next Generation Coding Assistant'],
                $btnComp->id => ['value' => 'Get Started', 'extra' => 'https://example.com/start'],
                $paraComp->id => ['value' => 'This is a full paragraph saved in MySQL database.'],
            ]
        ];

        $saveResponse = $this->actingAs($admin)->post('/admin/section/' . $section->id . '/content', $postData);
        $saveResponse->assertRedirect('/admin/section/' . $section->id);
        $saveResponse->assertSessionHas('success');

        // Check MySQL Database has the saved rows!
        $this->assertDatabaseHas('section_component_data', [
            'section_id' => $section->id,
            'component_id' => $headingComp->id,
            'content_value' => 'Welcome to Antigravity IDE',
        ]);

        $this->assertDatabaseHas('section_component_data', [
            'section_id' => $section->id,
            'component_id' => $paraComp->id,
            'content_value' => 'This is a full paragraph saved in MySQL database.',
        ]);

        $this->assertDatabaseHas('section_component_data', [
            'section_id' => $section->id,
            'component_id' => $btnComp->id,
            'content_value' => 'Get Started',
            'extra_value' => 'https://example.com/start',
        ]);

        // Now test single-component edit: only edit headingComp
        $singleEditData = [
            'active_component_id' => $headingComp->id,
            'components' => [
                $headingComp->id => ['value' => 'Updated Heading Only'],
            ],
        ];

        $singleEditResponse = $this->actingAs($admin)->post('/admin/section/' . $section->id . '/content', $singleEditData);
        $singleEditResponse->assertRedirect('/admin/section/' . $section->id);
        $singleEditResponse->assertSessionHas('success');

        // Heading must be updated!
        $this->assertDatabaseHas('section_component_data', [
            'section_id' => $section->id,
            'component_id' => $headingComp->id,
            'content_value' => 'Updated Heading Only',
        ]);

        // Paragraph and button must REMAIN UNCHANGED!
        $this->assertDatabaseHas('section_component_data', [
            'section_id' => $section->id,
            'component_id' => $paraComp->id,
            'content_value' => 'This is a full paragraph saved in MySQL database.',
        ]);

        $this->assertDatabaseHas('section_component_data', [
            'section_id' => $section->id,
            'component_id' => $btnComp->id,
            'content_value' => 'Get Started',
            'extra_value' => 'https://example.com/start',
        ]);
    }

    public function test_status_toggles_for_pages_sections_and_components(): void
    {
        $superAdmin = User::create([
            'name' => 'Superadmin',
            'email' => 'superadmin@gmail.com',
            'password' => '12345678',
            'role' => 'Super Admin',
            'email_verified_at' => now(),
        ]);

        // 1. Page status toggle
        $page = \App\Models\Page::create([
            'page_name' => 'About Us',
            'title' => 'About',
            'slug' => 'about-us',
            'status' => 'active',
            'order' => 1,
        ]);

        $this->actingAs($superAdmin)->patch('/pages/' . $page->id . '/toggle-status');
        $this->assertEquals('inactive', $page->fresh()->status);

        $this->actingAs($superAdmin)->patch('/pages/' . $page->id . '/toggle-status');
        $this->assertEquals('active', $page->fresh()->status);

        // 2. Section status toggle
        $section = Section::create([
            'section_name' => 'Footer Section',
            'section_title' => 'footer',
            'section_slug' => 'footer',
            'status' => true,
        ]);

        $this->actingAs($superAdmin)->patch('/sections/' . $section->id . '/toggle-status');
        $this->assertEquals(0, $section->fresh()->status);

        $this->actingAs($superAdmin)->patch('/sections/' . $section->id . '/toggle-status');
        $this->assertEquals(1, $section->fresh()->status);

        // 3. Component status toggle
        $component = Component::create([
            'component_name' => 'Navbar Component',
            'component_title' => 'navbar',
            'component_slug' => 'navbar',
            'status' => true,
        ]);

        $this->actingAs($superAdmin)->patch('/components/' . $component->id . '/toggle-status');
        $this->assertEquals(0, $component->fresh()->status);

        $this->actingAs($superAdmin)->patch('/components/' . $component->id . '/toggle-status');
        $this->assertEquals(1, $component->fresh()->status);
    }

    public function test_admin_card_component_subcomponents_render_and_save()
    {
        $admin = User::factory()->create(['role' => 'Admin']);

        $section = Section::create([
            'section_name' => 'Testimonial Section',
            'section_title' => 'testimonial',
            'section_slug' => 'testimonial',
            'status' => true,
        ]);

        // Create Card component
        $cardComp = Component::create([
            'component_name' => 'Card',
            'component_title' => 'card',
            'component_slug' => 'card',
            'status' => true,
            'is_subcomponent' => true,
        ]);

        // Create Subcomponents: Heading, Paragraph, Image
        $headingComp = Component::create([
            'component_name' => 'Card Heading',
            'component_title' => 'card-heading',
            'component_slug' => 'card-heading',
            'status' => true,
        ]);
        $paraComp = Component::create([
            'component_name' => 'Card Paragraph',
            'component_title' => 'card-paragraph',
            'component_slug' => 'card-paragraph',
            'status' => true,
        ]);
        $imgComp = Component::create([
            'component_name' => 'Card Image',
            'component_title' => 'card-image',
            'component_slug' => 'card-image',
            'status' => true,
        ]);

        // Assign subcomponents to Card globally
        $cardComp->subcomponents()->attach([
            $headingComp->id => ['status' => 1],
            $paraComp->id => ['status' => 1],
            $imgComp->id => ['status' => 1],
        ]);

        // Attach Card to Section
        $section->components()->attach($cardComp->id, ['status' => 1]);

        // Assign section to Admin
        AdminSection::create([
            'admin_id' => $admin->id,
            'section_id' => $section->id,
            'status' => 1,
        ]);

        // View assigned section
        $response = $this->actingAs($admin)->get('/admin/section/' . $section->id);
        $response->assertStatus(200);

        // Assert subcomponents are displayed with their inputs in the view
        $response->assertSee('Card Heading');
        $response->assertSee('Card Paragraph');
        $response->assertSee('Card Image');
        $response->assertSee('name="components[' . $cardComp->id . '][subcomponents][' . $headingComp->id . '][value]"', false);
        $response->assertSee('name="components[' . $cardComp->id . '][subcomponents][' . $paraComp->id . '][value]"', false);
        $response->assertSee('name="components[' . $cardComp->id . '][subcomponents][' . $imgComp->id . '][file]"', false);

        // POST content for Card's subcomponents
        $postData = [
            'components' => [
                $cardComp->id => [
                    'subcomponents' => [
                        $headingComp->id => ['value' => 'Top Rated Customer Review'],
                        $paraComp->id => ['value' => 'Outstanding quality and very smooth UI.'],
                    ]
                ]
            ]
        ];

        $saveResponse = $this->actingAs($admin)->post('/admin/section/' . $section->id . '/content', $postData);
        $saveResponse->assertRedirect('/admin/section/' . $section->id);
        $saveResponse->assertSessionHas('success');

        // Verify stored in section_component_data
        $this->assertDatabaseHas('section_component_data', [
            'section_id' => $section->id,
            'component_id' => $cardComp->id,
            'sub_component_id' => $headingComp->id,
            'content_value' => 'Top Rated Customer Review',
        ]);

        $this->assertDatabaseHas('section_component_data', [
            'section_id' => $section->id,
            'component_id' => $cardComp->id,
            'sub_component_id' => $paraComp->id,
            'content_value' => 'Outstanding quality and very smooth UI.',
        ]);

        // Re-visit section view and assert saved values are visible
        $revisitResponse = $this->actingAs($admin)->get('/admin/section/' . $section->id);
        $revisitResponse->assertSee('Top Rated Customer Review');
        $revisitResponse->assertSee('Outstanding quality and very smooth UI.');
    }

    public function test_superadmin_manage_section_view_renders_clean_open_view_trigger()
    {
        $superAdmin = User::factory()->create(['role' => 'Super Admin']);
        $section = Section::create([
            'section_name' => 'Header Section',
            'section_title' => 'header',
            'section_slug' => 'header',
            'status' => true,
        ]);

        $response = $this->actingAs($superAdmin)->get(route('Superadmin.managesection'));
        $response->assertStatus(200);
        $response->assertSee('x-data="manageSectionApp()"', false);
        $response->assertSee('function manageSectionApp()', false);
        $response->assertSee('@click="openView(' . $section->id . ')"', false);
        $response->assertSee('sectionsData:', false);
    }

    public function test_superadmin_can_assign_components_to_subsection()
    {
        $superAdmin = User::factory()->create(['role' => 'Super Admin']);
        $section = Section::create([
            'section_name'  => 'Hero Carousel Section',
            'section_title' => 'hero',
            'section_slug'  => 'hero-carousel',
            'is_subsection' => true,
            'status'        => true,
        ]);

        $sub = $section->subsections()->create([
            'subsection_name'  => 'Slide 1',
            'subsection_title' => 'First Slide',
            'subsection_slug'  => 'slide-1',
            'status'           => true,
            'order'            => 1,
        ]);

        $comp = Component::create([
            'component_name'  => 'Banner Heading',
            'component_title' => 'heading',
            'component_slug'  => 'banner-heading',
            'status'          => true,
        ]);

        // Check addsection page contains the Add Component trigger and modal
        $pageResponse = $this->actingAs($superAdmin)->get(route('Superadmin.addsection'));
        $pageResponse->assertStatus(200);
        $pageResponse->assertSee('openSubCompAssign', false);
        $pageResponse->assertSee('openSubCompModal', false);
        $pageResponse->assertSee('Banner Heading');

        // Test POST to assign components to this subsection
        $assignResponse = $this->actingAs($superAdmin)->postJson(route('subsections.assignComponents', $sub), [
            'component_ids' => [$comp->id],
        ]);

        $assignResponse->assertStatus(200);
        $assignResponse->assertJson(['success' => true]);
        $this->assertTrue($sub->fresh()->components->contains($comp->id));
    }
}

