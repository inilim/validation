<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class After extends Rule
{
    use Traits\DateUtilsTrait;

    protected string $message = "The :attribute must be a date after :time.";
    /** @var string[] */
    protected array $fillableParams = ['time'];

    /**
     * Check the value is valid
     * @param mixed $value
     * @throws \Exception
     */
    function check($value): bool
    {
        $this->requireParameters($this->fillableParams);
        $time = $this->parameter('time');

        if (!$this->isValidDate($value)) {
            throw $this->throwException($value);
        }

        if (!$this->isValidDate($time)) {
            throw $this->throwException($time);
        }

        return $this->getTimeStamp($time) < $this->getTimeStamp($value);
    }
}
