<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;
use Rakit\Validation\Rules\Interfaces\ModifyValue;

class Defaults extends Rule implements ModifyValue
{
    protected string $message = "The :attribute default is :default";
    /** @var array */
    protected array $fillableParams = ['default'];

    /**
     * Check the $value is valid
     * @param mixed $value
     */
    function check($value): bool
    {
        $this->requireParameters($this->fillableParams);
        // $default = $this->parameter('default');
        return \true;
    }

    /**
     * {@inheritDoc}
     */
    function modifyValue($value)
    {
        return $this->isEmptyValue($value) ? $this->parameter('default') : $value;
    }

    /**
     * Check $value is empty value
     *
     * @param mixed $value
     */
    protected function isEmptyValue($value): bool
    {
        return false === (new Required)->check($value);
    }
}
