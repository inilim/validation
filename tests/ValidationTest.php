<?php

namespace Rakit\Validation\Tests;

use Rakit\Validation\Validation;
use Rakit\Validation\Validator;
use ReflectionClass;

class ValidationTest extends \Rakit\Validation\Tests\TestCase
{
    function test_without_rule_required()
    {
        $inputs = [
            'config' => [
                'CREATE_TRY_RES' => false,
                'REQ_BLOCK' => false,
                'RES_BLOCK' => false,
                'REQ_DIR' => 'aaaa',
                'RES_DIR' => 'bbbb',
                'TRY_RES_DIR' => 'cccc',
                'RESOURCES_DIR' => [] // <--- ранее пропускал
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
            'config.RESOURCES_DIR' => 'str_strict',
        ];

        $validation = (new Validator)->make($inputs, $rules)->validate();

        // TODO отсутствие правила required игнорирует false в других правилах (а точнее даже не делает проверку),
        // и вносит данные в getValidData(), даже если они плохие
        $data = $validation->getValidData();
        $this->assertFalse(isset($data['config']['RESOURCES_DIR']));
    }

    /**
     * @param string $rules
     * @param array $expectedResult
     *
     * @dataProvider parseRuleProvider
     */
    public function testParseRule($rules, $expectedResult)
    {
        $class = new ReflectionClass(Validation::class);
        $method = $class->getMethod('parseRuleInstruction');
        $method->setAccessible(true);

        $validation = new Validation(new Validator(), [], []);

        $result = $method->invokeArgs($validation, [$rules]);
        $this->assertSame($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function parseRuleProvider()
    {
        return [
            [
                'email',
                [
                    'email',
                    [],
                ],
            ],
            [
                'min:6',
                [
                    'min',
                    ['6'],
                ],
            ],
            [
                'same:password',
                [
                    'same',
                    ['password'],
                ],
            ],
            [
                'regex:/^([a-zA-Z\,]*)$/',
                [
                    'regex',
                    ['/^([a-zA-Z\,]*)$/'],
                ],
            ],
        ];
    }
}
