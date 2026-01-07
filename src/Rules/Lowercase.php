<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Lowercase extends Rule
{
    protected string $message = "The :attribute must be lowercase";

    /**
     * Check the $value is valid
     * @param mixed $value
     */
    function check($value): bool
    {
        return \mb_strtolower($value, \mb_detect_encoding($value)) === $value;
    }
}
