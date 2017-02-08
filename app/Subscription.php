<?php

namespace App;

use Carbon\CarbonInterval;
use ReflectionClass;

class Subscription
{
    const SUBSCRIPTION_6_MONTHS = 1;
    const SUBSCRIPTION_1_MONTHS = 2;
    const SUBSCRIPTION_2_WEEKS = 3;

    protected static $data = [
        1 => [
            'amount' => 69.90,
            'currency' => TransactionCurrency::EUR,
            'duration' => [0, 6],
            'description' => 'Subscription service'
        ],
        2 => [
            'amount' => 18.90,
            'currency' => TransactionCurrency::EUR,
            'duration' => [0, 1],
            'description' => 'Subscription service'
        ],
        3 => [
            'amount' => 11.90,
            'currency' => TransactionCurrency::EUR,
            'duration' => [0, 0, 2],
            'description' => 'Subscription service'
        ]
    ];

    /**
     * Returns the constant values.
     *
     * @return array
     */
    public static function enum()
    {
        return array_values((new ReflectionClass(__CLASS__))->getConstants());
    }

    /**
     * Returns the data for the given subscription.
     *
     * @param int $subscription
     * @return array
     */
    public static function getData($subscription)
    {
        return array_key_exists($subscription, static::$data) ?
            collect(static::$data[$subscription])->map(function ($value, $key) {
                if ($key === 'duration') {
                    return CarbonInterval::create(...$value);
                }

                return $value;
            }) :
            [
                'amount' => 0,
                'currency' => 0,
                'duration' => CarbonInterval::create(),
                'description' => ''
            ];
    }
}
