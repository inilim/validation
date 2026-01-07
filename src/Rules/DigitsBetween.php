<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class DigitsBetween extends Rule
{
    protected string $message = "The :attribute must have a length between the given :min and :max";
    /** @var array */
    protected array $fillableParams = ['min', 'max'];

    /**
     * Check the $value is valid
     * @param mixed $value
     */
    function check($value): bool
    {
        $this->requireParameters($this->fillableParams);

        // TODO чек что они могут быть int
        $min = (int) $this->parameter('min');
        $max = (int) $this->parameter('max');

        $length = \strlen((string) $value);

        return ! \preg_match('/[^0-9]/', $value)
            && $length >= $min && $length <= $max;
    }
}
