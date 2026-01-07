<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class FloatStrict extends Rule
{

    /** @var string */
    protected $message = "The :attribute must be float";

    /**
     * Check the $value is valid
     *
     * @param mixed $value
     * @return bool
     */
    public function check($value): bool
    {
        return \is_float($value);
    }
}
