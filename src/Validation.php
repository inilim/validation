<?php

namespace Rakit\Validation;

use Closure;
use Inilim\Tool\VD;
use Rakit\Validation\Rule;
use Rakit\Validation\ErrorBag;
use Rakit\Validation\Attribute;
use Rakit\Validation\Validator;
use Rakit\Validation\Rules\Required;
use Rakit\Validation\Rules\Interfaces\ModifyValue;
use Rakit\Validation\Rules\Interfaces\BeforeValidate;

/**
 * @psalm-import-type TypeVarRulesInstruction from Validator
 * @psalm-import-type TypeVarRuleInstraction from Validator
 */
class Validation
{
    use Traits\TranslationsTrait, Traits\MessagesTrait;

    protected Validator $validator;

    /** @var mixed[] */
    protected array $inputs = [];

    /** @var Attribute[] */
    protected array $attributes = [];

    /** @var array */
    protected array $aliases = [];

    protected string $messageSeparator = ':';

    /** @var mixed[] */
    protected array $validData = [];

    /** @var mixed[] */
    protected array $invalidData = [];

    protected ErrorBag $errors;

    protected Required $requiredRule;

    protected bool $hasBeforeValidateRule = false;
    protected bool $hasModifyValueRule    = false;

    /**
     * Constructor
     * @param \Rakit\Validation\Validator $validator
     * @param array $inputs
     * @param TypeVarRulesInstruction $rulesInstruction
     * @param array $messages
     */
    function __construct(
        Validator $validator,
        array $inputs,
        array $rulesInstruction,
        array $messages = []
    ) {
        $this->validator    = $validator;
        $this->inputs       = $this->resolveInputAttributes($inputs);
        $this->messages     = $messages;
        $this->errors       = new ErrorBag;
        $this->requiredRule = new Required;
        foreach ($rulesInstruction as $attributeKey => $ruleInstruction) {
            /** @var TypeVarRuleInstraction $ruleInstruction */
            $this->addAttribute($attributeKey, $ruleInstruction);
        }
    }

    /**
     * Add attribute rules
     * @param TypeVarRuleInstraction $ruleInstruction
     */
    function addAttribute(string $attributeKey, $ruleInstruction): void
    {
        $resolvedRuleInstruction = $this->resolveRulesInstruction($ruleInstruction);

        $this->attributes[$attributeKey] = new Attribute(
            $this,
            $attributeKey,
            $this->getAlias($attributeKey),
            $resolvedRuleInstruction
        );
    }

    /**
     * Get attribute by key
     */
    function getAttribute(string $attributeKey): ?Attribute
    {
        return $this->attributes[$attributeKey] ?? null;
    }

    /**
     * Run validation
     */
    function validate(array $inputs = []): self
    {
        $this->errors = new ErrorBag; // reset error bag
        if ($inputs) {
            $this->inputs = \array_merge($this->inputs, $this->resolveInputAttributes($inputs));
        }

        // Before validation hooks
        if ($this->hasBeforeValidateRule) {
            foreach ($this->attributes as $attribute) {
                foreach ($attribute->getRules() as $rule) {
                    if ($rule instanceof BeforeValidate) {
                        $rule->beforeValidate();
                    }
                }
            }
        }

        foreach ($this->attributes as $attribute) {
            if ($this->isArrayAttribute($attribute)) {
                foreach ($this->parseArrayAttribute($attribute) as $attr) {
                    $this->validateAttribute($attr);
                }
            } else {
                $this->validateAttribute($attribute);
            }
        }

        return $this;
    }

    /**
     * Get ErrorBag instance
     */
    function errors(): ErrorBag
    {
        return $this->errors;
    }

