<?php

use Rakit\Validation\Rules\ArrayCountMin;
use Rakit\Validation\Tests\TestCase;

class ArrayCountMinTest extends TestCase
{
    protected function setUp(): void
    {
        $this->rule = new ArrayCountMin();
    }

    public function testValidArrayCountMin()
    {
        // Test array with count greater than or equal to min
        $this->assertTrue($this->rule->fillParameters(['min' => 2])->check([1, 2, 3]));
        $this->assertTrue($this->rule->fillParameters(['min' => 3])->check([1, 2, 3]));
        $this->assertTrue($this->rule->fillParameters(['min' => 0])->check([]));
        $this->assertTrue($this->rule->fillParameters(['min' => 0])->check([1, 2, 3]));
    }

    public function testInvalidArrayCountBelowMin()
    {
        // Test array with count below min
        $this->assertFalse($this->rule->fillParameters(['min' => 5])->check([1, 2, 3]));
        $this->assertFalse($this->rule->fillParameters(['min' => 3])->check([1, 2]));
        $this->assertFalse($this->rule->fillParameters(['min' => 1])->check([]));
    }

    public function testInvalidNonArrayValue()
    {
        // Test non-array values
        $this->assertFalse($this->rule->fillParameters(['min' => 1])->check('not an array'));
        $this->assertFalse($this->rule->fillParameters(['min' => 1])->check(123));
        $this->assertFalse($this->rule->fillParameters(['min' => 1])->check(null));
        $this->assertFalse($this->rule->fillParameters(['min' => 1])->check(new stdClass()));
    }

    public function testEmptyArray()
    {
        // Test empty array with various min values
        $this->assertTrue($this->rule->fillParameters(['min' => 0])->check([]));
        $this->assertFalse($this->rule->fillParameters(['min' => 1])->check([]));
    }

    public function testInvalidParameters()
    {
        // Test invalid parameters
        $this->expectException(InvalidArgumentException::class);
        $this->rule->fillParameters(['min' => 'not numeric'])->check([1, 2, 3]);
    }
}