<?php

require_once __DIR__ . '/bootstrap.php';

use Rakit\Validation\Validator;


de(\explode(':', $rule ?? '123', 2));

$inputs = [];

$rules = [
    'company_id'      => 'nullable|isNumeric|maxLen:48',
    'date_meter'      => 'nullable|date:Y-m-d H:i:s',
    'brand'           => 'nullable|maxLen:255',
    'type_meter'      => 'nullable|maxLen:255',
    'type_repair'     => 'nullable|maxLen:255',
    'type_building'   => 'nullable|maxLen:255',
    'discount'        => 'nullable|integer',
    'count_area'      => ['nullable', function ($v) {
        return \is_float($v) || \is_int($v);
    }],
    'adress'          => 'nullable|maxLen:255',
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
    'meet_format'     => 'nullable|maxLen:64',
    'time_meter'      => 'nullable|maxLen:16',
];


$rules = \array_map(static function ($rule) {
    if (\is_array($rule)) {
        foreach ($rule as $key => $ruleItem) {
            if (\is_string($ruleItem)) {
                unset($rule[$key]);
                $rule = \array_merge(\explode('|', $ruleItem), $rule);
            }
        }
    }
    return $rule;
}, $rules);

$v = (new Validator)->make($inputs, $rules);



$v->validate();
$v->errors()->toArray();
