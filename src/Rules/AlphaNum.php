<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class AlphaNum extends Rule
{
    protected string $message = "The :attribute only allows alphabet and numeric";

    /**
     * Check the $value is valid
     * @param mixed $value
     */
    function check($value): bool
    {
        if (! \is_string($value) && ! \is_numeric($value)) {
            return \false;
        }

        return \preg_match('/^[\pL\pM\pN]+$/u', $value) > 0;
    }
}
