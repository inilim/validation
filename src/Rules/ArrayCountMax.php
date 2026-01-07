<?php

namespace Rakit\Validation\Rules;

use Inilim\Tool\VD;
use Rakit\Validation\Rule;

class ArrayCountMax extends Rule
{
    /** @var string */
    protected $message = "The :attribute must be array maximum is count :max";

    /** @var array */
    protected $fillableParams = ['max'];

    /**
     * Check the $value is valid
     * @param mixed $value
     */
    public function check($value): bool
    {
        $this->requireParameters($this->fillableParams);

        $count = $this->parameter('max');
        if (!\is_numeric($count)) {
            throw new \InvalidArgumentException('Size must be numeric', 1);
        }
        $count = (int)$count;

        if (!\is_array($value)) {
            return \false;
        }

        return \count($value) <= $count;
    }
}
