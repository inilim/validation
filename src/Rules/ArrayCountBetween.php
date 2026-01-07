<?php

namespace Rakit\Validation\Rules;

use Inilim\Tool\VD;
use Rakit\Validation\Rule;

class ArrayCountBetween extends Rule
{
    /** @var string */
    protected $message = "The :attribute must be array count between :min and :max";

    /** @var array */
    protected $fillableParams = ['min', 'max'];

    /**
     * Check the $value is valid
     * @param mixed $value
     */
    public function check($value): bool
    {
        $this->requireParameters($this->fillableParams);

        if (!\is_array($value)) {
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

        $valueSize = \count($value);
        return ($valueSize >= $min && $valueSize <= $max);
    }
}
