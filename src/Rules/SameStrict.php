<?php

namespace Rakit\Validation\Rules;

use Inilim\Tool\VD;
use Rakit\Validation\Rule;

class SameStrict extends Rule
{
    protected string $message = "The :attribute must be same with :field";

    /** @var string[] */
    protected array $fillableParams = ['field'];

    // protected bool $implicit = true;  // We removed implicit flag to prevent breaking validation chain

    /**
     * Check the $value is valid
     * @param mixed $value
     */
    function check($value): bool
    {
        $this->requireParameters($this->fillableParams);
        $field = $this->parameter('field');
        $anotherValue = $this->getAttribute()->getValue($field);
        return $value === $anotherValue;
    }
}
