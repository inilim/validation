<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class RequiredIf extends Required
{
    protected bool $implicit = true;
    protected string $message = "The :attribute is required";

    /**
     * Given $params and assign the $this->params
     *
     * @param array $params
     * @return self
     */
    public function fillParameters(array $params): Rule
    {
        $this->params['field'] = \array_shift($params);
        $this->params['values'] = $params;
        return $this;
    }

    /**
     * @param mixed $value
     */
    function check($value): bool
    {
        $this->requireParameters(['field', 'values']);

        $anotherAttribute = $this->parameter('field');
        $definedValues = $this->parameter('values');
        $anotherValue = $this->getAttribute()->getValue($anotherAttribute);

        $requiredValidator = $this->validation->getValidator()->__invoke('required');

        if (\in_array($anotherValue, $definedValues)) {
            $this->setAttributeAsRequired();
            return $requiredValidator->check($value, []);
        }

        return true;
    }
}