    /**
     * Get all of the exact attribute values for a given wildcard attribute.
     * Adapted from: https://github.com/illuminate/validation/blob/v5.3.23/Validator.php#L354
     * @param  array  $data
     * @return array
     */
    function extractValuesForWildcards(array $data, string $attributeKey): array
    {
        $keys = [];

        $pattern = \str_replace('\*', '[^\.]+', \preg_quote($attributeKey));

        foreach ($data as $key => $value) {
            if ((bool) \preg_match('/^' . $pattern . '/', $key, $matches)) {
                $keys[$matches[0]] = null;
            }
        }

        $keys = \array_keys($keys);

        $data = [];

        foreach ($keys as $key) {
            $data[$key] = Helper::arrayGet($this->inputs, $key);
        }

        return $data;
    }



    /**
     * Given $attributeKey and $alias then assign alias
     */
    function setAlias(string $attributeKey, string $alias): void
    {
        $this->aliases[$attributeKey] = $alias;
    }

    /**
     * Get attribute alias from given key
     */
    function getAlias(string $attributeKey): ?string
    {
        return $this->aliases[$attributeKey] ?? null;
    }

    /**
     * Set attributes aliases
     * @param array $aliases
     */
    function setAliases(array $aliases): void
    {
        $this->aliases = \array_merge($this->aliases, $aliases);
    }

    /**
     * Check validations are passed
     */
    function passes(): bool
    {
        return $this->errors->count() === 0;
    }

    /**
     * Check validations are failed
     */
    function fails(): bool
    {
        return !$this->passes();
    }

    /**
     * Given $key and get value
     * @return mixed
     */
    function getValue(string $key)
    {
        return Helper::arrayGet($this->inputs, $key);
    }

    /**
     * Set input value
     * @param mixed $value
     */
    function setValue(string $key, $value): void
    {
        Helper::arraySet($this->inputs, $key, $value);
    }

    /**
     * Given $key and check value is exsited
     */
    function hasValue(string $key): bool
    {
        return Helper::arrayHas($this->inputs, $key);
    }

    /**
     * Get Validator class instance
     */
    function getValidator(): Validator
    {
        return $this->validator;
    }

    /**
     * Get validated data
     * @return array
     */
    function getValidatedData(): array
    {
        return \array_merge($this->validData, $this->invalidData);
    }

    /**
     * Get valid data
     * @return array
     */
    function getValidData(): array
    {
        return $this->validData;
    }

    /**
     * Get invalid data
     * @return array
     */
    function getInvalidData(): array
    {
        return $this->invalidData;
    }

    /**
     * Gather a copy of the attribute data filled with any missing attributes.
     * Adapted from: https://github.com/illuminate/validation/blob/v5.3.23/Validator.php#L334
     * @return array
     */
    protected function initializeAttributeOnData(string $attributeKey): array
    {
        $data = $this->extractDataFromPath(
            $this->getLeadingExplicitAttributePath($attributeKey)
        );
        $asteriskPos = \strpos($attributeKey, '*');

        if (\false === $asteriskPos || $asteriskPos === (\mb_strlen($attributeKey, 'UTF-8') - 1)) {
            return $data;
        }

        return Helper::arraySet($data, $attributeKey, null, true);
    }

