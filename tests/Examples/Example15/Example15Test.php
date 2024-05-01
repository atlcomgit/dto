<?php

namespace Atlcom\Dto\Tests\Examples\Example15;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto extends \Atlcom\Dto\DefaultDto
{
    public string $markName = 'Lexus';

    protected function onSerializing(array &$array): void
    {
        $array['modelName'] = 'RX500';
    }
}

/**
 * Тест 15
 * Заполнение Dto с событием onSerializing (перед преобразованием в массив)
 */

final class Example15Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carArray = CarDto::create()->toArray();

        $this->assertArrayHasKey('markName', $carArray);
        $this->assertArrayHasKey('modelName', $carArray);
        $this->assertEquals('Lexus', $carArray['markName'] ?? null);
        $this->assertEquals('RX500', $carArray['modelName'] ?? null);
    }
}
