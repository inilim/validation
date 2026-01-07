<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

class Required extends Rule
{
    use Traits\FileTrait;

    protected bool $implicit = true;
    protected string $message = "The :attribute is required";

    /**
     * Check the $value is valid
     * @param mixed $value
     */
    function check($value): bool
    {
        $this->setAttributeAsRequired();

        if (isset($this->attribute) and $this->attribute->hasRule('uploaded_file')) {
            return $this->isValueFromUploadedFiles($value) and $value['error'] != \UPLOAD_ERR_NO_FILE;
        }
        $type = \gettype($value);
        if ($type === 'string') {
            /** @var string $value */
            return \mb_strlen(\trim($value), 'UTF-8') > 0;
        }
        if ($type === 'array') {
            /** @var array $value */
            return \count($value) > 0;
        }
        return $value !== null;
    }

    /**
     * Set attribute is required if $this->attribute is set
     *
     * @return void
     */
    protected function setAttributeAsRequired()
    {
        if (isset($this->attribute)) {
            $this->attribute->setRequired(true);
        }
    }
}
