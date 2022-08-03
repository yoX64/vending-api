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

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_create_product()
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

        $response->assertJson([
            'seller_id' => $user->id,
            'name' => $productName,
            'cost' => $productCost,
            'amount_available' => $productAmountAvailable
        ]);
    }
}
