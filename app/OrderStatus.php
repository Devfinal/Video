<?php

namespace App;

use ReflectionClass;

class OrderStatus
{
    const PENDING = 1;
    const PROCESSING = 2;
    const SHIPPED = 4;
    const CANCELED = 8;

    protected static $strings = [
        1 => 'Pending',
        2 => 'Processing',
        4 => 'Shipped',
        8 => 'Canceled'
    ];

    /**
     * Returns the constant values.
     *
     * @return array
     */
    public static function enum()
    {
        $reflection = new ReflectionClass(__CLASS__);

        return array_values($reflection->getConstants());
    }

    /**
     * Returns the string representation of the given currency.
     *
     * @param int $currency
     * @return string
     */
    public static function getString($currency)
    {
        return array_key_exists($currency, static::$strings) ? static::$strings[$currency] : '';
    }
}
