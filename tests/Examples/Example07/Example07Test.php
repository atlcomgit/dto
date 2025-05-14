<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example07;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto extends \Atlcom\Dto
{
    public string $markName;
    public string $modelName;
    public string $driverName;
    public string $passengerName;

    protected function mappings(): array
    {
        return [
            'markName' => 'mark.name',
            'modelName' => ['model.name', 'model_name'],
            'driverName' => ['driver.name', 'driver_name'],
            'passengerName' => 'passenger.name',
        ];
    }
}

/**
 * Тест 07
 * Заполнение Dto с маппингом из многоуровневого массива
 */

final class Example07Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDto = CarDto::create([
            'mark' => ['name' => 'Lexus'],
            'model' => ['name' => 'RX500'],
            'driver_name' => 'Ivan',
            'driver' => ['name' => 'Alek'],
            'passenger.name' => 'Julia',
        ]);

        $this->assertObjectHasProperty('markName', $carDto);
        $this->assertObjectHasProperty('modelName', $carDto);
        $this->assertObjectHasProperty('driverName', $carDto);
        $this->assertObjectHasProperty('passengerName', $carDto);
        $this->assertEquals('Lexus', $carDto->markName);
        $this->assertEquals('RX500', $carDto->modelName);
        $this->assertEquals('Ivan', $carDto->driverName);
        $this->assertEquals('Julia', $carDto->passengerName);

        $carDto->driver_name = 'Peter';

        $this->assertEquals('Peter', $carDto->driver_name);
    }
}
