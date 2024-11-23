<?php

require_once __DIR__ . '/bootstrap.php';

use Rakit\Validation\Validator;


$inputs = [];

$rules = [
    'company_id'      => 'nullable|integer|max:48',
    'date_meter'      => 'nullable|date:Y-m-d H:i:s',
    'brand'           => 'nullable|max:255',
    'type_meter'      => 'nullable|max:255',
    'type_repair'     => 'nullable|max:255',
    'type_building'   => 'nullable|max:255',
    'discount'        => 'required|integer',
    'count_area'      => ['nullable', function ($v) {
        return \is_float($v) || \is_int($v);
    }],
    'adress'          => 'nullable|max:255',
    'showroom'        => 'nullable|integer',
    'team_id'         => 'nullable|integer',
    'manager'         => 'nullable|integer',
    'status'          => 'nullable|integer',
    'package'         => 'nullable',
    'budget'          => 'nullable|integer',
    'date_contract'   => 'nullable|date:Y-m-d H:i:s',
    'sum_contract'    => 'nullable|integer',
    'sum_stage1'      => 'nullable|integer',
    'date_end_stage1' => 'nullable|date:Y-m-d H:i:s',
    'sum_stage2'      => 'nullable|integer',
    'date_end_stage2' => 'nullable|date:Y-m-d H:i:s',
    'sum_stage3'      => 'nullable|integer',
    'date_end_stage3' => 'nullable|date:Y-m-d H:i:s',
    'date_end_fact'   => 'nullable|date:Y-m-d H:i:s',
    'date_update'     => 'nullable|date:Y-m-d H:i:s',
    'meet_format'     => 'nullable|max:64',
    'time_meter'      => 'nullable|max:16',
];

$validator = new Validator;

$validator->validate();

// $v = $validator->make($inputs, $rules);

$v = $v->validate();
$err = $v->errors()->toArray();

$a = $v->getValidatedData();

de(get_included_files());

// d($err);
// de($a);
