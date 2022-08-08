<?php

namespace App\Services;

use Exception;

class CoinChangeService
{
    public const COIN_ONE_HUNDRED = 100;
    public const COIN_FIFTY = 50;
    public const COIN_TWENTY = 20;
    public const COIN_TEN = 10;
    public const COIN_FIVE = 5;

    private array $defaultCoins = [
        self::COIN_ONE_HUNDRED => 0,
        self::COIN_FIFTY => 0,
        self::COIN_TWENTY => 0,
        self::COIN_TEN => 0,
        self::COIN_FIVE => 0,
    ];

    /**
     * @throws Exception
     */
    public function calculate(int $amount): array
    {
        if ($amount < 0) {
            throw new Exception('Invalid amount. Cannot be below 0');
        } else if ($amount % 5 !== 0) {
            throw new Exception('Invalid amount. Must be a divident of 5');
        }

        $coins = $this->defaultCoins;

        do {
            if ($amount - self::COIN_ONE_HUNDRED >= 0) {
                $amount -= self::COIN_ONE_HUNDRED;
                $coins[self::COIN_ONE_HUNDRED]++;
            }

            if ($amount - self::COIN_FIFTY >= 0) {
                $amount -= self::COIN_FIFTY;
                $coins[self::COIN_FIFTY]++;
            }

            if ($amount - self::COIN_TWENTY >= 0) {
                $amount -= self::COIN_TWENTY;
                $coins[self::COIN_TWENTY]++;
            }

            if ($amount - self::COIN_TEN >= 0) {
                $amount -= self::COIN_TEN;
                $coins[self::COIN_TEN]++;
            }

            if ($amount - self::COIN_FIVE >= 0) {
                $amount -= self::COIN_FIVE;
                $coins[self::COIN_FIVE]++;
            }

        } while ($amount !== 0);

        return $coins;
    }
}
