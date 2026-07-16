<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\MenuItem;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(SettingsSeeder::class);
    }

    public function test_adding_an_item_to_the_cart(): void
    {
        $item = MenuItem::factory()->for(Category::factory())->create(['price' => 4.50]);

        $response = $this->post(route('cart.add', $item), ['quantity' => 2]);

        $response->assertRedirect(route('cart.index'));
        $this->assertSame(2, session('sbe_cart')[array_key_first(session('sbe_cart'))]['quantity']);
    }

    public function test_cart_page_shows_added_items_and_totals(): void
    {
        $item = MenuItem::factory()->for(Category::factory())->create(['price' => 5.00]);

        $this->post(route('cart.add', $item), ['quantity' => 3]);

        $response = $this->get(route('cart.index'));

        $response->assertOk();
        $response->assertSee($item->name);
        $response->assertSee('15.00'); // 3 x $5.00 subtotal
    }

    public function test_cannot_add_unavailable_item_to_cart(): void
    {
        $item = MenuItem::factory()->unavailable()->for(Category::factory())->create();

        $response = $this->post(route('cart.add', $item), ['quantity' => 1]);

        $response->assertSessionHas('error');
        $this->assertEmpty(session('sbe_cart', []));
    }

    public function test_updating_line_quantity_to_zero_removes_it(): void
    {
        $item = MenuItem::factory()->for(Category::factory())->create();
        $this->post(route('cart.add', $item), ['quantity' => 1]);
        $lineId = array_key_first(session('sbe_cart'));

        $this->patch(route('cart.update', $lineId), ['quantity' => 0]);

        $this->assertArrayNotHasKey($lineId, session('sbe_cart', []));
    }

    public function test_removing_a_line_from_the_cart(): void
    {
        $item = MenuItem::factory()->for(Category::factory())->create();
        $this->post(route('cart.add', $item), ['quantity' => 1]);
        $lineId = array_key_first(session('sbe_cart'));

        $this->delete(route('cart.remove', $lineId));

        $this->assertArrayNotHasKey($lineId, session('sbe_cart', []));
    }

    public function test_clearing_the_cart(): void
    {
        $item = MenuItem::factory()->for(Category::factory())->create();
        $this->post(route('cart.add', $item), ['quantity' => 1]);

        $this->post(route('cart.clear'));

        $this->assertEmpty(session('sbe_cart', []));
    }

    public function test_quantity_cannot_exceed_maximum(): void
    {
        $item = MenuItem::factory()->for(Category::factory())->create();

        $this->post(route('cart.add', $item), ['quantity' => 999])->assertSessionHasErrors('quantity');
    }
}
