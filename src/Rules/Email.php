<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Email extends Rule
{
    protected string $message = "The :attribute is not valid email";

    /**
     * Check $value is valid
     * @param mixed $value
     */
    function check($value): bool
    {
        return \filter_var($value, \FILTER_VALIDATE_EMAIL) !== \false;
    }
}
