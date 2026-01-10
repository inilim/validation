<?php

require_once __DIR__ . '/boot.php';
require_once __DIR__ . '/../vendor/autoload.php';

use Rakit\Validation\Validator;

echo "Memory Leak Test Bench - Starting...\n";

// Подготовка тестовых данных
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

$rules = [
    'name' => 'required|str_strict|min:2|max:50',
    'email' => 'required|email',
    'age' => 'required|integer|min:18|max:99',
    'website' => 'url',
    'description' => 'required|str_strict|min:10|max:200',
    'tags' => 'array|min:1|max:10',
    'score' => 'required|numeric|min:0|max:100',
    'active' => 'boolean',
];

$validator = new Validator();

// Измеряем начальное состояние памяти
$initialMemory = memory_get_usage(true);
echo "Initial memory usage: " . formatBytes($initialMemory) . "\n";

$iterations = 10_000; // Количество итераций для теста
$results = [];

echo "Running {$iterations} validations...\n";

// Выполняем большое количество валидаций для проверки утечки памяти
for ($i = 0; $i < $iterations; $i++) {
    // Создаем новую валидацию в каждой итерации
    $validation = $validator->validate($data, $rules);

    // Сохраняем результат валидации для предотвращения оптимизации
    $results[] = $validation->passes();

    // Каждые 100 итераций выводим информацию о текущем использовании памяти
    if (($i + 1) % 100 === 0) {
        $currentMemory = memory_get_usage(true);
        echo "Iteration " . ($i + 1) . " - Memory usage: " . formatBytes($currentMemory) .
            " (diff: " . formatBytes($currentMemory - $initialMemory) . ")\n";
    }
}

// Измеряем конечное состояние памяти
$finalMemory = memory_get_usage(true);
echo "Final memory usage: " . formatBytes($finalMemory) . "\n";
echo "Total difference: " . formatBytes($finalMemory - $initialMemory) . "\n";

// Освобождаем результаты для проверки сборщика мусора
unset($results);

// Запускаем сборщик мусора и снова измеряем память
gc_collect_cycles();
$afterGCMemory = memory_get_usage(true);
echo "Memory usage after garbage collection: " . formatBytes($afterGCMemory) . "\n";
echo "Difference after GC: " . formatBytes($afterGCMemory - $initialMemory) . "\n";

echo "\nMemory Leak Test Complete!\n";

/**
 * Форматирует размер памяти в удобочитаемый вид
 */
function formatBytes(int $size, int $precision = 2): string
{
    $units = ['B', 'KB', 'MB', 'GB'];

    $i = 0;
    while ($size >= 1024 && $i < count($units) - 1) {
        $size /= 1024;
        $i++;
    }

    return round($size, $precision) . ' ' . $units[$i];
}
