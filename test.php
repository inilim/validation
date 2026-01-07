<?php

use Rakit\Validation\Validator;

require_once __DIR__ . '/vendor/autoload.php';

$validator = new Validator;

// make it
$validation = $validator->make([], [
    'name'                  => 'required',
    'email'                 => 'required|email',
    'password'              => 'required|min:6',
    'confirm_password'      => 'required|same:password',
    'avatar'                => 'required|uploaded_file:0,500K,png,jpeg',
    'skills'                => 'array',
    'skills.*.id'           => 'required|numeric',
    'skills.*.percentage'   => 'required|numeric'
]);
