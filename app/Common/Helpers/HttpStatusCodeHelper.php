<?php

namespace App\Common\Helpers;

class HttpStatusCodeHelper
{
    static public $ok = 200;
    static public $created = 201;
    static public $noContent = 204;
    static public $unauthorized = 401;
    static public $forbidden = 403;
    static public $notFound = 404;
    static public $unprocessableEntity = 422;
}