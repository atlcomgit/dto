<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example18;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto extends \Atlcom\Dto
{
    public const AUTO_MAPPINGS_ENABLED = true;

    public string $markName;
    public string $modelName;
    public string $driverName;
}

/**
 * Тест 18
 * Заполнение Dto с автоматическим приведением стилей camelCase и snake_case
 */

final class Example18Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDto = CarDto::create([
            'mark_name' => 'Lexus',
            'model_name' => 'RX500',
            'driver.name' => 'Ivan',
        ]);

        $this->assertObjectHasProperty('markName', $carDto);
        $this->assertObjectHasProperty('modelName', $carDto);
        $this->assertObjectHasProperty('driverName', $carDto);
        $this->assertEquals('Lexus', $carDto->markName);
        $this->assertEquals('RX500', $carDto->modelName);
        $this->assertEquals('Ivan', $carDto->driverName);

        $carDtoArray = $carDto->toArray();

        $this->assertArrayHasKey('markName', $carDtoArray);
        $this->assertArrayHasKey('modelName', $carDtoArray);
        $this->assertArrayHasKey('driverName', $carDtoArray);
        $this->assertArrayNotHasKey('mark_name', $carDtoArray);
        $this->assertArrayNotHasKey('model_name', $carDtoArray);
        $this->assertArrayNotHasKey('driver.name', $carDtoArray);
    }
}
