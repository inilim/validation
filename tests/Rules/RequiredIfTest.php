<?php

namespace Tests\Rules;

use Rakit\Validation\Rules\RequiredIf;
use Rakit\Validation\Validator;

class RequiredIfTest extends \PHPUnit\Framework\TestCase
{
    protected $validator;

    protected function setUp(): void
    {
        $this->validator = new Validator;
    }
    // Проверяет, что правило возвращает true, когда условие не выполняется (другое поле не содержит указанные значения)
    public function testValidWhenConditionNotMet()
    {
        $validation = $this->validator->validate([
            'email' => 'john@example.com',
            'phone' => '' // необязательное поле, так как condition_field не равно 'yes'
        ], [
            'phone' => 'required_if:condition_field,yes'
        ]);

        $this->assertTrue($validation->passes());
    }

    // Проверяет, что правило возвращает false, когда условие выполняется, но обязательное поле пустое
    public function testInvalidWhenConditionMetAndFieldEmpty()
    {
        $validation = $this->validator->validate([
            'condition_field' => 'yes',
            'phone' => '' // обязательное поле, так как condition_field равно 'yes', но значение пустое
        ], [
            'phone' => 'required_if:condition_field,yes'
        ]);

        $this->assertFalse($validation->passes());
    }

    // Проверяет, что правило возвращает true, когда условие выполняется и обязательное поле заполнено
    public function testValidWhenConditionMetAndFieldFilled()
    {
        $validation = $this->validator->validate([
            'condition_field' => 'yes',
            'phone' => '1234567890' // обязательное поле, так как condition_field равно 'yes'
        ], [
            'phone' => 'required_if:condition_field,yes'
        ]);

        $this->assertTrue($validation->passes());
    }

    // Проверяет, что правило работает с несколькими возможными значениями
    public function testValidWithMultipleConditionValues()
    {
        $validation = $this->validator->validate([
            'condition_field' => 'maybe',
            'phone' => '1234567890' // обязательное поле, так как condition_field равно 'maybe', что входит в список значений
        ], [
            'phone' => 'required_if:condition_field,yes,no,maybe'
        ]);

        $this->assertTrue($validation->passes());
    }

    // Проверяет, что правило возвращает false, когда одно из нескольких условий выполняется, но обязательное поле пустое
    public function testInvalidWithMultipleConditionValues()
    {
        $validation = $this->validator->validate([
            'condition_field' => 'yes',
            'phone' => '' // обязательное поле, так как condition_field равно 'yes', что входит в список значений, но значение пустое
        ], [
            'phone' => 'required_if:condition_field,yes,no,maybe'
        ]);

        $this->assertFalse($validation->passes());
    }

    // Проверяет, что правило возвращает true, когда условие не выполняется среди нескольких возможных значений
    public function testValidWhenNoneOfMultipleConditionsMet()
    {
        $validation = $this->validator->validate([
            'condition_field' => 'other',
            'phone' => '' // необязательное поле, так как condition_field не равно ни одному из значений в списке
        ], [
            'phone' => 'required_if:condition_field,yes,no,maybe'
        ]);

        $this->assertTrue($validation->passes());
    }

    // Проверяет, что правило выбрасывает исключение при отсутствии необходимых параметров
    public function testThrowsExceptionWhenMissingParams()
    {
        $this->expectException(\Rakit\Validation\MissingRequiredParameterException::class);

        $validation = $this->validator->validate([
            'field' => 'value'
        ], [
            'field' => 'required_if' // параметры field и values отсутствуют
        ]);

        $validation->passes();
    }
}
