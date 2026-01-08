<?php

use Inilim\Tool\VD;
use Rakit\Validation\Validator;

require_once __DIR__ . '/vendor/autoload.php';

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
$onlyValidData = $validation->getOnlyValidData();
VD::de($onlyValidData);
