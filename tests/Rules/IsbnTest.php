<?php

namespace Rakit\Validation\Tests;

use Rakit\Validation\Rules\Isbn;

class IsbnTest extends \Rakit\Validation\Tests\TestCase
{
    function testValid()
    {
        $rule = new Isbn;
        foreach (
            [
                'ISBN-13: 978-0-596-52068-7',
                '978 0 596 52068 7',
                '9780596520687',
                '0-596-52068-9',
                '0 512 52068 9',
                'ISBN-10 0-596-52068-9',
                'ISBN-10: 0-596-52068-9',
            ] as $input
        ) {
            $this->assertTrue($rule->check($input));
        }
    }

    function testInvalid()
    {
        $rule = new Isbn;
        foreach (
            [
                'ISBN 11978-0-596-52068-7',
                'ISBN-12: 978-0-596-52068-7',
                '978 10 596 52068 7',
                '119780596520687',
                '0-5961-52068-9',
                '11 5122 52068 9',
                'ISBN-11 0-596-52068-9',
                'ISBN-10- 0-596-52068-9',
                'Defiatly no ISBN',
                'Neither ISBN-13: 978-0-596-52068-7',
            ] as $input
        ) {
            $this->assertFalse($rule->check($input));
        }
    }
}
