<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CategoryCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = User::factory()->manager()->create();
    }

    public function test_manager_can_create_a_category_with_image(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->manager)->post(route('admin.categories.store'), [
            'name' => 'Fresh Bakery',
            'slug' => 'fresh-bakery',
            'description' => 'Muffins, cookies and croissants.',
            'image' => UploadedFile::fake()->image('bakery.jpg'),
            'is_active' => '1',
            'is_featured' => '1',
            'sort_order' => 3,
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', ['slug' => 'fresh-bakery', 'is_featured' => true]);

        $category = Category::where('slug', 'fresh-bakery')->first();
        Storage::disk('public')->assertExists($category->image);
    }

    public function test_category_requires_a_unique_slug(): void
    {
        Category::factory()->create(['slug' => 'taken-slug']);

        $response = $this->actingAs($this->manager)->post(route('admin.categories.store'), [
            'name' => 'Duplicate',
            'slug' => 'taken-slug',
            'is_active' => '1',
        ]);

        $response->assertSessionHasErrors('slug');
    }

    public function test_manager_can_update_a_category(): void
    {
        $category = Category::factory()->create(['name' => 'Old Name']);

        $this->actingAs($this->manager)->put(route('admin.categories.update', $category), [
            'name' => 'New Name',
            'slug' => $category->slug,
            'is_active' => '1',
        ])->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'New Name']);
    }

    public function test_manager_can_delete_a_category(): void
    {
        $category = Category::factory()->create();

        $this->actingAs($this->manager)
            ->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_guest_cannot_manage_categories(): void
    {
        $this->get(route('admin.categories.index'))->assertRedirect(route('admin.login'));
    }
}
