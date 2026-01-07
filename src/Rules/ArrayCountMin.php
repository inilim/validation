<?php

namespace Rakit\Validation\Rules;

use Inilim\Tool\VD;
use Rakit\Validation\Rule;

class ArrayCountMin extends Rule
{
    protected string $message = "The :attribute must be array minimum is count :min";
    /** @var string[] */
    protected array $fillableParams = ['min'];

    /**
     * Check the $value is valid
     * @param mixed $value
     */
    function check($value): bool
    {
        $this->requireParameters($this->fillableParams);

        $count = $this->parameter('min');
        if (!\is_numeric($count)) {
            throw new \InvalidArgumentException('Size must be numeric', 1);
        }
        $count = (int)$count;

        if (!\is_array($value)) {
            return \false;
        }

        return \count($value) >= $count;
    }
}
