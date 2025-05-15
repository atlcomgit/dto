<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example58;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto extends \Atlcom\Dto
{
    public string $markName;
    public ?string $modelName;
}

/**
 * Тест 58
 * Работа с преобразованием массива в коллекцию из dto
 */

final class Example58Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDtoArray = CarDto::collect([
            ['markName' => 'Lexus', 'modelName' => 'RX500'],
            ['markName' => 'Toyota', 'modelName' => 'Allion'],
        ]);

        $this->assertIsArray($carDtoArray);
        $this->assertInstanceOf(CarDto::class, $carDtoArray[0]);
        $this->assertInstanceOf(CarDto::class, $carDtoArray[1]);

        $this->assertEquals(CarDto::create(markName: 'Lexus', modelName: 'RX500'), $carDtoArray[0]);
        $this->assertEquals(CarDto::create(markName: 'Toyota', modelName: 'Allion'), $carDtoArray[1]);
    }
}