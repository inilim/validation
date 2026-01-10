<?php

require_once __DIR__ . '/boot.php';

use Rakit\Validation\Validator;

// Подготовка данных для тестирования
$data = [
    'username' => 'testuser',
    'password' => 'secret123',
    'confirm_password' => 'secret123',
    'birth_date' => '1990-05-15',
    'salary' => 50000.50,
    'skills' => ['php', 'javascript', 'python'],
    'profile' => [
        'bio' => 'Software developer with 5 years experience',
        'location' => 'New York'
    ],
    'website' => 'https://example.com',
    'phone' => '+1234567890',
    'age' => 32
];

$validator = new Validator();

// Бенчмарк со сложными правилами
$result = timedMsCall(function () use ($data, $validator) {
    $validation = $validator->validate($data, [
        'username' => 'required|str_strict|min:3|max:20|regex:/^[a-zA-Z0-9_]+$/',
        'password' => 'required|str_strict|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/',
        'confirm_password' => 'required|str_strict|same:password',
        'birth_date' => 'required|date|before:today',
        'salary' => 'required|numeric|min:0',
        'skills' => 'required|array|min:1|max:10',
        'skills.*' => 'str_strict|min:2|max:20',
        'profile.bio' => 'str_strict|max:500',
        'profile.location' => 'str_strict|max:100',
        'website' => 'url',
        'phone' => 'str_strict|regex:/^\+[\d]{10,15}$/',
        'age' => 'required|integer|between:18,100'
    ]);

    return $validation;
});

echo "Advanced Rules Validation Bench:\n";
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
            'username' => 'required|str_strict|min:3|max:20|regex:/^[a-zA-Z0-9_]+$/',
            'password' => 'required|str_strict|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/',
            'confirm_password' => 'required|str_strict|same:password',
            'birth_date' => 'required|date|before:today',
            'salary' => 'required|numeric|min:0',
            'skills' => 'required|array|min:1|max:10',
            'skills.*' => 'str_strict|min:2|max:20',
            'profile.bio' => 'str_strict|max:500',
            'profile.location' => 'str_strict|max:100',
            'website' => 'url',
            'phone' => 'str_strict|regex:/^\+[\d]{10,15}$/',
            'age' => 'required|integer|between:18,100'
        ]);
    });

    $totalTime += $result['time'];
    $totalMemory += $result['memory'];
    $validations++;
}

echo "Average over 100 runs:\n";
echo "Avg Time: " . ($totalTime / $validations) . " ms\n";
echo "Avg Memory: " . ($totalMemory / $validations) . " bytes\n\n";
