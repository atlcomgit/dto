<?php

namespace Atlcom\Dto\Tests\Examples\Example02;

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
 * Тест 02
 * Заполнение Dto из массива
 */

final class Example02Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $array = [
            'markName' => 'Lexus',
            'modelName' => 'RX500',
        ];

        $carDto = CarDto::create($array);

        $this->assertObjectHasProperty('markName', $carDto);
        $this->assertObjectHasProperty('modelName', $carDto);
        $this->assertEquals('Lexus', $carDto->markName);
        $this->assertEquals('RX500', $carDto->modelName);
    }
}

