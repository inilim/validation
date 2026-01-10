<?php

require_once __DIR__ . '/boot.php';

use Rakit\Validation\Validator;

// Подготовка данных для тестирования
$datasets = [
    // Данные для тестирования email и основных правил
    [
        'type' => 'basic_email_validation',
        'data' => [
            'email' => 'test@example.com',
            'name' => 'John Doe',
            'age' => 30,
            'active' => true
        ],
        'rules' => [
            'email' => 'required|email',
            'name' => 'required|str_strict|max:50',
            'age' => 'required|integer|min:0|max:120',
            'active' => 'boolean'
        ]
    ],
    // Данные для тестирования массивов
    [
        'type' => 'array_validation',
        'data' => [
            'items' => ['item1', 'item2', 'item3'],
            'numbers' => [1, 2, 3, 4, 5],
            'mixed' => ['string', 123, true, ['nested' => 'value']]
        ],
        'rules' => [
            'items' => 'required|array|min:1|max:10',
            'items.*' => 'str_strict|max:50',
            'numbers' => 'array|min:1|max:20',
            'numbers.*' => 'integer|min:0',
            'mixed' => 'array|max:10',
            'mixed.*' => 'max:100'
        ]
    ],
    // Данные для тестирования строк и сложных правил
    [
        'type' => 'string_and_complex_validation',
        'data' => [
            'title' => 'Sample Title',
            'content' => 'This is a sample content for testing',
            'tags' => ['php', 'validation', 'benchmark'],
            'status' => 'published',
            'priority' => 2
        ],
        'rules' => [
            'title' => 'required|str_strict|min:5|max:100',
            'content' => 'required|str_strict|min:10|max:1000',
            'tags' => 'array|max:20',
            'tags.*' => 'str_strict|in:php,javascript,python,java,csharp,go,rust',
            'status' => 'required|in:draft,published,archived',
            'priority' => 'integer|min:1|max:5'
        ]
    ]
];

$validator = new Validator();

foreach ($datasets as $dataset) {
    echo "Testing: {$dataset['type']}\n";

    // Одиночное измерение
    $result = timedMsCall(function () use ($dataset, $validator) {
        $validation = $validator->validate($dataset['data'], $dataset['rules']);
        return $validation;
    });

    echo "Single run - Time: {$result['time']} ms, Memory: {$result['memory']} bytes\n";
    echo "Result: " . ($result['result']->passes() ? 'Valid' : 'Invalid') . "\n";

    // Измерения для получения среднего значения
    $totalTime = 0;
    $totalMemory = 0;
    $validations = 0;

    for ($i = 0; $i < 50; $i++) {
        $result = timedMsCall(function () use ($dataset, $validator) {
            $validation = $validator->validate($dataset['data'], $dataset['rules']);
            return $validation;
        });

        $totalTime += $result['time'];
        $totalMemory += $result['memory'];
        $validations++;
    }

    echo "Average over 50 runs - Avg Time: " . ($totalTime / $validations) . " ms, ";
    echo "Avg Memory: " . ($totalMemory / $validations) . " bytes\n\n";
}
