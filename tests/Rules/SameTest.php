<?php

use Rakit\Validation\Rules\Same;
use Rakit\Validation\Tests\TestCase;

class SameTest extends TestCase
{
    protected function setUp(): void
    {
        $this->rule = new Same();
    }

    public function testValidSameValues()
    {
        // Test same values
        $validator = new Rakit\Validation\Validator();
        $validation = $validator->validate([
            'password' => 'secret',
            'password_confirmation' => 'secret'
        ], [
            'password_confirmation' => 'same:password'
        ]);

        $this->assertTrue($validation->passes());
    }

    public function testInvalidSameDifferentValues()
    {
        // Test different values
        $validator = new Rakit\Validation\Validator();
        $validation = $validator->validate([
            'password' => 'secret',
            'password_confirmation' => 'different'
        ], [
            'password_confirmation' => 'same:password'
        ]);

        $this->assertTrue($validation->fails());
    }

    public function testValidSameBothEmpty()
    {
        // Test both values empty
        $validator = new Rakit\Validation\Validator();
        $validation = $validator->validate([
            'field1' => '',
            'field2' => ''
        ], [
            'field2' => 'same:field1'
        ]);

        $this->assertTrue($validation->passes());
    }

    public function testInvalidSameEmptyVsNonEmpty()
    {
        // Test empty vs non-empty values
        $validator = new Rakit\Validation\Validator();
        $validation = $validator->validate([
            'field1' => 'value',
            'field2' => ''
        ], [
            'field2' => 'same:field1'
        ]);

        $this->assertTrue($validation->fails());
    }

    public function testSameDoesNotBreakOtherRulesInChain()
    {
        // Test that same doesn't break other rules in validation chain
        $validator = new Rakit\Validation\Validator();
        $validation = $validator->validate([
            'password' => 'secret',
            'password_confirmation' => 'different123' // different (so same fails) and has numbers (so alpha fails too)
        ], [
            'password_confirmation' => 'same:password|alpha'
        ]);

        // Both same and alpha should fail
        $this->assertFalse($validation->passes());
        $this->assertTrue($validation->errors()->has('password_confirmation'));
        
        // Should have both errors
        $errors = $validation->errors()->get('password_confirmation');
        $this->assertArrayHasKey('same', $errors);
        $this->assertArrayHasKey('alpha', $errors);
    }

    public function testMissingFieldParameter()
    {
        // Test missing field parameter
        $this->expectException(Rakit\Validation\MissingRequiredParameterException::class);
        $this->rule->fillParameters([])->check('value');
    }
}