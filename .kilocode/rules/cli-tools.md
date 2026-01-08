# CLI иснтрументы

## phpunit юнит тестирование
- Для юнит тестов используй команду "phpunit", аргументов нет. (Последний результат записывается в файл ./files/phpunit-last-output.txt)
```bash
phpunit
```

Так работать не будет:
```bash
phpunit tests/NameTest.php --verbose
```
```bash
phpunit --filter NameTest
```

Только так (Проверка всех тестов):
```bash
phpunit
```