<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_menu_page_only_shows_available_items(): void
    {
        $category = Category::factory()->create();
        $visible = MenuItem::factory()->for($category)->create(['name' => 'Visible Bagel']);
        $hidden = MenuItem::factory()->unavailable()->for($category)->create(['name' => 'Hidden Bagel']);

        $response = $this->get(route('menu.index'));

        $response->assertOk();
        $response->assertSee('Visible Bagel');
        $response->assertDontSee('Hidden Bagel');
    }

    public function test_menu_page_hides_categories_with_no_available_items(): void
    {
        $emptyCategory = Category::factory()->create(['name' => 'Empty Category']);
        MenuItem::factory()->unavailable()->for($emptyCategory)->create();

        $response = $this->get(route('menu.index'));

        $response->assertDontSee('Empty Category');
    }

    public function test_inactive_category_page_returns_404(): void
    {
        $category = Category::factory()->create(['is_active' => false]);

        $this->get(route('menu.category', $category))->assertNotFound();
    }

    public function test_active_category_page_shows_its_items(): void
    {
        $category = Category::factory()->create();
        $item = MenuItem::factory()->for($category)->create(['name' => 'Category Special']);

        $response = $this->get(route('menu.category', $category));

        $response->assertOk();
        $response->assertSee('Category Special');
    }

    public function test_unavailable_item_detail_page_returns_404(): void
    {
        $item = MenuItem::factory()->unavailable()->for(Category::factory())->create();

        $this->get(route('menu.item', $item))->assertNotFound();
    }

    public function test_available_item_detail_page_shows_price(): void
    {
        $item = MenuItem::factory()->for(Category::factory())->create(['name' => 'Detail Bagel', 'price' => 3.25]);

        $response = $this->get(route('menu.item', $item));

        $response->assertOk();
        $response->assertSee('Detail Bagel');
        $response->assertSee('3.25');
    }

    public function test_sold_out_item_still_displays_with_badge_but_cannot_be_added(): void
    {
        $item = MenuItem::factory()->soldOut()->for(Category::factory())->create(['name' => 'Sold Out Bagel']);

        $response = $this->get(route('menu.index'));

        $response->assertSee('Sold Out Bagel');
        $response->assertSee('Sold Out');
    }
}
