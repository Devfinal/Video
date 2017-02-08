<?php

namespace App;

use ReflectionClass;

class TransactionReason
{
    const CUSTOMER_LIKE = 1;
    const ADMIN_EDIT = 2;
    const USER_REGISTRATION = 4;
    const SUBSCRIPTION_PAYMENT = 8;
    const PURCHASE = 16;
    const REFUND = 32;
    const ADMIN_LIKE = 64;

    protected static $strings = [
        1 => 'Customer like',
        2 => 'Admin edit',
        4 => 'User registration',
        8 => 'Subscription payment',
        16 => 'Purchase',
        32 => 'Refund',
        64 => 'Admin like'
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
     * Returns the string representation of the given reason.
     *
     * @param string $reason
     * @return string
     */
    public static function getString($reason)
    {
        return array_key_exists($reason, static::$strings) ? static::$strings[$reason] : '';
    }
}
