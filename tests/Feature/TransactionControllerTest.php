<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Providers\AuthServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_deposit_unauthorized(): void
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/deposit');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_deposit_without_buy_ability(): void
    {
        $depositAmount = 10;

        /** @var User $user */
        $user = User::factory()->state([
            'abilities' => json_encode([]),
        ])->create();

        $response = $this->actingAs($user)->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/deposit', [
            'user_id' => $user->id,
            'amount' => $depositAmount,
        ]);

        $response->assertJson(['error' => 'You are not allowed to deposit']);
    }

    public function test_deposit_bad_ability(): void
    {
        $depositAmount = 10;

        /** @var User $user */
        $user = User::factory()->state([
            'abilities' => json_encode([AuthServiceProvider::ABILITY_SELL]),
        ])->create();

        $response = $this->actingAs($user)->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/deposit', [
            'user_id' => $user->id,
            'amount' => $depositAmount,
        ]);

        $response->assertJson(['error' => 'You are not allowed to deposit']);
    }

    public function test_deposit_bad_amount(): void
    {
        $depositAmount = 9;

        /** @var User $user */
        $user = User::factory()->state([
            'abilities' => json_encode([AuthServiceProvider::ABILITY_BUY]),
        ])->create();

        $response = $this->actingAs($user)->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/deposit', [
            'user_id' => $user->id,
            'amount' => $depositAmount,
        ]);

        $response->assertJson(['error' => 'Cost must be 5, 10, 20, 50 or 100']);
    }

    public function test_deposit_successfully(): void
    {
        $depositAmount = 10;

        /** @var User $user */
        $user = User::factory()->state([
            'abilities' => json_encode([AuthServiceProvider::ABILITY_BUY]),
        ])->create();

        $response = $this->actingAs($user)->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/deposit', [
            'user_id' => $user->id,
            'amount' => $depositAmount,
        ]);

        $this->assertEquals($depositAmount, $user->deposit);
        $response->assertNoContent();
    }

    public function test_buy_unauthorized(): void
    {
        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/buy');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function test_buy_without_buy_ability(): void
    {
        $amountAvailable = 10;
        $amountBought = 10;

        /** @var Factory $product */
        $product = Product::factory()->state([
            'amount_available' => $amountAvailable,
        ])->count(1);

        /** @var User $user */
        $user = User::factory()
            ->state([
                'abilities' => json_encode([]),
            ])
            ->has($product)
            ->create();

        $response = $this->actingAs($user)->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/buy', [
            'product_id' => $user->products->first()->id,
            'amount' => $amountBought,
        ]);

        $response->assertJson(['error' => 'You are not allowed to buy']);
    }

    public function test_buy_bad_ability(): void
    {
        $amountAvailable = 10;
        $amountBought = 10;

        /** @var Factory $product */
        $product = Product::factory()->state([
            'amount_available' => $amountAvailable,
        ])->count(1);

        /** @var User $user */
        $user = User::factory()
            ->state([
                'abilities' => json_encode([AuthServiceProvider::ABILITY_SELL]),
            ])
            ->has($product)
            ->create();

        $response = $this->actingAs($user)->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/buy', [
            'product_id' => $user->products->first()->id,
            'amount' => $amountBought,
        ]);

        $response->assertJson(['error' => 'You are not allowed to buy']);
    }

    public function test_buy_bad_amount(): void
    {
        $amountAvailable = 10;
        $amountBought = 20;

        /** @var Factory $product */
        $product = Product::factory()->state([
            'amount_available' => $amountAvailable,
        ])->count(1);

        /** @var User $user */
        $user = User::factory()
            ->state([
                'abilities' => json_encode([AuthServiceProvider::ABILITY_BUY]),
            ])
            ->has($product)
            ->create();

        $response = $this->actingAs($user)->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/buy', [
            'product_id' => $user->products->first()->id,
            'amount' => $amountBought,
        ]);

        $response->assertJson(['error' => 'Not enough products available']);
    }

    public function test_buy_bad_product(): void
    {
        $amountAvailable = 10;
        $amountBought = 20;

        /** @var Factory $product */
        $product = Product::factory()->state([
            'amount_available' => $amountAvailable,
        ])->count(1);

        /** @var User $user */
        $user = User::factory()
            ->state([
                'abilities' => json_encode([AuthServiceProvider::ABILITY_BUY]),
            ])
            ->has($product)
            ->create();

        $response = $this->actingAs($user)->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/buy', [
            'product_id' => 223,
            'amount' => $amountBought,
        ]);

        $response->assertJson(['error' => 'The selected product id is invalid.']);
    }

    public function test_buy_not_enough_deposit(): void
    {
        $productCost = 10;
        $amountAvailable = 10;
        $depositValue = 10;
        $amountBought = 10;

        /** @var Factory $product */
        $product = Product::factory()->state([
            'cost' => $productCost,
            'amount_available' => $amountAvailable,
        ])->count(1);

        /** @var User $user */
        $user = User::factory()
            ->state([
                'deposit' => $depositValue,
                'abilities' => json_encode([AuthServiceProvider::ABILITY_BUY]),
            ])
            ->has($product)
            ->create();

        $response = $this->actingAs($user)->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/buy', [
            'product_id' => $user->products->first()->id,
            'amount' => $amountBought,
        ]);

        $response->assertJson(['error' => 'Not enough money']);
    }

    public function test_buy_successfully(): void
    {
        $productCost = 5;
        $amountAvailable = 2;
        $depositValue = 10;
        $amountBought = 2;

        /** @var Factory $product */
        $product = Product::factory()->state([
            'cost' => $productCost,
            'amount_available' => $amountAvailable,
        ])->count(1);

        /** @var User $user */
        $user = User::factory()
            ->state([
                'deposit' => $depositValue,
                'abilities' => json_encode([AuthServiceProvider::ABILITY_BUY]),
            ])
            ->has($product)
            ->create();

        $response = $this->actingAs($user)->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/buy', [
            'product_id' => $user->products->first()->id,
            'amount' => $amountBought,
        ]);

        $response->assertExactJson([
            'product_purchased' => $user->products->first()->name,
            'total_spent' => $productCost * $amountBought,
            'total_remaining' => $depositValue - ($productCost * $amountBought),
        ]);
    }
}
