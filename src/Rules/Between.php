<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Between extends Rule
{
    use Traits\SizeTrait;

    protected string $message = "The :attribute must be between :min and :max";
    /** @var string[] */
    protected array $fillableParams = ['min', 'max'];

    /**
     * Check the $value is valid
     * @param mixed $value
     */
    function check($value): bool
    {
        $this->requireParameters($this->fillableParams);

        $min = $this->getBytesSize($this->parameter('min'));
        $max = $this->getBytesSize($this->parameter('max'));

        $valueSize = $this->getValueSize($value);

        if (!\is_numeric($valueSize)) {
            return \false;
        }

        return ($valueSize >= $min && $valueSize <= $max);
    }
}
