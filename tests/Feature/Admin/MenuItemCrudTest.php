<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MenuItemCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $manager;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->manager = User::factory()->manager()->create();
        $this->category = Category::factory()->create();
    }

    public function test_manager_can_create_a_menu_item_with_image(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->manager)->post(route('admin.menu-items.store'), [
            'category_id' => $this->category->id,
            'name' => 'Everything Bagel',
            'slug' => 'everything-bagel',
            'price' => '1.50',
            'image' => UploadedFile::fake()->image('bagel.jpg'),
            'is_available' => '1',
            'low_stock_threshold' => 5,
        ]);

        $response->assertRedirect();
        $item = MenuItem::where('slug', 'everything-bagel')->first();
        $this->assertNotNull($item);
        $this->assertSame('1.50', (string) $item->price);
        Storage::disk('public')->assertExists($item->image);
    }

    public function test_discounted_price_must_be_less_than_regular_price(): void
    {
        $response = $this->actingAs($this->manager)->post(route('admin.menu-items.store'), [
            'category_id' => $this->category->id,
            'name' => 'Bad Deal Bagel',
            'slug' => 'bad-deal-bagel',
            'price' => '5.00',
            'discounted_price' => '7.00',
            'is_available' => '1',
        ]);

        $response->assertSessionHasErrors('discounted_price');
    }

    public function test_manager_can_update_stock_and_availability(): void
    {
        $item = MenuItem::factory()->for($this->category)->create(['stock_quantity' => 10]);

        $this->actingAs($this->manager)->put(route('admin.menu-items.update', $item), [
            'category_id' => $this->category->id,
            'name' => $item->name,
            'slug' => $item->slug,
            'price' => $item->price,
            'stock_quantity' => 0,
            'is_sold_out' => '1',
            'is_available' => '1',
        ])->assertRedirect();

        $item->refresh();
        $this->assertSame(0, $item->stock_quantity);
        $this->assertTrue($item->is_sold_out);
    }

    public function test_manager_can_delete_a_menu_item(): void
    {
        $item = MenuItem::factory()->for($this->category)->create();

        $this->actingAs($this->manager)
            ->delete(route('admin.menu-items.destroy', $item))
            ->assertRedirect(route('admin.menu-items.index'));

        $this->assertDatabaseMissing('menu_items', ['id' => $item->id]);
    }

    public function test_staff_cannot_create_menu_items(): void
    {
        $staff = User::factory()->staff()->create();

        $this->actingAs($staff)->post(route('admin.menu-items.store'), [
            'category_id' => $this->category->id,
            'name' => 'Sneaky Item',
            'slug' => 'sneaky-item',
            'price' => '5.00',
        ])->assertForbidden();

        $this->assertDatabaseMissing('menu_items', ['slug' => 'sneaky-item']);
    }
}
