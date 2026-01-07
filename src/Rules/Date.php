<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Date extends Rule
{
    protected string $message = "The :attribute is not valid date format";
    /** @var array */
    protected array $fillableParams = ['format'];
    /** @var array */
    protected array $params = [
        'format' => 'Y-m-d'
    ];

    /**
     * Check the $value is valid
     * @param mixed $value
     */
    function check($value): bool
    {
        $this->requireParameters($this->fillableParams);

        $format = $this->parameter('format');
        return \date_create_from_format($format, $value) !== \false;
    }
}
