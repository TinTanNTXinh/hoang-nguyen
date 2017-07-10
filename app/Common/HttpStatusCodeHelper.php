<?php

namespace App\Common;

class HttpStatusCodeHelper
{
    static public $ok = 200;
    static public $created = 201;
    static public $unauthorized = 401;
    static public $forbidden = 403;
    static public $notFound = 404;
    static public $unprocessableEntity = 422;
}