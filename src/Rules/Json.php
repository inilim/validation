<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Json extends Rule
{
    protected string $message = "The :attribute must be a valid JSON string";

    /**
     * Check the $value is valid
     * @param mixed $value
     */
    function check($value): bool
    {
        if (! \is_string($value) || empty($value)) {
            return \false;
        }

        if (\PHP_VERSION_ID >= 80300) {
            return \json_validate($value);
        }

        \json_decode($value);

        return \json_last_error() === \JSON_ERROR_NONE;
    }
}
