<?php

namespace Rakit\Validation\Tests\Rules;

use Rakit\Validation\Rules\TypeArrayKeysOnlyInt;

class TypeArrayKeysOnlyIntTest extends \Rakit\Validation\Tests\TestCase
{
    public function testValidIntegerKeys()
    {
        $rule = new TypeArrayKeysOnlyInt();

        // Test with integer keys
        $this->assertTrue($rule->check([1 => 'value1', 2 => 'value2', 3 => 'value3']));
        $this->assertTrue($rule->check([0 => 'value0', 10 => 'value10']));
        $this->assertTrue($rule->check([])); // Empty array should be valid
    }

    public function testInvalidNonArray()
    {
        $rule = new TypeArrayKeysOnlyInt();

        // Test with non-array values
        $this->assertFalse($rule->check('not an array'));
        $this->assertFalse($rule->check(123));
        $this->assertFalse($rule->check(123.2));
        $this->assertFalse($rule->check(true));
        $this->assertFalse($rule->check(null));
        $this->assertFalse($rule->check(new \stdClass()));
    }

    public function testInvalidStringKeys()
    {
        $rule = new TypeArrayKeysOnlyInt();

        // Test with string keys
        $this->assertFalse($rule->check(['key1' => 'value1', 'key2' => 'value2']));
        $this->assertFalse($rule->check(['string_key' => 'value']));
    }

    public function testInvalidMixedKeys()
    {
        $rule = new TypeArrayKeysOnlyInt();

        // Test with mixed keys (integer and string)
        $this->assertFalse($rule->check([1 => 'value1', 'key2' => 'value2']));
        $this->assertFalse($rule->check(['key1' => 'value1', 2 => 'value2']));
    }

    public function testValidWithVariousIntegerKeys()
    {
        $rule = new TypeArrayKeysOnlyInt();

        // Test with various integer keys including negative
        $this->assertTrue($rule->check([-1 => 'negative', 0 => 'zero', 1 => 'positive']));
        $this->assertTrue($rule->check([100 => 'large_number', 5 => 'small_number']));
    }
    
    public function testAliasArrayKeysOnlyInteger()
    {
        $validator = new \Rakit\Validation\Validator();
        
        // Test with main rule name
        $validation1 = $validator->validate([
            'test_array' => [0 => 'value1', 1 => 'value2', 2 => 'value3']
        ], [
            'test_array' => 'array_keys_only_int'
        ]);
        
        $this->assertTrue($validation1->passes());
        
        // Test with alias
        $validation2 = $validator->validate([
            'test_array' => [0 => 'value1', 1 => 'value2', 2 => 'value3']
        ], [
            'test_array' => 'array_keys_only_integer'
        ]);
        
        $this->assertTrue($validation2->passes());
        
        // Test with invalid data using main rule name
        $validation3 = $validator->validate([
            'test_array' => ['key1' => 'value1', 'key2' => 'value2']
        ], [
            'test_array' => 'array_keys_only_int'
        ]);
        
        $this->assertFalse($validation3->passes());
        
        // Test with invalid data using alias
        $validation4 = $validator->validate([
            'test_array' => ['key1' => 'value1', 'key2' => 'value2']
        ], [
            'test_array' => 'array_keys_only_integer'
        ]);
        
        $this->assertFalse($validation4->passes());
    }
}
