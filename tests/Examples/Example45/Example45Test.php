<?php

namespace Atlcom\Tests\Examples\Example45;

use Atlcom\Traits\AsDto;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto
{
    use AsDto;

    public string $markName;
}

/**
 * Тест 45
 * Создание своего Dto с расширением функционала через трейт
 */

final class Example45Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDto1 = CarDto::create(markName: 'Lexus');

        $this->assertTrue($carDto1->markName === 'Lexus');
    }
}
