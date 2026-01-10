<?php

namespace Rakit\Validation\Rules\Traits;

use Rakit\Validation\Rule;

/**
 * @psalm-require-extends Rule
 * @phpstan-require-extends Rule
 */
trait SizeTrait
{
    /**
     * Get size (int) value from given $value
     * @param int|string $value
     * @return float|false
     */
    protected function getValueSize($value)
    {
        /** @var Rule $this */

        $attribute = $this->getAttribute();

        if (
            $attribute
            && ($attribute->hasRule('numeric') || $attribute->hasRule('integer'))
            && \is_numeric($value)
        ) {
            $value = (float) $value;
        }

        $typeValue = \gettype($value);

        if ($typeValue === 'integer' || $typeValue === 'double') {
            /** @var int|float $value */
            return (float) $value;
        } elseif ($typeValue === 'string') {
            /** @var string $value */
            return (float) \mb_strlen($value, 'UTF-8');
        } elseif ($typeValue === 'array') {
            /** @var array $value */
            return (float) \count($value);
        } else {
            return \false;
        }
    }

    /**
     * Given $size and get the bytes
     * @param string|int $size
     * @throws \InvalidArgumentException
     */
    protected function getBytesSize($size): float
    {
        if (\is_numeric($size)) {
            return (float) $size;
        }

        if (!\is_string($size)) {
            throw new \InvalidArgumentException("Size must be string or numeric Bytes", 1);
        }

        if (!\preg_match("/^(?<number>((\d+)?\.)?\d+)(?<format>(B|K|M|G|T|P)B?)?$/i", $size, $match)) {
            throw new \InvalidArgumentException("Size is not valid format", 1);
        }

        $number = (float) $match['number'];
        $format = isset($match['format']) ? $match['format'] : '';

        switch (\strtoupper($format)) {
            case "KB":
            case "K":
                return $number * 1024;

            case "MB":
            case "M":
                return $number * \pow(1024, 2);

            case "GB":
            case "G":
                return $number * \pow(1024, 3);

            case "TB":
            case "T":
                return $number * \pow(1024, 4);

            case "PB":
            case "P":
                return $number * \pow(1024, 5);

            default:
                return $number;
        }
    }
}
