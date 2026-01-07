<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class BoolStrict extends Rule
{
    /** @var string */
    protected $message = "The :attribute must be a boolean";

    /**
     * Check the value is valid
     *
     * @param mixed $value
     */
    public function check($value): bool
    {
        return \is_bool($value);
    }
}
