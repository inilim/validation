<?php

namespace Rakit\Validation;

use Rakit\Validation\Attribute;
use Rakit\Validation\Validation;
use Rakit\Validation\MissingRequiredParameterException;

abstract class Rule
{
    protected string $key;

    protected ?Attribute $attribute;

    protected ?Validation $validation;

    protected bool $implicit = false;

    /** @var array */
    protected array $params = [];

    /** @var array */
    protected array $paramsTexts = [];

    /** @var string[] */
    protected array $fillableParams = [];

    protected string $message = "The :attribute is invalid";

    /**
     * @param mixed $value
     */
    abstract function check($value): bool;

    /**
     * Set Validation class instance
     */
    function setValidation(Validation $validation): void
    {
        $this->validation = $validation;
    }

    function setKey(string $key): void
    {
        $this->key = $key;
    }

    /**
     * @return string|class-string<Rule>
     */
    function getKey(): string
    {
        return $this->key ?: \get_class($this);
    }

    function setAttribute(Attribute $attribute): void
    {
        $this->attribute = $attribute;
    }

    function getAttribute(): ?Attribute
    {
        return $this->attribute;
    }

    /**
     * @return array
     */
    function getParameters(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     */
    function setParameters(array $params): self
    {
        $this->params = \array_merge($this->params, $params);
        return $this;
    }

    /**
     * @param mixed $value
     */
    function setParameter(string $key, $value): self
    {
        $this->params[$key] = $value;
        return $this;
    }

    /**
     * Fill $params to $this->params
     * @param array $params
     */
    function fillParameters(array $params): self
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
    function parameter(string $key)
    {
        return $this->params[$key] ?? null;
    }

    /**
     * Set parameter text that can be displayed in error message using ':param_key'
     */
    function setParameterText(string $key, string $text): void
    {
        $this->paramsTexts[$key] = $text;
    }

    /**
     * Get $paramsTexts
     * @return array
     */
    function getParametersTexts(): array
    {
        return $this->paramsTexts;
    }

    /**
     * Check whether this rule is implicit
     */
    function isImplicit(): bool
    {
        return $this->implicit;
    }

    /**
     * Just alias of setMessage
     */
    function message(string $message): self
    {
        return $this->setMessage($message);
    }

    function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    function getMessage(): string
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
