<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example26;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto extends \Atlcom\Dto
{
    public string $markName = 'Lexus';
    public string $modelName = 'RX500';
    public string $driverName = 'Ivan';

    protected function mappings(): array
    {
        return [
            'modelName' => 'model_name',
            'driverName' => ['driver.name', 'driver_name'],
        ];
    }
}

class ModelDto extends \Atlcom\Dto
{
    public string $modelName = 'RX500';
    public string $driverName = 'Ivan';

    protected function mappings(): array
    {
        return [
            'modelName' => 'model_name',
            'driverName' => ['driver.name', 'driver_name'],
        ];
    }


    protected function onSerializing(array &$array): void
    {
        $this->mappingKeys($this->mappings());
    }
}

class Entity
{
    public string $model_name;
}

/**
 * Тест 26
 * Сериализация Dto в массив с использованием excludeKeys
 */

final class Example26Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $a = CarDto::create();
        unset($a->markName);

        $carArray = CarDto::create()->excludeKeys(['modelName'])->toArray();

        $this->assertArrayHasKey('markName', $carArray);
        $this->assertArrayHasKey('driverName', $carArray);
        $this->assertArrayNotHasKey('mark_name', $carArray);
        $this->assertArrayNotHasKey('modelName', $carArray);
        $this->assertEquals('Lexus', $carArray['markName']);
        $this->assertEquals('Ivan', $carArray['driverName']);

        $modelArray = ModelDto::create()
            ->includeStyles()
            ->onlyKeys(['model_name', 'driver_name'])
            ->excludeKeys(['model_name'])
            ->toArray();

            $this->assertArrayNotHasKey('modelName', $modelArray);
            $this->assertArrayNotHasKey('model_name', $modelArray);
            $this->assertArrayNotHasKey('driver.name', $modelArray);
            $this->assertArrayHasKey('driver_name', $modelArray);

        $carArray = CarDto::create()->for(Entity::class)->toArray();

        $this->assertArrayNotHasKey('markName', $carArray);
        $this->assertArrayNotHasKey('mark_name', $carArray);
        $this->assertArrayNotHasKey('modelName', $carArray);
        $this->assertArrayNotHasKey('driverName', $carArray);
        $this->assertArrayNotHasKey('driver.name', $carArray);
        $this->assertArrayNotHasKey('driver_name', $carArray);
        $this->assertArrayHasKey('model_name', $carArray);
        $this->assertEquals('RX500', $carArray['model_name']);

        $modelArray = ModelDto::create()->toArray();

        $this->assertArrayNotHasKey('modelName', $modelArray);
        $this->assertArrayNotHasKey('driverName', $modelArray);
        $this->assertArrayHasKey('model_name', $modelArray);
        $this->assertArrayHasKey('driver.name', $modelArray);
        $this->assertArrayHasKey('driver_name', $modelArray);
        $this->assertEquals('RX500', $modelArray['model_name']);
        $this->assertEquals('Ivan', $modelArray['driver.name']);
        $this->assertEquals('Ivan', $modelArray['driver_name']);

        $carArray = CarDto::create()->includeStyles(true)->excludeKeys(['modelName'])->toArray();

        $this->assertArrayHasKey('markName', $carArray);
        $this->assertArrayHasKey('mark_name', $carArray);
        $this->assertArrayNotHasKey('modelName', $carArray);
        $this->assertArrayHasKey('model_name', $carArray);
        $this->assertArrayHasKey('driverName', $carArray);
        $this->assertArrayHasKey('driver_name', $carArray);

        $modelArray = ModelDto::create()->includeStyles(true)->excludeKeys(['modelName', 'driverName'])->toArray();

        $this->assertArrayNotHasKey('modelName', $modelArray);
        $this->assertArrayNotHasKey('model_name', $modelArray);
        $this->assertArrayNotHasKey('driverName', $modelArray);
        $this->assertArrayNotHasKey('driver.name', $modelArray);
        $this->assertArrayNotHasKey('driver_name', $modelArray);

        $modelArray = ModelDto::create()->includeStyles(true)->excludeKeys(['modelName'])->toArray();

        $this->assertArrayNotHasKey('modelName', $modelArray);
        $this->assertArrayNotHasKey('model_name', $modelArray);
        $this->assertArrayNotHasKey('driverName', $modelArray);
        $this->assertArrayHasKey('driver.name', $modelArray);
        $this->assertArrayHasKey('driver_name', $modelArray);
    }
}
