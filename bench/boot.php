<?php

require_once __DIR__ . '/../vendor/autoload.php';

function unixMs(): int
{
    $t = \microtime(false);
    return \intval(\substr($t, 11) . \substr($t, 2, 3));
}

/**
 * @author inilim
 * 
 * @template R of mixed
 * @template Time of int
 * @template Memory of int
 * 
 * @param callable():R $callable
 * @return array{result:R,time:Time,memory:Memory}
 */
function timedMsCall(callable $callable): array
{
    $m = \memory_get_usage(true);
    $ms = \unixMs();
    $result = $callable();
    $ms = \unixMs() - $ms;
    $m = \memory_get_usage(true) - $m;

    return [
        'result' => $result,
        'time'   => $ms,
        'memory' => $m,
    ];
}
