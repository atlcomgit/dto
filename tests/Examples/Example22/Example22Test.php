<?php

namespace Atlcom\Tests\Examples\Example22;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto extends \Atlcom\Dto
{
    public string $markName = 'Lexus';
    public ?string $modelName = null;
}

/**
 * Тест 22
 * Сериализация Dto в массив с использованием onlyFilled
 */

final class Example22Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carArray = CarDto::create()->onlyFilled()->toArray();

        $this->assertArrayHasKey('markName', $carArray);
        $this->assertArrayNotHasKey('modelName', $carArray);
        $this->assertEquals('Lexus', $carArray['markName']);
    }
}
