<?php

namespace Atlcom\Tests\Examples\Example37;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto extends \Atlcom\Dto
{
    public string $markName = 'Lexus';
    public string $modelName = 'RX500';
}

/**
 * Тест 37
 * Добавление своих опций в dto с использованием customOptions
 */

final class Example37Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDto = CarDto::create()
            ->customOptions(['firstOption' => 1])
            ->customOptions(['secondOption' => 2])
        ;

        $customOptions = $carDto->getOption('customOptions');

        $this->assertArrayHasKey('firstOption', $customOptions);
        $this->assertArrayHasKey('secondOption', $customOptions);
        $this->assertEquals(1, $customOptions['firstOption']);
        $this->assertEquals(2, $customOptions['secondOption']);
    }
}
