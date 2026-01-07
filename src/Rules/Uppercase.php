<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Uppercase extends Rule
{
    protected string $message = "The :attribute must be uppercase";

    /**
     * Check the $value is valid
     * @param mixed $value
     */
    function check($value): bool
    {
        return \mb_strtoupper($value, \mb_detect_encoding($value)) === $value;
    }
}
