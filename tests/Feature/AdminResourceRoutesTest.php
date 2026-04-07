<?php

use App\Models\Customer;
use App\Models\Download;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Storage::fake('public');

    config()->set('media-library.disk_name', 'public');

    Role::findOrCreate('admin', 'web');
    Role::findOrCreate('user', 'web');

    $admin = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $admin->assignRole('admin');

    $this->actingAs($admin);
});

it('renders the admin customer project and download pages', function () {
    $customer = Customer::factory()->active()->create();
    $project = Project::factory()->for($customer)->create();
    $download = Download::factory()->create();

    $download->addMedia(UploadedFile::fake()->create('brand-kit.zip', 64, 'application/zip'))
        ->toMediaCollection('file');

    $this->get(route('admin.customers.index'))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page->component('admin/customers/Index'));

    $this->get(route('admin.customers.create'))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page->component('admin/customers/Form'));

    $this->get(route('admin.customers.show', $customer))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/customers/Show')
            ->where('customer.uuid', $customer->uuid)
        );

    $this->get(route('admin.customers.edit', $customer))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page->component('admin/customers/Form'));

    $this->get(route('admin.projects.index'))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page->component('admin/projects/Index'));

    $this->get(route('admin.projects.create', $customer))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/projects/Form')
            ->where('selected_customer_uuid', $customer->uuid)
        );

    $this->get(route('admin.projects.show', $project))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/projects/Show')
            ->where('project.uuid', $project->uuid)
        );

    $this->get(route('admin.projects.edit', $project))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page->component('admin/projects/Form'));

    $this->get(route('admin.downloads.index'))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page->component('admin/downloads/Index'));

    $this->get(route('admin.downloads.create'))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page->component('admin/downloads/Form'));

    $this->get(route('admin.downloads.show', $download))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page
            ->component('admin/downloads/Show')
            ->where('download.uuid', $download->uuid)
        );

    $this->get(route('admin.downloads.edit', $download))
        ->assertSuccessful()
        ->assertInertia(fn (Assert $page) => $page->component('admin/downloads/Form'));
});

it('stores updates and deletes customers and projects through admin routes', function () {
    $customer = Customer::factory()->active()->create();

    $this->post(route('admin.customers.store'), [
        'first_name' => 'Mina',
        'last_name' => 'Stone',
        'company_name' => 'Northwind Atelier',
        'email' => 'mina@laravel.com',
        'status' => 'active',
    ])->assertRedirect();

    $createdCustomer = Customer::query()->where('email', 'mina@laravel.com')->first();

    expect($createdCustomer)->not->toBeNull();

    $project = Project::factory()->for($customer)->create([
        'name' => 'Original Project',
        'status' => 'not_started',
    ]);

    $this->patch(route('admin.customers.update', $customer), [
        'first_name' => 'Updated',
        'last_name' => 'Customer',
        'company_name' => 'Updated Co',
        'email' => 'updated-customer@laravel.com',
        'status' => 'inactive',
    ])->assertRedirect(route('admin.customers.show', $customer));

    expect($customer->fresh()->status)->toBe('inactive');
    expect($customer->fresh()->email)->toBe('updated-customer@laravel.com');

    $this->post(route('admin.projects.store'), [
        'customer_id' => $customer->uuid,
        'name' => 'Admin Portal',
        'status' => 'in_progress',
        'priority' => 'high',
        'production_type' => 'Web App',
        'category' => 'Internal',
        'technologies' => 'Laravel, Vue',
        'features' => 'Dashboards, Reporting',
        'is_featured' => true,
        'is_public' => false,
    ])->assertRedirect();

    $createdProject = Project::query()->where('name', 'Admin Portal')->first();

    expect($createdProject)->not->toBeNull();
    expect($createdProject?->customer_id)->toBe($customer->id);

    $this->put(route('admin.projects.update', $project), [
        'customer_id' => $customer->uuid,
        'name' => 'Updated Project',
        'status' => 'completed',
        'priority' => 'critical',
        'technologies' => 'Laravel, Vue, Tailwind',
        'features' => 'Dashboards, Reporting, Exports',
        'is_featured' => false,
        'is_public' => true,
    ])->assertRedirect(route('admin.projects.show', $project));

    expect($project->fresh()->name)->toBe('Updated Project');
    expect($project->fresh()->status)->toBe('completed');

    $customerToDelete = Customer::factory()->create();
    $projectToDelete = Project::factory()->create();

    $this->delete(route('admin.customers.destroy', $customerToDelete))
        ->assertRedirect(route('admin.customers.index'));

    $this->delete(route('admin.projects.destroy', $projectToDelete))
        ->assertRedirect(route('admin.projects.index'));

    $this->assertDatabaseMissing('customers', ['id' => $customerToDelete->id]);
    $this->assertDatabaseMissing('projects', ['id' => $projectToDelete->id]);
});

