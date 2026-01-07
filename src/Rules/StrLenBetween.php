<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class StrLenBetween extends Rule
{
    protected string $message = "The :attribute must be string length between :min and :max";
    protected array $fillableParams = ['min', 'max'];

    /**
     * Check the $value is valid
     * @param mixed $value
     */
    function check($value): bool
    {
        $this->requireParameters($this->fillableParams);

        if (!\is_string($value)) {
            return \false;
        }

        $max = $this->parameter('max');
        if (!\is_numeric($max)) {
            throw new \InvalidArgumentException('Size max must be numeric', 1);
        }
        $min = $this->parameter('min');
        if (!\is_numeric($min)) {
            throw new \InvalidArgumentException('Size min must be numeric', 1);
        }
        $max = (int)$max;
        $min = (int)$min;

        $valueSize = \mb_strlen($value, \mb_detect_encoding($value));
        return ($valueSize >= $min && $valueSize <= $max);
    }
}
