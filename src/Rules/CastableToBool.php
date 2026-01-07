<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class CastableToBool extends Rule
{
    protected string $message = "The :attribute must be a castable to boolean";

    /**
     * Check the value is valid
     * @param mixed $value
     * @throws \Exception
     */
    function check($value): bool
    {
        return \in_array($value, [\true, \false, "true", "false", 1, 0, "0", "1", "y", "n"], \true);
    }
}
