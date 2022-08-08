<?php

namespace Tests\Unit;

use App\Services\CoinChangeService;
use Exception;
use PHPUnit\Framework\TestCase;

class CoinChangeServiceTest extends TestCase
{
    private CoinChangeService $coinChangeService;

    protected function setUp(): void
    {
        $this->coinChangeService = new CoinChangeService();
    }

    public function test_bad_change()
    {
        $change = $this->coinChangeService->calculate(100);

        $this->assertNotEquals([
            CoinChangeService::COIN_ONE_HUNDRED => 0,
            CoinChangeService::COIN_FIFTY => 0,
            CoinChangeService::COIN_TWENTY => 0,
            CoinChangeService::COIN_TEN => 0,
            CoinChangeService::COIN_FIVE => 1,
        ], $change);
    }

    public function test_invalid_amount_below_zero()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid amount. Cannot be below 0');
        $change = $this->coinChangeService->calculate(-20);
    }

    public function test_invalid_amount_not_divident_of_five()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid amount. Must be a divident of 5');
        $change = $this->coinChangeService->calculate(19);
    }

    public function test_change_successfully()
    {
        $change = $this->coinChangeService->calculate(150);

        $this->assertEquals([
            CoinChangeService::COIN_ONE_HUNDRED => 1,
            CoinChangeService::COIN_FIFTY => 1,
            CoinChangeService::COIN_TWENTY => 0,
            CoinChangeService::COIN_TEN => 0,
            CoinChangeService::COIN_FIVE => 0,
        ], $change);
    }
}