    /**
     * Validate attribute
     */
    protected function validateAttribute(Attribute $attribute): void
    {
        $key          = $attribute->getKey();
        $hasKey       = Helper::arrayHas($this->inputs, $key);
        $value        = $hasKey ? Helper::arrayGet($this->inputs, $key) : null;
        $isEmptyValue = \false === $this->requiredRule->check($value);
        // if ($attribute->hasRule('nullable') && $isEmptyValue) {
        //     $rules = [];
        // } else {
        //     $rules = $attribute->getRules();
        // }

        $rules = $attribute->getRules();

        $isValid = true;
        foreach ($rules as $rule) {
            $rule->setAttribute($attribute);

            if ($this->hasModifyValueRule && $rule instanceof ModifyValue) {
                $value = $rule->modifyValue($value);
                $isEmptyValue = false === $this->requiredRule->check($value);
            }

            // INFO при любом раскаде делаем проверку
            $check = $rule->check($value);
            $attribute->setRuleCheck($rule, $check);

            if ($isEmptyValue && $this->ruleIsOptional($attribute, $rule)) {
                continue;
            }

            if (!$check) {
                $isValid = false;
                $this->addError($attribute, $value, $rule);
                if ($rule->isImplicit()) {
                    break;
                }
            }
        } // endforeach

        // if ($key === 'config.RESOURCES_DIR') {
        //     VD::dd([
        //         '$isValid' => $isValid,
        //         '$isEmptyValue' => $isEmptyValue,
        //     ]);
        // }

        // TODO отсутствие правила required игнорирует false в других правилах (а точнее даже не делает проверку),
        // и вносит данные в getValidData(), даже если они плохие
        if ($isValid && $isEmptyValue && $attribute->hasFalseRuleCheck()) {
            $isValid = false;
        }

        // if ($key === 'config.RESOURCES_DIR') {
        //     VD::dd([
        //         '$isValid' => $isValid,
        //         '$isEmptyValue' => $isEmptyValue,
        //     ]);
        // }

        if ($isValid) {
            $this->setValidData($attribute, $value);
        } else {
            $this->setInvalidData($attribute, $value);
        }
    }

    /**
     * Check whether given $attribute is array attribute
     */
    protected function isArrayAttribute(Attribute $attribute): bool
    {
        return \strpos($attribute->getKey(), '*') !== false;
    }

    /**
     * Parse array attribute into it's child attributes
     * @return Attribute[]
     */
    protected function parseArrayAttribute(Attribute $attribute): array
    {
        $attributeKey = $attribute->getKey();
        $data         = Helper::arrayDot(
            $this->initializeAttributeOnData($attributeKey)
        );

        $pattern = \str_replace('\*', '([^\.]+)', \preg_quote($attributeKey));
        $data    = \array_merge($data, $this->extractValuesForWildcards(
            $data,
            $attributeKey
        ));

        $attributes = [];

        foreach ($data as $key => $value) {
            if ((bool) \preg_match('/^' . $pattern . '\z/', $key, $match)) {
                $attr = new Attribute($this, $key, null, $attribute->getRules());
                $attr->setPrimaryAttribute($attribute);
                $attr->setKeyIndexes(\array_slice($match, 1));
                $attributes[] = $attr;
            }
        }

        // set other attributes to each attributes
        foreach ($attributes as $i => $attr) {
            $otherAttributes = $attributes;
            unset($otherAttributes[$i]);
            $attr->setOtherAttributes($otherAttributes);
        }

        return $attributes;
    }

    /**
     * Get the explicit part of the attribute name.
     * Adapted from: https://github.com/illuminate/validation/blob/v5.3.23/Validator.php#L2817
     *
     * E.g. 'foo.bar.*.baz' -> 'foo.bar'
     *
     * Allows us to not spin through all of the flattened data for some operations.
     *
     * @return string|null null when root wildcard
     */
    protected function getLeadingExplicitAttributePath(string $attributeKey): ?string
    {
        return \rtrim(\explode('*', $attributeKey)[0], '.') ?: null;
    }

    /**
     * Extract data based on the given dot-notated path.
     * Adapted from: https://github.com/illuminate/validation/blob/v5.3.23/Validator.php#L2830
     *
     * Used to extract a sub-section of the data for faster iteration.
     * @return array
     */
    protected function extractDataFromPath(?string $attributeKey): array
    {
        $results = [];

        $value = Helper::arrayGet($this->inputs, $attributeKey, '__missing__');

        if ($value !== '__missing__') {
            Helper::arraySet($results, $attributeKey, $value);
        }

        return $results;
    }

    /**
     * Add error to the $this->errors
     * @param mixed $value
     */
    protected function addError(Attribute $attribute, $value, Rule $rule): void
    {
        $this->errors->add(
            $attribute->getKey(),
            $rule->getKey(), // rule name
            $this->resolveMessage($attribute, $value, $rule) // message
        );
    }

