<?php

use Rakit\Validation\Rules\StrLenBetween;
use Rakit\Validation\Tests\TestCase;

class StrLenBetweenTest extends TestCase
{
    protected function setUp(): void
    {
        $this->rule = new StrLenBetween();
    }

    public function testValidStringLengthBetween()
    {
        // Test string with length between min and max
        $this->assertTrue($this->rule->fillParameters(['min' => 2, 'max' => 5])->check('abc'));
        $this->assertTrue($this->rule->fillParameters(['min' => 1, 'max' => 5])->check('abc'));
        $this->assertTrue($this->rule->fillParameters(['min' => 3, 'max' => 5])->check('abc'));
        $this->assertTrue($this->rule->fillParameters(['min' => 3, 'max' => 3])->check('abc'));
    }

    public function testInvalidStringLengthBelowMin()
    {
        // Test string with length below min
        $this->assertFalse($this->rule->fillParameters(['min' => 5, 'max' => 10])->check('abc'));
        $this->assertFalse($this->rule->fillParameters(['min' => 2, 'max' => 5])->check('a'));
    }

    public function testInvalidStringLengthAboveMax()
    {
        // Test string with length above max
        $this->assertFalse($this->rule->fillParameters(['min' => 1, 'max' => 3])->check('abcde'));
        $this->assertFalse($this->rule->fillParameters(['min' => 2, 'max' => 4])->check('abcdef'));
    }

    public function testInvalidNonStringValue()
    {
        // Test non-string values
        $this->assertFalse($this->rule->fillParameters(['min' => 1, 'max' => 5])->check(['not', 'a', 'string']));
        $this->assertFalse($this->rule->fillParameters(['min' => 1, 'max' => 5])->check(123));
        $this->assertFalse($this->rule->fillParameters(['min' => 1, 'max' => 5])->check(null));
        $this->assertFalse($this->rule->fillParameters(['min' => 1, 'max' => 5])->check(new stdClass()));
    }

    public function testEmptyString()
    {
        // Test empty string
        $this->assertFalse($this->rule->fillParameters(['min' => 1, 'max' => 5])->check(''));
        $this->assertTrue($this->rule->fillParameters(['min' => 0, 'max' => 5])->check(''));
    }

    public function testStringWithSpaces()
    {
        // Test string with spaces
        $this->assertTrue($this->rule->fillParameters(['min' => 3, 'max' => 10])->check('a b'));
        $this->assertTrue($this->rule->fillParameters(['min' => 5, 'max' => 10])->check('a b c'));
        $this->assertFalse($this->rule->fillParameters(['min' => 10, 'max' => 15])->check('a b'));
    }

    public function testStringWithSpecialCharacters()
    {
        // Test string with special characters
        $this->assertTrue($this->rule->fillParameters(['min' => 3, 'max' => 10])->check('a@b'));
        $this->assertTrue($this->rule->fillParameters(['min' => 5, 'max' => 10])->check('a@b#c'));
        $this->assertFalse($this->rule->fillParameters(['min' => 10, 'max' => 15])->check('a@b'));
    }

    public function testStringWithUnicodeCharacters()
    {
        // Test string with unicode characters
        $this->assertTrue($this->rule->fillParameters(['min' => 2, 'max' => 5])->check('абв'));
        $this->assertTrue($this->rule->fillParameters(['min' => 3, 'max' => 3])->check('абв'));
        $this->assertFalse($this->rule->fillParameters(['min' => 5, 'max' => 10])->check('абв'));
    }

    public function testInvalidParameters()
    {
        // Test invalid parameters
        $this->expectException(\InvalidArgumentException::class);
        $this->rule->fillParameters(['min' => 'not numeric', 'max' => 5])->check('abc');
    }

    public function testInvalidMaxParameter()
    {
        // Test invalid max parameter
        $this->expectException(\InvalidArgumentException::class);
        $this->rule->fillParameters(['min' => 1, 'max' => 'not numeric'])->check('abc');
    }
}
