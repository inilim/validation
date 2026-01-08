<?php

namespace Rakit\Validation\Tests;

use Inilim\Tool\VD;
use PHPUnit\Framework\TestCase;
use Rakit\Validation\Validator;

class GetOnlyValidDataTest extends TestCase
{
    public function test_get_only_valid_data_returns_only_validated_attributes()
    {
        $validator = new Validator();

        $inputs = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'age' => 25,
            'extra_field' => 'should_not_be_included'
        ];

        $rules = [
            'name' => 'required|str_strict',
            'email' => 'required|email',
            'age' => 'required|integer'
        ];

        $validation = $validator->validate($inputs, $rules);
        
        $allValidData = $validation->getValidData();
        $onlyValidData = $validation->getOnlyValidData();
        
        // All valid data should include only validated fields (no extra fields without validation rules)
        $this->assertArrayHasKey('name', $allValidData);
        $this->assertArrayHasKey('email', $allValidData);
        $this->assertArrayHasKey('age', $allValidData);
        $this->assertArrayNotHasKey('extra_field', $allValidData);
        
        // Only valid data should include only validated fields
        $this->assertArrayHasKey('name', $onlyValidData);
        $this->assertArrayHasKey('email', $onlyValidData);
        $this->assertArrayHasKey('age', $onlyValidData);
        $this->assertArrayNotHasKey('extra_field', $onlyValidData);

        $this->assertEquals('John Doe', $onlyValidData['name']);
        $this->assertEquals('john@example.com', $onlyValidData['email']);
        $this->assertEquals(25, $onlyValidData['age']);
    }

    public function test_get_only_valid_data_with_nested_arrays()
    {
        $validator = new Validator();

        $inputs = [
            'config' => [
                'CREATE_TRY_RES' => false,
                'REQ_BLOCK' => false,
                'RES_BLOCK' => false,
                'REQ_DIR' => 'D:/projects/lazy_server/files/input/request/',
                'RES_DIR' => 'D:/projects/lazy_server/files/output/response',
                'TRY_RES_DIR' => 'D:/projects/lazy_server/files/input/try_response',
                'RESOURCES_DIR' => '/' // This should not be included as it has no validation rule
            ]
        ];

        $rules = [
            'config' => 'required|array',
            'config.CREATE_TRY_RES' => 'required|bool_strict',
            'config.REQ_BLOCK' => 'required|bool_strict',
            'config.RES_BLOCK' => 'required|bool_strict',
            'config.REQ_DIR' => 'required|str_strict',
            'config.RES_DIR' => 'required|str_strict',
            'config.TRY_RES_DIR' => 'required|str_strict',
        ];

        $validation = $validator->validate($inputs, $rules);
        
        $allValidData = $validation->getValidData();
        $onlyValidData = $validation->getOnlyValidData();
        
        // All valid data should include all config values (including RESOURCES_DIR which has no validation rule but is present in input)
        $flattenedAllValidData = \Rakit\Validation\Helper::arrayDot($allValidData);
        $this->assertArrayHasKey('config.CREATE_TRY_RES', $flattenedAllValidData);
        $this->assertArrayHasKey('config.REQ_BLOCK', $flattenedAllValidData);
        $this->assertArrayHasKey('config.RES_BLOCK', $flattenedAllValidData);
        $this->assertArrayHasKey('config.REQ_DIR', $flattenedAllValidData);
        $this->assertArrayHasKey('config.RES_DIR', $flattenedAllValidData);
        $this->assertArrayHasKey('config.TRY_RES_DIR', $flattenedAllValidData);
        $this->assertArrayHasKey('config.RESOURCES_DIR', $flattenedAllValidData);
        $this->assertEquals('/', $flattenedAllValidData['config.RESOURCES_DIR']);
        
        // Only valid data should include validated fields but not RESOURCES_DIR
        $flattenedOnlyValidData = \Rakit\Validation\Helper::arrayDot($onlyValidData);
        $this->assertArrayHasKey('config.CREATE_TRY_RES', $flattenedOnlyValidData);
        $this->assertArrayHasKey('config.REQ_BLOCK', $flattenedOnlyValidData);
        $this->assertArrayHasKey('config.RES_BLOCK', $flattenedOnlyValidData);
        $this->assertArrayHasKey('config.REQ_DIR', $flattenedOnlyValidData);
        $this->assertArrayHasKey('config.RES_DIR', $flattenedOnlyValidData);
        $this->assertArrayHasKey('config.TRY_RES_DIR', $flattenedOnlyValidData);
        $this->assertArrayNotHasKey('config.RESOURCES_DIR', $flattenedOnlyValidData);
    }

    public function test_get_only_valid_data_when_all_inputs_have_rules()
    {
        $validator = new Validator();

        $inputs = [
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ];

        $rules = [
            'name' => 'required|str_strict',
            'email' => 'required|email'
        ];

        $validation = $validator->validate($inputs, $rules);

        $allValidData = $validation->getValidData();
        $onlyValidData = $validation->getOnlyValidData();

        // When all inputs have rules, both methods should return the same data
        $this->assertEquals($allValidData, $onlyValidData);
    }

    public function test_get_only_valid_data_when_validation_fails()
    {
        $validator = new Validator();

        $inputs = [
            'name' => 'John Doe',
            'email' => 'invalid-email',
            'extra_field' => 'should_not_be_included'
        ];

        $rules = [
            'name' => 'required|str_strict',
            'email' => 'required|email'
        ];

        $validation = $validator->validate($inputs, $rules);

        $onlyValidData = $validation->getOnlyValidData();

        // Only the valid field should be returned
        $this->assertArrayHasKey('name', $onlyValidData);
        $this->assertArrayNotHasKey('email', $onlyValidData);
        $this->assertArrayNotHasKey('extra_field', $onlyValidData);

        $this->assertEquals('John Doe', $onlyValidData['name']);
    }

    public function test_get_only_valid_data_with_empty_input()
    {
        $validator = new Validator();

        $inputs = [];
        $rules = [];

        $validation = $validator->validate($inputs, $rules);

        $onlyValidData = $validation->getOnlyValidData();

        $this->assertEmpty($onlyValidData);
    }
}
