<?php

namespace App;

use ReflectionClass;

class TransactionCurrency
{
    const HEARTS = 1;
    const EUR = 978;

    protected static $strings = [
        1 => 'HEARTS',
        978 => 'EUR'
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
     * Returns the string representation of the given currency.
     *
     * @param string $currency
     * @return string
     */
    public static function getString($currency)
    {
        return array_key_exists($currency, static::$strings) ? static::$strings[$currency] : '';
    }
}
