<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Helper;
use Rakit\Validation\Rule;

class In extends Rule
{
    protected string $message = "The :attribute only allows :allowed_values";
    protected bool $strict = false;

    /**
     * Given $params and assign the $this->params
     * @param array $params
     * @return self
     */
    function fillParameters(array $params): Rule
    {
        if (\count($params) == 1 && \is_array($params[0])) {
            $params = $params[0];
        }
        $this->params['allowed_values'] = $params;
        return $this;
    }

    /**
     * Set strict value
     * @param bool $strict
     * @return void
     */
    function strict(bool $strict = \true)
    {
        $this->strict = $strict;
    }

    /**
     * Check $value is existed
     *
     * @param mixed $value
     */
    function check($value): bool
    {
        $this->requireParameters(['allowed_values']);

        $allowedValues = $this->parameter('allowed_values');

        $or = $this->validation ? $this->validation->getTranslation('or') : 'or';
        $allowedValuesText = Helper::join(Helper::wraps($allowedValues, "'"), ', ', ", {$or} ");
        $this->setParameterText('allowed_values', $allowedValuesText);

        return \in_array($value, $allowedValues, $this->strict);
    }
}
