<?php

use Rakit\Validation\Rules\ArrayCountBetween;
use Rakit\Validation\Tests\TestCase;

class ArrayCountBetweenTest extends TestCase
{
    protected function setUp(): void
    {
        $this->rule = new ArrayCountBetween();
    }

    public function testValidArrayCountBetween()
    {
        // Test array with count between min and max
        $this->assertTrue($this->rule->fillParameters(['min' => 2, 'max' => 5])->check([1, 2, 3]));
        $this->assertTrue($this->rule->fillParameters(['min' => 1, 'max' => 5])->check([1, 2, 3]));
        $this->assertTrue($this->rule->fillParameters(['min' => 3, 'max' => 5])->check([1, 2, 3]));
        $this->assertTrue($this->rule->fillParameters(['min' => 3, 'max' => 3])->check([1, 2, 3]));
    }

    public function testInvalidArrayCountBelowMin()
    {
        // Test array with count below min
        $this->assertFalse($this->rule->fillParameters(['min' => 5, 'max' => 10])->check([1, 2, 3]));
        $this->assertFalse($this->rule->fillParameters(['min' => 2, 'max' => 5])->check([1]));
    }

    public function testInvalidArrayCountAboveMax()
    {
        // Test array with count above max
        $this->assertFalse($this->rule->fillParameters(['min' => 1, 'max' => 3])->check([1, 2, 3, 4, 5]));
        $this->assertFalse($this->rule->fillParameters(['min' => 2, 'max' => 4])->check([1, 2, 3, 4, 5, 6]));
    }

    public function testInvalidNonArrayValue()
    {
        // Test non-array values
        $this->assertFalse($this->rule->fillParameters(['min' => 1, 'max' => 5])->check('not an array'));
        $this->assertFalse($this->rule->fillParameters(['min' => 1, 'max' => 5])->check(123));
        $this->assertFalse($this->rule->fillParameters(['min' => 1, 'max' => 5])->check(null));
        $this->assertFalse($this->rule->fillParameters(['min' => 1, 'max' => 5])->check(new stdClass()));
    }

    public function testEmptyArray()
    {
        // Test empty array
        $this->assertFalse($this->rule->fillParameters(['min' => 1, 'max' => 5])->check([]));
        $this->assertTrue($this->rule->fillParameters(['min' => 0, 'max' => 5])->check([]));
    }

    public function testInvalidParameters()
    {
        // Test invalid parameters
        $this->expectException(\InvalidArgumentException::class);
        $this->rule->fillParameters(['min' => 'not numeric', 'max' => 5])->check([1, 2, 3]);
    }

    public function testInvalidMaxParameter()
    {
        // Test invalid max parameter
        $this->expectException(\InvalidArgumentException::class);
        $this->rule->fillParameters(['min' => 1, 'max' => 'not numeric'])->check([1, 2, 3]);
    }
}
