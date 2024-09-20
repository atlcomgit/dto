<?php

namespace Atlcom\Tests\Examples\Example39;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto extends \Atlcom\Dto
{
    public ?string $markName;
    public ?string $modelName;
}

/**
 * Тест 39
 * Проверка на пустой dto
 */

final class Example39Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDto = CarDto::create();
        $this->assertTrue($carDto->isEmpty());
        
        $carDto = new CarDto();
        $this->assertTrue($carDto->isEmpty());
        
        $carDto = CarDto::create(markName: 'Lexus');
        $this->assertFalse($carDto->isEmpty());
    }
}
