<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class StrStrict extends Rule
{
    protected string $message = "The :attribute must be string";

    /**
     * Check the $value is valid
     * @param mixed $value
     */
    function check($value): bool
    {
        return \is_string($value);
    }
}
