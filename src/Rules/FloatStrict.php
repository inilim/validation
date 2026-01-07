<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class FloatStrict extends Rule
{
    protected string $message = "The :attribute must be float";

    /**
     * Check the $value is valid
     * @param mixed $value
     */
    function check($value): bool
    {
        return \is_float($value);
    }
}
