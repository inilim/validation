<?php

namespace Rakit\Validation\Tests\Rules;

use Rakit\Validation\Rules\TypeArrayKeysOnlyString;

class TypeArrayKeysOnlyStringTest extends \Rakit\Validation\Tests\TestCase
{
    public function testValidStringKeys()
    {
        $rule = new TypeArrayKeysOnlyString();

        // Test with string keys
        $this->assertTrue($rule->check(['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3']));
        $this->assertTrue($rule->check(['string_key' => 'value']));
        $this->assertTrue($rule->check([])); // Empty array should be valid
    }

    public function testInvalidNonArray()
    {
        $rule = new TypeArrayKeysOnlyString();

        // Test with non-array values
        $this->assertFalse($rule->check('not an array'));
        $this->assertFalse($rule->check(123));
        $this->assertFalse($rule->check(123.2));
        $this->assertFalse($rule->check(false));
        $this->assertFalse($rule->check(null));
        $this->assertFalse($rule->check(new \stdClass()));
    }

    public function testInvalidIntegerKeys()
    {
        $rule = new TypeArrayKeysOnlyString();

        // Test with integer keys
        $this->assertFalse($rule->check([1 => 'value1', 2 => 'value2']));
        $this->assertFalse($rule->check([0 => 'value0', 10 => 'value10']));
    }

    public function testInvalidMixedKeys()
    {
        $rule = new TypeArrayKeysOnlyString();

        // Test with mixed keys (integer and string)
        $this->assertFalse($rule->check([1 => 'value1', 'key2' => 'value2']));
        $this->assertFalse($rule->check(['key1' => 'value1', 2 => 'value2']));
    }

    public function testValidWithVariousStringKeys()
    {
        $rule = new TypeArrayKeysOnlyString();

        // Test with various string keys
        $this->assertTrue($rule->check(['a' => 'value1', 'b' => 'value2']));
        $this->assertTrue($rule->check(['_underscore' => 'value', 'with-dash' => 'value2']));
        $this->assertTrue($rule->check(['123string' => 'value'])); // String starting with numbers is still a string key
    }
    
    public function testAliasArrayKeysOnlyString()
    {
        $validator = new \Rakit\Validation\Validator();
        
        // Test with main rule name
        $validation1 = $validator->validate([
            'test_array' => ['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3']
        ], [
            'test_array' => 'array_keys_only_str'
        ]);
        
        $this->assertTrue($validation1->passes());
        
        // Test with alias
        $validation2 = $validator->validate([
            'test_array' => ['key1' => 'value1', 'key2' => 'value2', 'key3' => 'value3']
        ], [
            'test_array' => 'array_keys_only_string'
        ]);
        
        $this->assertTrue($validation2->passes());
        
        // Test with invalid data using main rule name
        $validation3 = $validator->validate([
            'test_array' => [0 => 'value1', 1 => 'value2']
        ], [
            'test_array' => 'array_keys_only_str'
        ]);
        
        $this->assertFalse($validation3->passes());
        
        // Test with invalid data using alias
        $validation4 = $validator->validate([
            'test_array' => [0 => 'value1', 1 => 'value2']
        ], [
            'test_array' => 'array_keys_only_string'
        ]);
        
        $this->assertFalse($validation4->passes());
    }
}
