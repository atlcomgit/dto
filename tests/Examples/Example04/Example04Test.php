<?php

namespace Atlcom\Dto\Tests\Examples\Example04;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto extends \Atlcom\Dto\DefaultDto
{
    public string $markName;
    public string $modelName;
}

/**
 * Тест 04
 * Заполнение Dto из строки json
 */

final class Example04Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDto = CarDto::create('{"markName": "Lexus", "modelName": "RX500"}');

        $this->assertObjectHasProperty('markName', $carDto);
        $this->assertObjectHasProperty('modelName', $carDto);
        $this->assertEquals('Lexus', $carDto->markName);
        $this->assertEquals('RX500', $carDto->modelName);
    }
}
