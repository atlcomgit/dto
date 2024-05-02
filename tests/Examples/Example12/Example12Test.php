<?php

namespace Atlcom\Tests\Examples\Example12;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto extends \Atlcom\Dto
{
    public ?string $markName;
    public ?string $modelName;

    protected function onFilled(array $array): void
    {
        $this->markName = 'Lexus';
        $this->modelName = 'RX500';
    }
}

/**
 * Тест 12
 * Заполнение Dto с событием onFilled (после заполнения)
 */

final class Example12Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDto = CarDto::create();

        $this->assertObjectHasProperty('markName', $carDto);
        $this->assertObjectHasProperty('modelName', $carDto);
        $this->assertEquals('Lexus', $carDto->markName);
        $this->assertEquals('RX500', $carDto->modelName);
    }
}
