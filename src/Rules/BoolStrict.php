<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class BoolStrict extends Rule
{
    protected string $message = "The :attribute must be a boolean";

    /**
     * Check the value is valid
     * @param mixed $value
     */
    function check($value): bool
    {
        return \is_bool($value);
    }
}
