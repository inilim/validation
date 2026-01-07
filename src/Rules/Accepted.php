<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Accepted extends Rule
{
    protected bool $implicit = true;
    protected string $message = "The :attribute must be accepted";

    /**
     * Check the $value is accepted
     * @param mixed $value
     */
    function check($value): bool
    {
        return \in_array($value, ['yes', 'on', '1', 1, \true, 'true'], \true);
    }
}
