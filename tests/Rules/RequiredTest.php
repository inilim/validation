<?php

namespace Rakit\Validation\Tests;

use Rakit\Validation\Rules\Required;

class RequiredTest extends \Rakit\Validation\Tests\TestCase
{
    protected function setUp(): void
    {
        $this->rule = new Required;
    }

    function testValids()
    {
        $this->assertTrue($this->rule->check('foo'));
        $this->assertTrue($this->rule->check([1]));
        $this->assertTrue($this->rule->check(1));
        $this->assertTrue($this->rule->check(-1));
        $this->assertTrue($this->rule->check(1.1));
        $this->assertTrue($this->rule->check(true));
        $this->assertTrue($this->rule->check('0'));
        $this->assertTrue($this->rule->check(0));
        $this->assertTrue($this->rule->check(0.0));
        $this->assertTrue($this->rule->check(new \stdClass));
    }

    function testInvalids()
    {
        $this->assertFalse($this->rule->check(null));
        $this->assertFalse($this->rule->check(''));
        $this->assertFalse($this->rule->check(' '));
        $this->assertFalse($this->rule->check([]));
    }
}
