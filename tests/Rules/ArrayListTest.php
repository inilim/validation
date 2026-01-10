<?php

use Rakit\Validation\Rules\ArrayList;
use Rakit\Validation\Tests\TestCase;

class ArrayListTest extends TestCase
{
    function test()
    {
        $rule = new ArrayList;
        $this->assertTrue($rule->check([]));
        $this->assertTrue($rule->check([\NAN, 'foo', 123]));
        $this->assertFalse($rule->check([1 => 'a', 0 => 'b']));
        $this->assertFalse($rule->check(['a' => 'b']));
        $this->assertFalse($rule->check([0 => 'a', 2 => 'b']));
        $this->assertFalse($rule->check([1 => 'a', 2 => 'b']));

        $x = ['key' => 2, \NAN];
        unset($x['key']);
        $this->assertTrue($rule->check($x));
    }
}
