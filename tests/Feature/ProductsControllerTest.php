<?php

namespace Tests\Feature;

use App\Models\User;
use App\Providers\AuthServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_product_bad_ability()
    {
        $productName = fake()->name();
        $productCost = 10;
        $productAmountAvailable = fake()->numberBetween(1, 100);

        /** @var User $user */
        $user = User::factory()->state([
            'abilities' => json_encode([AuthServiceProvider::ABILITY_BUY]),
        ])->create();

        $response = $this->actingAs($user)->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/products', [
            'name' => $productName,
            'cost' => $productCost,
            'amount_available' => $productAmountAvailable
        ]);

        $response->assertJson(['error' => 'You are not allowed to create a product']);
    }

    public function test_create_product_successfully()
    {
        $productName = fake()->name();
        $productCost = 10;
        $productAmountAvailable = fake()->numberBetween(1, 100);

        /** @var User $user */
        $user = User::factory()->state([
            'abilities' => json_encode([AuthServiceProvider::ABILITY_SELL]),
        ])->create();

        $response = $this->actingAs($user)->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/products', [
            'name' => $productName,
            'cost' => $productCost,
            'amount_available' => $productAmountAvailable
        ]);

        $response->assertJson([
            'seller_id' => $user->id,
            'name' => $productName,
            'cost' => $productCost,
            'amount_available' => $productAmountAvailable
        ]);
    }
}
