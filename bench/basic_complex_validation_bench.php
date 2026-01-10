<?php

require_once __DIR__ . '/boot.php';

use Rakit\Validation\Validator;

// Подготовка данных для тестирования
$data = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'age' => 25,
    'website' => 'https://example.com',
    'description' => 'This is a sample description',
    'tags' => ['php', 'validation', 'benchmark'],
    'score' => 95.5,
    'active' => true,
];

$validator = new Validator();

// Базовый бенчмарк для комплексной валидации
$result = timedMsCall(function () use ($data, $validator) {
    $validation = $validator->validate($data, [
        'name' => 'required|str_strict|min:2|max:50',
        'email' => 'required|email',
        'age' => 'required|integer|min:18|max:99',
        'website' => 'url',
        'description' => 'required|str_strict|min:10|max:200',
        'tags' => 'array|min:1|max:10',
        'score' => 'required|numeric|min:0|max:100',
        'active' => 'boolean',
    ]);

    return $validation;
});

echo "Basic Complex Validation Bench:\n";
echo "Time: {$result['time']} ms\n";
echo "Memory: {$result['memory']} bytes\n";
echo "Result: " . ($result['result']->passes() ? 'Valid' : 'Invalid') . "\n\n";

// Повторяем измерения для получения среднего значения
$totalTime = 0;
$totalMemory = 0;
$validations = 0;

for ($i = 0; $i < 100; $i++) {
    $result = timedMsCall(function () use ($data, $validator) {
        $validator->validate($data, [
            'name' => 'required|str_strict|min:2|max:50',
            'email' => 'required|email',
            'age' => 'required|integer|min:18|max:99',
            'website' => 'url',
            'description' => 'required|str_strict|min:10|max:200',
            'tags' => 'array|min:1|max:10',
            'score' => 'required|numeric|min:0|max:100',
            'active' => 'boolean',
        ]);
    });

    $totalTime += $result['time'];
    $totalMemory += $result['memory'];
    $validations++;
}

echo "Average over 100 runs:\n";
echo "Avg Time: " . ($totalTime / $validations) . " ms\n";
echo "Avg Memory: " . ($totalMemory / $validations) . " bytes\n\n";
