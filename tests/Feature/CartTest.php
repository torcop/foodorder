<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /** @test */
    public function item_can_be_added_to_the_cart()
    {

        $this->post('/cart', [
            'id' => 1,
        ])
            ->assertRedirect('/cart')
            ->assertSessionHasNoErrors()
            ->assertSessionHas('cart.0', [
                'id' => 1,
                'qty' => 1,
            ]);
    }

    /** @test */
    public function same_item_cannot_be_added_to_the_cart_twice()
    {


        $this->post('/cart', [
            'id' => 1, // Taco
        ]);
        $this->post('/cart', [
            'id' => 1, // Taco
        ]);
        $this->post('/cart', [
            'id' => 2, // Pizza
        ]);

        $this->assertEquals(2, count(session('cart')));
    }

    /** @test */
    public function cart_page_can_be_accessed()
    {
        //Product::factory()->count(3)->create();

        $this->get('/cart')
            ->assertViewIs('cart');
    }

    /** @test */
    public function items_added_to_the_cart_can_be_seen_in_the_cart_page()
    {
        $this->post('/cart', [
            'id' => 1, // Taco
        ]);



        $this->post('/cart', [
            'id' => 3, // BBQ
        ]);



        $cart_items = [
            [
                'id' => 1,
                'qty' => 1,
                'name' => 'Taco',
                'image' => 'some-image.jpg',
                'cost' => 1.5,
            ],
            [
                'id' => 3,
                'qty' => 1,
                'name' => 'BBQ',
                'image' => 'some-image.jpg',
                'cost' => 3.2,
            ],
        ];

        $this->get('/cart')
            ->assertViewHas('cart_items', $cart_items)
            ->assertSeeTextInOrder([
                'Taco',
                'BBQ',
            ])
            ->assertDontSeeText('Pizza');
    }
}