    /**
     * Check the rule is optional
     */
    protected function ruleIsOptional(Attribute $attribute, Rule $rule): bool
    {
        // Same and SameStrict rules should always be checked, even if value is empty
        $ignoreOption = \in_array(\get_class($rule), [
            \Rakit\Validation\Rules\Same::class,
            \Rakit\Validation\Rules\SameStrict::class,
            \Rakit\Validation\Rules\Different::class,
        ], \true);

        return false === $attribute->isRequired() &&
            false === $rule->isImplicit() &&
            false === $rule instanceof Required &&
            false === $ignoreOption;
    }

    /**
     * Resolve attribute name
     */
    protected function resolveAttributeName(Attribute $attribute): string
    {
        $key = $attribute->getKey();
        $primaryAttribute = $attribute->getPrimaryAttribute();
        if (isset($this->aliases[$key])) {
            return $this->aliases[$key];
        } elseif ($primaryAttribute and isset($this->aliases[$primaryAttribute->getKey()])) {
            return $this->aliases[$primaryAttribute->getKey()];
        } elseif ($this->validator->isUsingHumanizedKey()) {
            return $attribute->getHumanizedKey();
        } else {
            return $key;
        }
    }

    /**
     * Resolve message
     * @param mixed $value
     * @return mixed
     */
    protected function resolveMessage(Attribute $attribute, $value, Rule $rule)
    {
        $primaryAttribute = $attribute->getPrimaryAttribute();
        $params           = \array_merge($rule->getParameters(), $rule->getParametersTexts());
        $attributeKey     = $attribute->getKey();
        $ruleKey          = $rule->getKey();
        $alias            = $attribute->getAlias() ?: $this->resolveAttributeName($attribute);
        $message          = $rule->getMessage(); // default rule message
        $messageKeys      = [
            $attributeKey . $this->messageSeparator . $ruleKey,
            $attributeKey,
            $ruleKey
        ];

        if ($primaryAttribute) {
            // insert primaryAttribute keys
            // $messageKeys = [
            //     $attributeKey.$this->messageSeparator.$ruleKey,
            //     >> here [1] <<
            //     $attributeKey,
            //     >> and here [3] <<
            //     $ruleKey
            // ];
            $primaryAttributeKey = $primaryAttribute->getKey();
            \array_splice($messageKeys, 1, 0, $primaryAttributeKey . $this->messageSeparator . $ruleKey);
            \array_splice($messageKeys, 3, 0, $primaryAttributeKey);
        }

        foreach ($messageKeys as $key) {
            if (isset($this->messages[$key])) {
                $message = $this->messages[$key];
                break;
            }
        }

        // Replace message params
        $vars = \array_merge($params, [
            'attribute' => $alias,
            'value'     => $value,
        ]);

        foreach ($vars as $key => $value) {
            $value = $this->stringify($value);
            $message = \str_replace(':' . $key, $value, $message);
        }

        // Replace key indexes
        $keyIndexes = $attribute->getKeyIndexes();
        foreach ($keyIndexes as $pathIndex => $index) {
            $replacers = [
                "[{$pathIndex}]" => $index,
            ];

            if (\is_numeric($index)) {
                $replacers["{{$pathIndex}}"] = $index + 1;
            }

            $message = \str_replace(\array_keys($replacers), \array_values($replacers), $message);
        }

        return $message;
    }

    /**
     * Stringify $value
     * @param mixed $value
     */
    protected function stringify($value): string
    {
        $type = \gettype($value);
        if ($type === 'string' || \is_numeric($value)) {
            return (string)$value;
        } elseif ($type === 'array' || $type === 'object') {
            if (\PHP_VERSION_ID >= 80100 && $value instanceof \UnitEnum) {
                if ($value instanceof \BackedEnum) {
                    return $value->value;
                }
                return $value->name;
            }
            return (string)\json_encode($value);
        } else {
            return '';
        }
    }

