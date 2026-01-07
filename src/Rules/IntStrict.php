<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class IntStrict extends Rule
{
    protected string $message = "The :attribute must be integer";

    /**
     * Check the $value is valid
     * @param mixed $value
     */
    function check($value): bool
    {
        return \is_int($value);
    }
}
