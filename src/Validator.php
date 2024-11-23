<?php

namespace Rakit\Validation;

use Rakit\Validation\Rule;

class Validator
{
    use Traits\TranslationsTrait, Traits\MessagesTrait;

    /** @var array */
    protected $translations = [];

    /** @var array<string,Rule|callable():Rule|class-string<Rule>> */
    protected $validators = [];

    /** @var bool */
    protected $allowRuleOverride = false;

    /** @var bool */
    protected $useHumanizedKeys = true;

    /**
     * Constructor
     *
     * @param array $messages
     * @return void
     */
    public function __construct(array $messages = [])
    {
        $this->messages = $messages;
        // $this->registerBaseValidators();
    }

    /**
     * Register or override existing validator
     *
     * @param mixed $key
     * @param Rule|callable():Rule|class-string<Rule> $rule
     * @return void
     */
    public function setValidator(string $key, $rule)
    {
        $this->validators[$key] = $rule;
    }

    /**
     * Get validator object from given $key
     *
     * @return Rule|null
     */
    public function getValidator(string $key)
    {
        $rule = $this->validators[$key] ?? $this->getBaseValidator($key);

        if ($rule === null) return null;

        if (\is_object($rule)) {
            if ($rule instanceof Rule) {
                $rule->setKey($key);
                return $rule;
            }

            if ($rule instanceof \Closure) {
                $rule = $rule->__invoke();
            }

            if ($rule instanceof Rule) {
                $rule->setKey($key);
                return $this->validators[$key] = $rule;
            }

            throw new \InvalidArgumentException(\sprintf(
                'object "%s" not rule',
                $key
            ));
        }

        if (\is_string($rule)) {
            if (!\class_exists($rule, true)) {
                throw new RuleNotFoundException(\sprintf(
                    'class rule "%s" not found',
                    $key
                ));
            }

            $rule = new $rule;

            if ($rule instanceof Rule) {
                $rule->setKey($key);
                return $this->validators[$key] = $rule;
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
                return $this->validators[$key] = $rule;
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
     * Given $ruleName and $rule to add new validator
     *
     * @param string $ruleName
     * @param Rule|callable():Rule|class-string<Rule> $rule recommend class-string or callable
     * @return void
     */
    public function addValidator(string $ruleName, $rule)
    {
        // if (!$this->allowRuleOverride && \array_key_exists($ruleName, $this->validators)) {
        if (!$this->allowRuleOverride && $this->getBaseValidator($ruleName) !== null) {
            throw new RuleQuashException(
                "You cannot override a built in rule. You have to rename your rule"
            );
        }

        $this->setValidator($ruleName, $rule);
    }

    /**
     * Validate $inputs
     *
     * @param array $inputs
     * @param array $rules
     * @param array $messages
     * @return Validation
     */
    public function validate(array $inputs, array $rules, array $messages = []): Validation
    {
        return $this->make($inputs, $rules, $messages)->validate();
    }

    /**
     * Given $inputs, $rules and $messages to make the Validation class instance
     *
     * @param mixed[] $inputs
     * @param array $rules
     * @param array $messages
     * @return Validation
     */
    public function make(array $inputs, array $rules, array $messages = []): Validation
    {
        $validation = new Validation(
            $this,
            $inputs,
            $rules,
            \array_merge($this->messages, $messages)
        );
        $validation->setTranslations($this->getTranslations());

        return $validation;
    }

    /**
     * Magic invoke method to make Rule instance
     *
     * @param string $rule
     * @return Rule
     * @throws RuleNotFoundException
     */
    public function __invoke(string $rule): Rule
    {
        $args      = \func_get_args();
        $rule      = \array_shift($args);
        $params    = $args;
        $validator = $this->getValidator($rule);
        if (!$validator) {
            throw new RuleNotFoundException('Validator ' . $rule . ' is not registered', 1);
        }

        $clonedValidator = clone $validator;
        $clonedValidator->fillParameters($params);

        return $clonedValidator;
    }

    /**
     * @return null|class-string<Rule>
     */
    protected function getBaseValidator(string $key)
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
            case 'integer':
                return Rules\Integer::class;
            case 'boolean':
                return Rules\Boolean::class;
            case 'array':
                return Rules\TypeArray::class;
            case 'same':
                return Rules\Same::class;
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
                return Rules\Defaults::class;
            case 'default':
                return Rules\Defaults::class; // alias of defaults

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
            case 'uploaded_file':
                return Rules\UploadedFile::class;
            case 'mimes':
                return Rules\Mimes::class;
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

    /**
     * Set rule can allow to be overrided
     *
     * @param boolean $status
     * @return void
     */
    public function allowRuleOverride(bool $status = false)
    {
        $this->allowRuleOverride = $status;
    }

    /**
     * Set this can use humanize keys
     *
     * @param boolean $useHumanizedKeys
     * @return void
     */
    public function setUseHumanizedKeys(bool $useHumanizedKeys = true)
    {
        $this->useHumanizedKeys = $useHumanizedKeys;
    }

    /**
     * Get $this->useHumanizedKeys value
     *
     * @return void
     */
    public function isUsingHumanizedKey(): bool
    {
        return $this->useHumanizedKeys;
    }
}
