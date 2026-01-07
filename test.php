<?php

use Inilim\Tool\VD;
use Rakit\Validation\Validator;

require_once __DIR__ . '/vendor/autoload.php';

$validator = new Rakit\Validation\Validator();

// Test 1: Empty confirm_password but password is set - should fail
$validation = $validator->make([
    'password' => 'secret',
    'confirm_password' => '' // empty value to test the rule behavior
], [
    'confirm_password' => 'same:password'
]);
$validation->validate();
VD::d("Test 1 - Empty confirm_password:");
VD::d($validation->errors()->first('confirm_password'));
VD::d($validation->passes());

// Test 2: Both values are empty - should pass
$validation2 = $validator->make([
    'password' => '',
    'confirm_password' => '' // both empty
], [
    'confirm_password' => 'same:password'
]);
$validation2->validate();
VD::d("Test 2 - Both values empty:");
VD::d($validation2->errors()->first('confirm_password'));
VD::d($validation2->passes());

// Test 3: Different values - should fail
$validation3 = $validator->make([
    'password' => 'secret',
    'confirm_password' => 'different'
], [
    'confirm_password' => 'same:password'
]);
$validation3->validate();
VD::d("Test 3 - Different values:");
VD::d($validation3->errors()->first('confirm_password'));
VD::d($validation3->passes());

// Test 4: Same values - should pass
$validation4 = $validator->make([
    'password' => 'secret',
    'confirm_password' => 'secret'
], [
    'confirm_password' => 'same:password'
]);
$validation4->validate();
VD::d("Test 4 - Same values:");
VD::d($validation4->errors()->first('confirm_password'));
VD::d($validation4->passes());

// Test 5: SameStrict with different types (string vs int) - should fail
$validation5 = $validator->make([
    'number' => 123,
    'confirm_number' => '123' // string vs integer
], [
    'confirm_number' => 'same_strict:number'
]);
$validation5->validate();
VD::d("Test 5 - SameStrict different types:");
VD::d($validation5->errors()->first('confirm_number'));
VD::d($validation5->passes());

// Test 6: SameStrict with same types - should pass
$validation6 = $validator->make([
    'number' => 123,
    'confirm_number' => 123 // both integers
], [
    'confirm_number' => 'same_strict:number'
]);
$validation6->validate();
VD::d("Test 6 - SameStrict same types:");
VD::d($validation6->errors()->first('confirm_number'));
VD::d($validation6->passes());

// exit;