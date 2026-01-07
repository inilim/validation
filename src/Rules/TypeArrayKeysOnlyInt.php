<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class TypeArrayKeysOnlyInt extends Rule
{

    /** @var string */
    protected $message = "The :attribute must be an array with integer keys";

    /**
     * Check the $value is valid
     *
     * @param mixed $value
     * @return bool
     */
    public function check($value): bool
    {
        if (!\is_array($value)) {
            return false;
        }
        foreach ($value as $key => $_) {
            if (!\is_int($key)) {
                return false;
            }
        }
        return true;
    }
}
