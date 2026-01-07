<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Regex extends Rule
{
    protected string $message = "The :attribute is not valid format";
    /** @var array */
    protected array $fillableParams = ['regex'];

    /**
     * Check the $value is valid
     * @param mixed $value
     */
    function check($value): bool
    {
        $this->requireParameters($this->fillableParams);
        $regex = $this->parameter('regex');
        return \preg_match($regex, $value) > 0;
    }
}
