<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class TypeArray extends Rule
{
    protected string $message = "The :attribute must be array";

    /**
     * Check the $value is valid
     * @param mixed $value
     */
    function check($value): bool
    {
        return \is_array($value);
    }
}
