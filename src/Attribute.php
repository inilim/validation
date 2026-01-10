<?php

namespace Rakit\Validation;

use Rakit\Validation\Rule;
use Rakit\Validation\Validation;

final class Attribute
{
    /** @var array<string,Rule> */
    protected array $rules = [];

    protected string $key;

    protected ?string $alias;

    protected Validation $validation;

    protected bool $required = false;

    protected ?Attribute $primaryAttribute = null;

    /** @var array */
    protected array $otherAttributes = [];

    /** @var array */
    protected array $keyIndexes = [];

    /**
     * @param Rule[] $rules
     */
    function __construct(
        Validation $validation,
        ?string $key,
        ?string $alias = null,
        array $rules = []
    ) {
        $this->validation = $validation;
        $this->alias      = $alias;
        $this->key        = $key;
        foreach ($rules as $rule) {
            $this->addRule($rule);
        }
    }

    /**
     * Set the primary attribute
     */
    function setPrimaryAttribute(Attribute $primaryAttribute): void
    {
        $this->primaryAttribute = $primaryAttribute;
    }

    /**
     * Set key indexes
     */
    function setKeyIndexes(array $keyIndexes): void
    {
        $this->keyIndexes = $keyIndexes;
    }

    /**
     * Get primary attributes
     */
    function getPrimaryAttribute(): ?Attribute
    {
        return $this->primaryAttribute;
    }

    /**
     * Set other attributes
     * @param array $otherAttributes
     */
    function setOtherAttributes(array $otherAttributes): void
    {
        $this->otherAttributes = [];
        foreach ($otherAttributes as $otherAttribute) {
            $this->addOtherAttribute($otherAttribute);
        }
    }

    /**
     * Add other attributes
     */
    function addOtherAttribute(Attribute $otherAttribute): void
    {
        $this->otherAttributes[] = $otherAttribute;
    }

    /**
     * Get other attributes
     * @return array
     */
    function getOtherAttributes(): array
    {
        return $this->otherAttributes;
    }

    /**
     * Add rule
     */
    function addRule(Rule $rule): void
    {
        $rule->setAttribute($this);
        $rule->setValidation($this->validation);
        $this->rules[$rule->getKey()] = $rule;
    }

    /**
     * Get rule
     */
    function getRule(string $ruleKey): ?Rule
    {
        return $this->rules[$ruleKey] ?? null;
    }

    /**
     * Get rules
     * @return array<string,Rule>
     */
    function getRules(): array
    {
        return $this->rules;
    }

    /**
     * Check the $ruleKey has in the rule
     */
    function hasRule(string $ruleKey): bool
    {
        return isset($this->rules[$ruleKey]);
    }

    /**
     * Set required
     */
    function setRequired(bool $required): void
    {
        $this->required = $required;
    }

    /**
     * Set rule is required
     */
    function isRequired(): bool
    {
        return $this->required;
    }

    /**
     * Get key
     */
    function getKey(): string
    {
        return $this->key;
    }

    /**
     * Get key indexes
     * @return array
     */
    function getKeyIndexes(): array
    {
        return $this->keyIndexes;
    }

    /**
     * Get value
     * @return mixed
     */
    function getValue(?string $key = null)
    {
        if ($key && $this->isArrayAttribute()) {
            $key = $this->resolveSiblingKey($key);
        }

        if (!$key) {
            $key = $this->getKey();
        }

        return $this->validation->getValue($key);
    }

    /**
     * Get that is array attribute
     */
    function isArrayAttribute(): bool
    {
        return \count($this->getKeyIndexes()) > 0;
    }

    /**
     * Check this attribute is using dot notation
     */
    function isUsingDotNotation(): bool
    {
        return \strpos($this->getKey(), '.') !== \false;
    }

    /**
     * Resolve sibling key
     */
    function resolveSiblingKey(string $key): string
    {
        $indexes        = $this->getKeyIndexes();
        $keys           = \explode('*', $key);
        $countAsterisks = \count($keys) - 1;
        if (\count($indexes) < $countAsterisks) {
            $indexes = \array_merge($indexes, \array_fill(0, $countAsterisks - \count($indexes), "*"));
        }
        // $args = \array_merge([\str_replace('*', '%s', $key)], $indexes);
        $args = \array_merge([\strtr($key, ['*' => '%s'])], $indexes);

        return \call_user_func_array('sprintf', $args);
    }

    /**
     * Get humanize key
     */
    function getHumanizedKey(): string
    {
        $primaryAttribute = $this->getPrimaryAttribute();
        // $key              = \str_replace('_', ' ', $this->key);
        $key              = \strtr($this->key, ['_' => ' ']);

        // Resolve key from array validation
        if ($primaryAttribute) {
            $split = \explode('.', $key);
            $key   = \implode(' ',  \array_map(static function (string $word) {
                if (\is_numeric($word)) {
                    $word = $word + 1;
                }
                return Helper::snakeCase($word, ' ');
            }, $split));
        }

        return \ucfirst($key);
    }

    /**
     * Set alias
     */
    function setAlias(string $alias): void
    {
        $this->alias = $alias;
    }

    /**
     * Get alias
     */
    function getAlias(): ?string
    {
        return $this->alias;
    }
}
