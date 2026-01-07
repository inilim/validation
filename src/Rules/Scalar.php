<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Scalar extends Rule
{
    protected string $message = "The :attribute must be a scalar";

    /**
     * Check the value is valid
     * @param mixed $value
     */
    function check($value): bool
    {
        return \is_scalar($value);
    }
}
