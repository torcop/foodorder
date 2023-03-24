<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Product;
use App\Models\User;

class SearchTest extends TestCase
{
    //use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_food_search_page_is_accessible()
    {
        $this->get('/')->assertOk();
    }

    /** @test */
    public function food_search_page_has_all_the_required_page_data()
    {

        //User::factory()->make()
        Product::factory()->count(3)->create();

        $response = $this->get('/');
        $items = Product::get();

        $response->assertViewIs('search')->assertViewHas('items', $items);
    }


    /** @test */
    public function food_search_page_shows_the_items()
    {
        Product::factory()->count(3)->create();

        $items = Product::get();

        $this->get('/')
            ->assertSeeInOrder([
                $items[0]->name,
                $items[1]->name,
                $items[2]->name,
            ]);
    }

    /** @test */
    public function food_can_be_searched_given_a_query()
    {
        Product::factory()->create([
            'name' => 'Taco'
        ]);
        Product::factory()->create([
            'name' => 'Pizza'
        ]);
        Product::factory()->create([
            'name' => 'BBQ'
        ]);

        $this->get('/?query=bbq')
            ->assertSee('BBQ')
            ->assertDontSeeText('Pizza')
            ->assertDontSeeText('Taco');

        $this->get('/')->assertSeeInOrder(['Taco', 'Pizza', 'BBQ']);
    }
}
