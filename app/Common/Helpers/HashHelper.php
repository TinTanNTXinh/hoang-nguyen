<?php

namespace App\Common\Helpers;

use Hash;

class HashHelper
{
    static public function make($string)
    {
        return Hash::make($string);
    }

    static public function check($left, $right)
    {
        return Hash::check($left, $right);
    }
}