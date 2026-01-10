<?php

namespace Rakit\Validation;

use Inilim\Tool\VD;
use Rakit\Validation\Rule;
use Rakit\Validation\Validation;

/**
 * @psalm-type TypeVarRuleInstraction = string|array<string|\Closure|Rule>
 * @psalm-type TypeVarRulesInstraction = array<string,TypeVarRuleInstraction>
 */
final class Validator
{
    use Traits\TranslationsTrait, Traits\MessagesTrait;

    /** @var array */
    // protected array $translations = [];

    /** @var array<string,Rule|callable():Rule|class-string<Rule>> */
    protected array $rules = [];

    protected bool $allowRuleOverride = false;
    protected bool $useHumanizedKeys = true;

    /**
     * Constructor
     * @param array $messages
     */
    function __construct(array $messages = [])
    {
        $this->messages = $messages;
    }

    /**
     * Get validator object from given $key
     */
    function getRule(string $key): ?Rule
    {
        $rule = $this->rules[$key] ?? $this->getBaseRule($key);

        if ($rule === null) return null;

        $typeRule = \gettype($rule);

        if ($typeRule === 'object') {
            /** @var object $rule */
            if ($rule instanceof Rule) {
                $rule->setKey($key);
                return $rule;
            }

            if ($rule instanceof \Closure) {
                $rule = $rule->__invoke();
            }

            if ($rule instanceof Rule) {
                $rule->setKey($key);
                return $this->rules[$key] = $rule;
            }

            throw new \InvalidArgumentException(\sprintf(
                'object "%s" not rule',
                $key
            ));
        }

        if ($typeRule === 'string') {
            /** @var string $rule */
            if (!\class_exists($rule, true)) {
                throw new RuleNotFoundException(\sprintf(
                    'class rule "%s" not found',
                    $key
                ));
            }

            $rule = new $rule;

            if ($rule instanceof Rule) {
                $rule->setKey($key);
                return $this->rules[$key] = $rule;
            }

            throw new \InvalidArgumentException(\sprintf(
                'class "%s" not rule',
                $key
            ));
        }

        if (\is_callable($rule)) {
            $rule = \call_user_func($rule);

            if ($rule instanceof Rule) {
                $rule->setKey($key);
                return $this->rules[$key] = $rule;
            }

            throw new \InvalidArgumentException(\sprintf(
                'class "%s" not rule',
                $key
            ));
        }

        throw new \InvalidArgumentException(\sprintf(
            'added incorect rule by "%s"',
            $key
        ));
    }

    /**
     * Given $name and $rule to add new validator
     *
     * @param Rule|class-string<Rule>|callable():Rule $rule recommend class-string or callable
     */
    function addRule(string $name, $rule): self
    {
        if (!$this->allowRuleOverride && $this->getBaseRule($name) !== null) {
            throw new RuleQuashException(
                "You cannot override a built in rule. You have to rename your rule"
            );
        }

        $this->rules[$name] = $rule;

        return $this;
    }

    /**
     * Validate $inputs
     * @param array $inputs
     * @param TypeVarRulesInstraction $rulesInstruction
     * @param array $messages
     */
    function validate(array $inputs, array $rulesInstruction, array $messages = []): Validation
    {
        return $this->make($inputs, $rulesInstruction, $messages)->validate();
    }

    /**
     * Given $inputs, $rulesInstruction and $messages to make the Validation class instance
     * @param mixed[] $inputs
     * @param TypeVarRulesInstraction $rulesInstruction
     * @param array $messages
     */
    function make(array $inputs, array $rulesInstruction, array $messages = []): Validation
    {
        $validation = new Validation(
            $this,
            $inputs,
            $rulesInstruction,
            \array_merge($this->messages, $messages)
        );
        $validation->setTranslations($this->getTranslations());

        return $validation;
    }

    /**
     * Magic invoke method to make Rule instance
     * @throws RuleNotFoundException
     */
    function __invoke(): Rule
    {
        $args     = \func_get_args();
        /** @var array<string|\Closure> $args */
        $ruleName = \array_shift($args);
        $params   = $args;
        $rule     = $this->getRule($ruleName);
        if (!$rule) {
            throw new RuleNotFoundException(\sprintf(
                'Validator "%s" is not registered',
                $ruleName
            ), 1);
        }

        $clonedRule = clone $rule;
        $clonedRule->fillParameters($params);

        return $clonedRule;
    }

    /**
     * Set rule can allow to be overrided
     */
    function allowRuleOverride(bool $status = false): void
    {
        $this->allowRuleOverride = $status;
    }

