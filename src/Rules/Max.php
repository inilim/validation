<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Max extends Rule
{
    use Traits\SizeTrait;

    protected string $message = "The :attribute maximum is :max";
    /** @var array */
    protected array $fillableParams = ['max'];

    /**
     * Check the $value is valid
     * @param mixed $value
     */
    function check($value): bool
    {
        $this->requireParameters($this->fillableParams);

        $max = $this->getBytesSize($this->parameter('max'));
        $valueSize = $this->getValueSize($value);

        if (!\is_numeric($valueSize)) {
            return \false;
        }

        return $valueSize <= $max;
    }
}
