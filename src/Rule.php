<?php

namespace Rakit\Validation;

use Rakit\Validation\Attribute;
use Rakit\Validation\Validation;
use Rakit\Validation\MissingRequiredParameterException;

abstract class Rule
{
    /** @var string */
    protected $key;

    /** @var Attribute|null */
    protected $attribute;

    /** @var Validation|null */
    protected $validation;

    /** @var bool */
    protected $implicit = false;

    /** @var array */
    protected $params = [];

    /** @var array */
    protected $paramsTexts = [];

    /** @var array */
    protected $fillableParams = [];

    /** @var string */
    protected $message = "The :attribute is invalid";

    /**
     * @param mixed $value
     */
    abstract public function check($value): bool;

    /**
     * Set Validation class instance
     */
    public function setValidation(Validation $validation): void
    {
        $this->validation = $validation;
    }

    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    /**
     * @return string|class-string
     */
    public function getKey(): string
    {
        return $this->key ?: \get_class($this);
    }

    public function setAttribute(Attribute $attribute): void
    {
        $this->attribute = $attribute;
    }

    public function getAttribute(): ?Attribute
    {
        return $this->attribute;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParameters(array $params): self
    {
        $this->params = \array_merge($this->params, $params);
        return $this;
    }

    /**
     * @param mixed $value
     */
    public function setParameter(string $key, $value): self
    {
        $this->params[$key] = $value;
        return $this;
    }

    /**
     * Fill $params to $this->params
     * @param array $params
     */
    public function fillParameters(array $params): self
    {
        if (!$params) return $this;

        \reset($params);
        foreach ($this->fillableParams as $key) {
            $idx = \key($params);
            // $this->params[$key] = \array_shift($params);
            $this->params[$key] = $idx === null ? null : $params[$idx];
            \next($params);
        }

        return $this;
    }

    /**
     * Get parameter from given $key, return null if it not exists
     * @return mixed
     */
    public function parameter(string $key)
    {
        return $this->params[$key] ?? null;
    }

    /**
     * Set parameter text that can be displayed in error message using ':param_key'
     */
    public function setParameterText(string $key, string $text): void
    {
        $this->paramsTexts[$key] = $text;
    }

    /**
     * Get $paramsTexts
     * @return array
     */
    public function getParametersTexts(): array
    {
        return $this->paramsTexts;
    }

    /**
     * Check whether this rule is implicit
     */
    public function isImplicit(): bool
    {
        return $this->implicit;
    }

    /**
     * Just alias of setMessage
     */
    public function message(string $message): self
    {
        return $this->setMessage($message);
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Check given $params must be exists
     * @param array $params
     * @throws \Rakit\Validation\MissingRequiredParameterException
     */
    protected function requireParameters(array $params): void
    {
        foreach ($params as $param) {
            if (!isset($this->params[$param])) {
                throw new MissingRequiredParameterException(
                    'Missing required parameter "' . $param . '" on rule "' . $this->getKey() . '"'
                );
            }
        }
    }
}
