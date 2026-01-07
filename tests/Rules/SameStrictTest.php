<?php

use Rakit\Validation\Rules\SameStrict;
use Rakit\Validation\Tests\TestCase;

class SameStrictTest extends TestCase
{
    protected function setUp(): void
    {
        $this->rule = new SameStrict();
    }

    public function testValidSameStrictValues()
    {
        // Test same strict values (same type and value)
        $validator = new Rakit\Validation\Validator();
        $validation = $validator->validate([
            'password' => 'secret',
            'password_confirmation' => 'secret'
        ], [
            'password_confirmation' => 'same_strict:password'
        ]);

        $this->assertTrue($validation->passes());
    }

    public function testInvalidSameStrictDifferentValues()
    {
        // Test different values
        $validator = new Rakit\Validation\Validator();
        $validation = $validator->validate([
            'password' => 'secret',
            'password_confirmation' => 'different'
        ], [
            'password_confirmation' => 'same_strict:password'
        ]);

        $this->assertTrue($validation->fails());
    }

    public function testInvalidSameStrictDifferentTypes()
    {
        // Test same values but different types (strict comparison should fail)
        $validator = new Rakit\Validation\Validator();
        $validation = $validator->validate([
            'number' => 123,
            'number_confirmation' => '123' // string vs integer
        ], [
            'number_confirmation' => 'same_strict:number'
        ]);

        $this->assertTrue($validation->fails());
    }

    public function testValidSameStrictSameTypeAndValue()
    {
        // Test same type and value
        $validator = new Rakit\Validation\Validator();
        $validation = $validator->validate([
            'number' => 123,
            'number_confirmation' => 123 // same type and value
        ], [
            'number_confirmation' => 'same_strict:number'
        ]);

        $this->assertTrue($validation->passes());
    }

    public function testValidSameStrictSameTypeAndValueString()
    {
        // Test same type and value with strings
        $validator = new Rakit\Validation\Validator();
        $validation = $validator->validate([
            'text1' => 'hello',
            'text2' => 'hello'
        ], [
            'text2' => 'same_strict:text1'
        ]);

        $this->assertTrue($validation->passes());
    }

    public function testValidSameStrictSameTypeAndValueBoolean()
    {
        // Test same type and value with booleans
        $validator = new Rakit\Validation\Validator();
        $validation = $validator->validate([
            'flag1' => true,
            'flag2' => true
        ], [
            'flag2' => 'same_strict:flag1'
        ]);

        $this->assertTrue($validation->passes());
    }

    public function testInvalidSameStrictDifferentBooleanTypes()
    {
        // Test different boolean types (true vs 1)
        $validator = new Rakit\Validation\Validator();
        $validation = $validator->validate([
            'flag1' => true,
            'flag2' => 1 // boolean vs integer
        ], [
            'flag2' => 'same_strict:flag1'
        ]);

        $this->assertTrue($validation->fails());
    }

    public function testValidSameStrictDifferentNullTypes()
    {
        // Test different null types (null vs '') - these should NOT be same strict
        $validator = new Rakit\Validation\Validator();
        $validation = $validator->validate([
            'field1' => null,
            'field2' => '' // null vs empty string
        ], [
            'field2' => 'same_strict:field1'
        ]);
        $this->assertTrue($validation->fails());
    }

    public function testValidSameStrictSameNullValues()
    {
        // Test same null values
        $validator = new Rakit\Validation\Validator();
        $validation = $validator->validate([
            'field1' => null,
            'field2' => null
        ], [
            'field2' => 'same_strict:field1'
        ]);

        $this->assertTrue($validation->passes());
    }

    public function testValidSameStrictSameArrayValues()
    {
        // Test same array values
        $validator = new Rakit\Validation\Validator();
        $validation = $validator->validate([
            'array1' => ['a', 'b', 'c'],
            'array2' => ['a', 'b', 'c']
        ], [
            'array2' => 'same_strict:array1'
        ]);

        $this->assertTrue($validation->passes());
    }

    public function testInvalidSameStrictDifferentArrayValues()
    {
        // Test different array values
        $validator = new Rakit\Validation\Validator();
        $validation = $validator->validate([
            'array1' => ['a', 'b', 'c'],
            'array2' => ['a', 'b', 'd']
        ], [
            'array2' => 'same_strict:array1'
        ]);

        $this->assertTrue($validation->fails());
    }

    public function testInvalidSameStrictDifferentArrayOrder()
    {
        // Test same array values but different order (should fail with strict comparison)
        $validator = new Rakit\Validation\Validator();
        $validation = $validator->validate([
            'array1' => ['a', 'b', 'c'],
            'array2' => ['c', 'b', 'a']
        ], [
            'array2' => 'same_strict:array1'
        ]);

        $this->assertTrue($validation->fails());
    }

    public function testMissingFieldParameter()
    {
        // Test missing field parameter
        $this->expectException(Rakit\Validation\MissingRequiredParameterException::class);
        $this->rule->fillParameters([])->check('value');
    }
}