it('stores updates and removes downloads through admin routes', function () {
    $download = Download::factory()->create([
        'title' => 'Original Kit',
        'download_count' => 3,
    ]);

    $this->post(route('admin.downloads.store'), [
        'title' => 'Admin Download',
        'brand' => 'Ultrashots',
        'category' => 'Branding',
        'description' => 'A new asset package.',
        'is_featured' => true,
        'is_public' => true,
        'download_file' => UploadedFile::fake()->create('admin-download.zip', 256, 'application/zip'),
        'poster_image' => UploadedFile::fake()->image('poster.png', 1200, 900),
    ])->assertRedirect();

    $createdDownload = Download::query()->where('title', 'Admin Download')->first();

    expect($createdDownload)->not->toBeNull();
    expect($createdDownload?->getFirstMedia('file'))->not->toBeNull();
    expect($createdDownload?->getFirstMedia('poster'))->not->toBeNull();

    $this->put(route('admin.downloads.update', $download), [
        'title' => 'Updated Kit',
        'brand' => 'Updated Brand',
        'category' => 'Templates',
        'description' => 'Updated asset copy.',
        'is_featured' => false,
        'is_public' => false,
        'download_file' => UploadedFile::fake()->create('replacement.pdf', 128, 'application/pdf'),
    ])->assertRedirect(route('admin.downloads.show', $download));

    expect($download->fresh()->title)->toBe('Updated Kit');
    expect($download->fresh()->is_public)->toBeFalse();
    expect($download->fresh()->file_type)->toBe('pdf');

    $downloadToDelete = Download::factory()->create();

    $this->delete(route('admin.downloads.destroy', $downloadToDelete))
        ->assertRedirect(route('admin.downloads.index'));

    $this->assertDatabaseMissing('downloads', ['id' => $downloadToDelete->id]);
});

it('handles admin download utility endpoints', function () {
    $download = Download::factory()->create([
        'title' => 'Utility Asset',
        'download_count' => 5,
    ]);
    $otherDownload = Download::factory()->create([
        'title' => 'Second Asset',
        'sort_order' => 99,
    ]);

    $download->addMedia(UploadedFile::fake()->create('utility.zip', 64, 'application/zip'))
        ->toMediaCollection('file');

    $this->postJson(route('admin.downloads.bulk-update'), [
        'download_ids' => [$download->id, $otherDownload->id],
        'changes' => [
            'is_public' => false,
            'category' => 'System',
        ],
    ])->assertSuccessful()
        ->assertJson([
            'success' => true,
            'updated' => 2,
        ]);

    expect($download->fresh()->is_public)->toBeFalse();
    expect($otherDownload->fresh()->category)->toBe('System');

    $this->postJson(route('admin.downloads.reorder'), [
        'downloads' => [
            ['id' => $download->id, 'sort_order' => 1],
            ['id' => $otherDownload->id, 'sort_order' => 2],
        ],
    ])->assertSuccessful()
        ->assertJson(['success' => true]);

    expect($otherDownload->fresh()->sort_order)->toBe(2);

    $this->getJson(route('admin.downloads.stats'))
        ->assertSuccessful()
        ->assertJsonStructure(['total_downloads', 'public_downloads', 'featured_downloads', 'download_events']);

    $this->get(route('admin.downloads.export'))
        ->assertOk()
        ->assertDownload();

    $this->post(route('admin.downloads.duplicate', $download))
        ->assertRedirect();

    expect(Download::query()->where('title', 'like', 'Utility Asset (Copy)%')->count())->toBe(1);

    $this->get(route('admin.downloads.download', $download))
        ->assertOk()
        ->assertDownload('utility.zip');
});
