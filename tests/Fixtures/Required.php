<?php

namespace Rakit\Validation\Tests;

use Rakit\Validation\Rule;

class Required extends Rule
{
    function check($value): bool
    {
        return \true;
    }
}
