<?php

namespace App\Common\Interfaces;

interface IValidate
{
    /** VALIDATION */
    public function validateInput($data);

    public function validateEmpty($data);

    public function validateLogic($data);
}