    /**
     * Set this can use humanize keys
     */
    function setUseHumanizedKeys(bool $useHumanizedKeys = true): void
    {
        $this->useHumanizedKeys = $useHumanizedKeys;
    }

    /**
     * Get $this->useHumanizedKeys value
     */
    function isUsingHumanizedKey(): bool
    {
        return $this->useHumanizedKeys;
    }

    /**
     * @return null|class-string<Rule>
     */
    protected function getBaseRule(string $key): ?string
    {
        // if someone thinks that it is better to make an array here, then this is not the case
        switch ($key) {
            case 'required':
                return Rules\Required::class;
            case 'nullable':
                return Rules\Nullable::class;
            case 'required_if':
                return Rules\RequiredIf::class;
            case 'email':
                return Rules\Email::class;
            case 'numeric':
                return Rules\Numeric::class;
            case 'in':
                return Rules\In::class;
            case 'not_in':
                return Rules\NotIn::class;
            case 'min':
                return Rules\Min::class;
            case 'max':
                return Rules\Max::class;
            case 'between':
                return Rules\Between::class;
            case 'url':
                return Rules\Url::class;
            case 'int':
            case 'integer':
                return Rules\Integer::class;
            case 'bool':
            case 'boolean':
                return Rules\CastableToBool::class;
            case 'scalar':
                return Rules\Scalar::class;

                // strict
            case 'bool_strict':
            case 'boolean_strict':
                return Rules\BoolStrict::class;
            case 'float_strict':
            case 'double_strict':
                return Rules\FloatStrict::class;
            case 'int_strict':
            case 'integer_strict':
                return Rules\IntStrict::class;
            case 'str_strict':
            case 'string_strict':
                return Rules\StrStrict::class;

                // array
            case 'array':
                return Rules\TypeArray::class;
            case 'array_list':
                return Rules\ArrayList::class;
            case 'array_keys_only_str':
            case 'array_keys_only_string':
                return Rules\TypeArrayKeysOnlyString::class;
            case 'array_keys_only_int':
            case 'array_keys_only_integer':
                return Rules\TypeArrayKeysOnlyInt::class;
            case 'array_count_min':
                return Rules\ArrayCountMin::class;
            case 'array_count_max':
                return Rules\ArrayCountMax::class;
            case 'array_count_between':
                return Rules\ArrayCountBetween::class;

                // string
            case 'string_length_between':
            case 'string_len_between':
            case 'str_length_between':
            case 'str_len_between':
                return Rules\StrLenBetween::class;

            case 'same':
                return Rules\Same::class;
            case 'same_strict':
                return Rules\SameStrict::class;
            case 'regex':
                return Rules\Regex::class;
            case 'date':
                return Rules\Date::class;
            case 'accepted':
                return Rules\Accepted::class;
            case 'before':
                return Rules\Before::class;
            case 'after':
                return Rules\After::class;
            case 'lowercase':
                return Rules\Lowercase::class;
            case 'uppercase':
                return Rules\Uppercase::class;
            case 'json':
                return Rules\Json::class;
            case 'digits':
                return Rules\Digits::class;
            case 'digits_between':
                return Rules\DigitsBetween::class;
            case 'defaults':
            case 'default':
                return Rules\Defaults::class;

            case 'isbn':
                return Rules\Isbn::class;
            case 'ip':
                return Rules\Ip::class;
            case 'ipv4':
                return Rules\Ipv4::class;
            case 'ipv6':
                return Rules\Ipv6::class;
            case 'extension':
                return Rules\Extension::class;

            case 'present':
                return Rules\Present::class;
            case 'different':
                return Rules\Different::class;

                // removed
            case 'uploaded_file':
                throw new RuleNotFoundException('Rule "uploaded_file" removed');
                // removed
            case 'mimes':
                throw new RuleNotFoundException('Rule "mimes" removed');

            case 'callback':
                return Rules\Callback::class;

            case 'required_unless':
                return Rules\RequiredUnless::class;
            case 'required_with':
                return Rules\RequiredWith::class;
            case 'required_without':
                return Rules\RequiredWithout::class;
            case 'required_with_all':
                return Rules\RequiredWithAll::class;
            case 'required_without_all':
                return Rules\RequiredWithoutAll::class;

            case 'alpha':
                return Rules\Alpha::class;
            case 'alpha_num':
                return Rules\AlphaNum::class;
            case 'alpha_dash':
                return Rules\AlphaDash::class;
            case 'alpha_spaces':
                return Rules\AlphaSpaces::class;

            default:
                return null;
        }
    }
}
