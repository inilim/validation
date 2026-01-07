<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Scalar extends Rule
{
    /** @var string */
    protected $message = "The :attribute must be a scalar";

    /**
     * Check the value is valid
     *
     * @param mixed $value
     */
    public function check($value): bool
    {
        return \is_scalar($value);
    }
}
