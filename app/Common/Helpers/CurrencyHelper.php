<?php

namespace App\Common\Helpers;

class CurrencyHelper
{
    private static $currency_signal;
    private static $initialized = false;

    private static function initialize()
    {
        if (self::$initialized)
            return;

        self::$currency_signal = 'đ';
        self::$initialized = true;
    }

    public static function get()
    {
        self::initialize();
        return self::$currency_signal;
    }
}