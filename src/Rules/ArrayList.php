<?php

namespace Rakit\Validation\Rules;

use Inilim\Tool\VD;
use Rakit\Validation\Rule;

class ArrayList extends Rule
{
    protected string $message = "The :attribute must be array list";

    /**
     * @param mixed $value
     */
    function check($value): bool
    {
        if (!\is_array($value)) {
            return \false;
        }

        if (\PHP_VERSION_ID >= 80100) {
            return \array_is_list($value);
        }

        if ([] === $value || $value === \array_values($value)) {
            return \true;
        }

        $nextKey = -1;

        foreach ($value as $k => $v) {
            if ($k !== ++$nextKey) {
                return \false;
            }
        }

        return \true;
    }
}