    /**
     * Resolve $rulesInstruction
     * @param TypeVarRuleInstraction $rulesInstruction
     * @return Rule[]
     */
    protected function resolveRulesInstruction($rulesInstruction): array
    {
        if (\is_string($rulesInstruction)) {
            $rulesInstruction = \explode('|', $rulesInstruction);
            /** @var string[] $rulesInstruction */
        }

        $resolvedRulesInstruction = [];
        $hasBeforeValidateRule    = null;
        $hasModifyValueRule       = null;
        foreach ($rulesInstruction as $ruleInstruction) {
            if (empty($ruleInstruction)) {
                continue;
            }
            $params = [];

            if (\is_string($ruleInstruction)) {
                [$ruleName, $params] = $this->parseRuleInstruction($ruleInstruction);
                $rule = $this->validator->__invoke($ruleName, ...$params);
            } elseif ($ruleInstruction instanceof Rule) {
                $rule = $ruleInstruction;
            } elseif ($ruleInstruction instanceof Closure) {
                $rule = $this->validator->__invoke('callback', $ruleInstruction);
            } else {
                throw new \Exception(\sprintf(
                    'Rule must be a string, Closure or "%s" instance. %s given',
                    Rule::class,
                    \is_object($ruleInstruction) ? \get_class($ruleInstruction) : \gettype($ruleInstruction)
                ));
            }

            if ($hasBeforeValidateRule === null && $rule instanceof BeforeValidate) {
                $hasBeforeValidateRule = $this->hasBeforeValidateRule = true;
            }
            if ($hasModifyValueRule === null && $rule instanceof ModifyValue) {
                $hasModifyValueRule = $this->hasModifyValueRule = true;
            }

            $resolvedRulesInstruction[] = $rule;
        }

        return $resolvedRulesInstruction;
    }

    /**
     * Parse $ruleInstruction
     * @return array{0:string,1:string[]}
     */
    protected function parseRuleInstruction(string $ruleInstruction): array
    {
        $exp  = \explode(':', $ruleInstruction, 2);
        $name = $exp[0];
        if ($name !== 'regex') {
            $params = isset($exp[1]) ? \explode(',', $exp[1]) : [];
        } else {
            // TODO is maby null
            $params = [$exp[1]];
        }

        return [$name, $params];
    }

    /**
     * Given $inputs and resolve input attributes
     * @param mixed[] $inputs
     * @return mixed[]
     */
    protected function resolveInputAttributes(array $inputs): array
    {
        $resolvedInputs = [];
        foreach ($inputs as $key => $rules) {
            $exp = \explode(':', $key);

            if (\count($exp) > 1) {
                // set attribute alias
                $this->aliases[$exp[0]] = $exp[1];
            }

            $resolvedInputs[$exp[0]] = $rules;
        }

        return $resolvedInputs;
    }

    /**
     * Set valid data
     * @param mixed $value
     */
    protected function setValidData(Attribute $attribute, $value): void
    {
        $key = $attribute->getKey();
        if ($attribute->isArrayAttribute() || $attribute->isUsingDotNotation()) {
            Helper::arraySet($this->validData, $key, $value);
            Helper::arrayUnset($this->invalidData, $key);
        } else {
            $this->validData[$key] = $value;
        }
    }

    /**
     * Set invalid data
     * @param mixed $value
     */
    protected function setInvalidData(Attribute $attribute, $value): void
    {
        $key = $attribute->getKey();
        if ($attribute->isArrayAttribute() || $attribute->isUsingDotNotation()) {
            Helper::arraySet($this->invalidData, $key, $value);
            Helper::arrayUnset($this->validData, $key);
        } else {
            $this->invalidData[$key] = $value;
        }
    }
}
