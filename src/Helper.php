<?php

namespace Rakit\Validation;

class Helper
{
    /**
     * Check if an item or items exist in an array using "dot" notation.
     * Adapted from: https://github.com/illuminate/support/blob/v5.3.23/Arr.php#L81
     *
     * @param  array $array
     * @param  string $key
     * @return bool
     */
    static function arrayHas(array $array, string $key): bool
    {
        if (\array_key_exists($key, $array)) {
            return true;
        }

        foreach (\explode('.', $key) as $segment) {
            if (\is_array($array) && \array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Get an item from an array using "dot" notation.
     * Adapted from: https://github.com/illuminate/support/blob/v5.3.23/Arr.php#L246
     *
     * @param  array       $array
     * @param  string|null $key
     * @param  mixed       $default
     * @return mixed
     */
    static function arrayGet(array $array, $key, $default = null)
    {
        if ($key === null) {
            return $array;
        }

        if (\array_key_exists($key, $array)) {
            return $array[$key];
        }

        foreach (\explode('.', $key) as $segment) {
            if (\is_array($array) && \array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }

        return $array;
    }

    /**
     * Convert a flatten "dot" notation array into an expanded array.
     * @param  iterable  $array
     */
    static function arrayUndot($array): array
    {
        $results = [];
        foreach ($array as $key => $value) {
            self::arraySet($results, $key, $value);
        }

        return $results;
    }

    /**
     * Flatten a multi-dimensional associative array with dots.
     * Adapted from: https://github.com/illuminate/support/blob/v5.3.23/Arr.php#L81
     *
     * @param  array  $array
     * @param  string $prepend
     * @return array
     */
    static function arrayDot(array $array, string $prepend = ''): array
    {
        $results = [];

        $flatten = static function ($data, $prefix) use (&$results, &$flatten): void {
            foreach ($data as $key => $value) {
                $newKey = $prefix . $key;

                if (\is_array($value) && ! empty($value)) {
                    $flatten($value, $newKey . '.');
                } else {
                    $results[$newKey] = $value;
                }
            }
        };

        $flatten($array, $prepend);

        return $results;
    }

    /**
     * Set an item on an array or object using dot notation.
     * Adapted from: https://github.com/illuminate/support/blob/v5.3.23/helpers.php#L437
     *
     * @param mixed             $target
     * @param string|array|null $key
     * @param mixed             $value
     * @param bool              $overwrite
     * @return mixed
     */
    static function arraySet(&$target, $key, $value, $overwrite = true): array
    {
        if ($key === null) {
            if ($overwrite) {
                return $target = \array_merge($target, $value);
            }
            return $target = \array_merge($value, $target);
        }

        $segments = \is_array($key) ? $key : \explode('.', $key);

        if (($segment = \array_shift($segments)) === '*') {
            if (! \is_array($target)) {
                $target = [];
            }

            if ($segments) {
                foreach ($target as &$inner) {
                    static::arraySet($inner, $segments, $value, $overwrite);
                }
            } elseif ($overwrite) {
                foreach ($target as &$inner) {
                    $inner = $value;
                }
            }
        } elseif (\is_array($target)) {
            if ($segments) {
                if (! \array_key_exists($segment, $target)) {
                    $target[$segment] = [];
                }

                static::arraySet($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite || ! \array_key_exists($segment, $target)) {
                $target[$segment] = $value;
            }
        } else {
            $target = [];

            if ($segments) {
                static::arraySet($target[$segment], $segments, $value, $overwrite);
            } elseif ($overwrite) {
                $target[$segment] = $value;
            }
        }

        return $target;
    }

    /**
     * Unset an item on an array or object using dot notation.
     *
     * @param  mixed        $target
     * @param  string|array $key
     * @return mixed
     */
    static function arrayUnset(&$target, $key)
    {
        if (!\is_array($target)) {
            return $target;
        }

        $segments = \is_array($key) ? $key : \explode('.', $key);
        $segment = \array_shift($segments);

        if ($segment == '*') {
            $target = [];
        } elseif ($segments) {
            if (\array_key_exists($segment, $target)) {
                static::arrayUnset($target[$segment], $segments);
            }
        } elseif (\array_key_exists($segment, $target)) {
            unset($target[$segment]);
        }

        return $target;
    }

    /**
     * Get snake_case format from given string
     *
     * @param  string $value
     * @param  string $delimiter
     * @return string
     */
    static function snakeCase(string $value, string $delimiter = '_'): string
    {
        if (!\ctype_lower($value)) {
            $value = \preg_replace('/\s+/u', '', \ucwords($value));
            $value = \strtolower(\preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value));
        }

        return $value;
    }

    /**
     * Join string[] to string with given $separator and $lastSeparator.
     *
     * @param  array        $pieces
     * @param  string       $separator
     * @param  string|null  $lastSeparator
     * @return string
     */
    static function join(array $pieces, string $separator, ?string $lastSeparator = null): string
    {
        if ($lastSeparator === null) {
            $lastSeparator = $separator;
        }

        $last = \array_pop($pieces);

        switch (\count($pieces)) {
            case 0:
                return $last ?: '';
            case 1:
                return $pieces[0] . $lastSeparator . $last;
            default:
                return \implode($separator, $pieces) . $lastSeparator . $last;
        }
    }

    /**
     * Wrap string[] by given $prefix and $suffix
     *
     * @param  array        $strings
     * @param  string       $prefix
     * @param  string|null  $suffix
     * @return array
     */
    static function wraps(array $strings, string $prefix, ?string $suffix = null): array
    {
        if ($suffix === null) {
            $suffix = $prefix;
        }

        return \array_map(static function ($str) use ($prefix, $suffix) {
            return $prefix . $str . $suffix;
        }, $strings);
    }
}
