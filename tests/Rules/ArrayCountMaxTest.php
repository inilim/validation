<?php

use Rakit\Validation\Rules\ArrayCountMax;
use Rakit\Validation\Tests\TestCase;

class ArrayCountMaxTest extends TestCase
{
    protected function setUp(): void
    {
        $this->rule = new ArrayCountMax();
    }

    public function testValidArrayCountMax()
    {
        // Test array with count less than or equal to max
        $this->assertTrue($this->rule->fillParameters(['max' => 5])->check([1, 2, 3]));
        $this->assertTrue($this->rule->fillParameters(['max' => 3])->check([1, 2, 3]));
        $this->assertTrue($this->rule->fillParameters(['max' => 0])->check([]));
    }

    public function testInvalidArrayCountAboveMax()
    {
        // Test array with count above max
        $this->assertFalse($this->rule->fillParameters(['max' => 3])->check([1, 2, 3, 4, 5]));
        $this->assertFalse($this->rule->fillParameters(['max' => 1])->check([1, 2]));
        $this->assertFalse($this->rule->fillParameters(['max' => 0])->check([1]));
    }

    public function testInvalidNonArrayValue()
    {
        // Test non-array values
        $this->assertFalse($this->rule->fillParameters(['max' => 5])->check('not an array'));
        $this->assertFalse($this->rule->fillParameters(['max' => 5])->check(123));
        $this->assertFalse($this->rule->fillParameters(['max' => 5])->check(null));
        $this->assertFalse($this->rule->fillParameters(['max' => 5])->check(new stdClass()));
    }

    public function testEmptyArray()
    {
        // Test empty array with various max values
        $this->assertTrue($this->rule->fillParameters(['max' => 5])->check([]));
        $this->assertTrue($this->rule->fillParameters(['max' => 0])->check([]));
    }

    public function testInvalidParameters()
    {
        // Test invalid parameters
        $this->expectException(\InvalidArgumentException::class);
        $this->rule->fillParameters(['max' => 'not numeric'])->check([1, 2, 3]);
    }
}
