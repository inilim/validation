<?php

use Inilim\Tool\VD;
use Rakit\Validation\Validator;

require_once __DIR__ . '/vendor/autoload.php';

$validator = new Validator;

// make it
$validation = $validator->make(
    [
        'test' => 123,
        'skills' => ['1'],
    ],
    [
        'test'                  => 'required|int_strict',
        'name'                  => 'required',
        'email'                 => 'required|email',
        'password'              => 'required|min:6',
        'confirm_password'      => 'required|same:password',
        'skills'                => 'required|array_count_between:2,3',
        'skills.*'              => 'required|str_strict',
    ],
    [
        'skills:array_count_between' => 'wadawdwa',
    ]
);
$validation->validate();

VD::de($validation->errors());
