<?php

namespace Rakit\Validation\Rules;

use Rakit\Validation\Rule;

/**
 * Validates whether the input is a valid ISBN (International Standard Book Number) or not.
 *
 * @author Henrique Moody <henriquemoody@gmail.com>
 * @author Moritz Fromm <moritzgitfromm@gmail.com>
 */
class Isbn extends Rule
{
    protected string $message = "The :attribute is not valid ISBN";

    /**
     * @see https://howtodoinjava.com/regex/java-regex-validate-international-standard-book-number-isbns
     */
    protected const PIECES = [
        '^(?:ISBN(?:-1[03])?:? )?(?=[0-9X]{10}$|(?=(?:[0-9]+[- ]){3})',
        '[- 0-9X]{13}$|97[89][0-9]{10}$|(?=(?:[0-9]+[- ]){4})[- 0-9]{17}$)',
        '(?:97[89][- ]?)?[0-9]{1,5}[- ]?[0-9]+[- ]?[0-9]+[- ]?[0-9X]$',
    ];

    /**
     * @param mixed $value
     */
    function check($value): bool
    {
        if (!\is_scalar($value)) {
            return \false;
        }

        return \preg_match(\sprintf('/%s/', \implode(self::PIECES)), (string) $value) > 0;
    }
}
