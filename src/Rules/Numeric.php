<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Numeric extends Rule
{
    protected string $message = "The :attribute must be numeric";

    /**
     * Check the $value is valid
     * @param mixed $value
     */
    function check($value): bool
    {
        return \is_numeric($value);
    }
}
