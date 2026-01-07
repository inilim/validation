<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class TypeArrayKeysOnlyInt extends Rule
{
    protected string $message = "The :attribute must be an array with integer keys";

    /**
     * Check the $value is valid
     * @param mixed $value
     */
    function check($value): bool
    {
        if (!\is_array($value)) {
            return \false;
        }
        foreach ($value as $key => $_) {
            if (!\is_int($key)) {
                return \false;
            }
        }
        return \true;
    }
}
