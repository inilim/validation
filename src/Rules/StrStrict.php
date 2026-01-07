<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class StrStrict extends Rule
{

    /** @var string */
    protected $message = "The :attribute must be string";

    /**
     * Check the $value is valid
     *
     * @param mixed $value
     */
    public function check($value): bool
    {
        return \is_string($value);
    }
}
