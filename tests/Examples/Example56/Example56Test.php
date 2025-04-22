<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example56;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto extends \Atlcom\Dto
{
    public string $markName;
}

/**
 * Тест 56
 * Работа с магическим методом __call (обращение к свойствам Dto как к методам)
 */

final class Example56Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDto = CarDto::create(markName: 'Lexus');
        
        $this->assertTrue($carDto->markName === 'Lexus');

        $carDto->markName('Toyota');

        $this->assertTrue($carDto->markName === 'Toyota');
        $this->assertTrue($carDto->markName() === 'Toyota');
    }
}