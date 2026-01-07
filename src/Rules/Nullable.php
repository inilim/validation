<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Nullable extends Rule
{
    /**
     * Check the $value is valid
     * @param mixed $value
     */
    function check($value): bool
    {
        return true;
    }
}
