<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Json extends Rule
{

    /** @var string */
    protected $message = "The :attribute must be a valid JSON string";

    /**
     * Check the $value is valid
     *
     * @param mixed $value
     */
    public function check($value): bool
    {
        if (! \is_string($value) || empty($value)) {
            return \false;
        }

        if (\PHP_VERSION_ID >= 80300) {
            return \json_validate($value);
        }

        \json_decode($value);

        if (\json_last_error() !== \JSON_ERROR_NONE) {
            return \false;
        }

        return \true;
    }
}
