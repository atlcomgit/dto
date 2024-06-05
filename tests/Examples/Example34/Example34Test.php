<?php

namespace Atlcom\Tests\Examples\Example34;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarFirstDto extends \Atlcom\Dto
{
    public string $markName;
    public string $modelName;

    protected function mappings(): array
    {
        return [
            'modelName' => 'model_name',
        ];
    }
}

class CarSecondDto extends \Atlcom\Dto
{
    public string $markName;
    public string $modelName;
    public int $year;
}

class CarThirdDto extends \Atlcom\Dto
{
    public string $markName;
    public string $modelName;
    public int $year;

    protected function mappings(): array
    {
        return [
            'markName' => 'mark_name',
        ];
    }

    protected function onSerializing(array &$array): void
    {
        $this->onlyKeys(['year']);
    }
}

/**
 * Тест 34
 * Трансформация Dto в другое Dto с дополнением данными из массива
 */

final class Example34Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carFirstDto = CarFirstDto::create([
            'markName' => 'Lexus',
            'modelName' => 'RX500',
        ]);

        $carSecondDto = $carFirstDto->transformToDto(CarSecondDto::class, ['year' => 2024]);
        
        $this->assertObjectHasProperty('markName', $carSecondDto);
        $this->assertObjectHasProperty('modelName', $carSecondDto);
        $this->assertObjectHasProperty('year', $carSecondDto);
        $this->assertEquals('Lexus', $carSecondDto->markName);
        $this->assertEquals('RX500', $carSecondDto->modelName);
        $this->assertEquals(2024, $carSecondDto->year);

        $carThirdDto = CarThirdDto::create([
            'markName' => 'Lexus',
            'modelName' => 'RX500',
            'year' => 2024,
        ]);

        $carFirstDto = $carThirdDto->transformToDto(CarFirstDto::class);

        $this->assertObjectHasProperty('markName', $carFirstDto);
        $this->assertObjectHasProperty('modelName', $carFirstDto);
        $this->assertEquals('Lexus', $carFirstDto->markName);
        $this->assertEquals('RX500', $carFirstDto->modelName);

        $carFirstArray = $carFirstDto->toArray();
        $this->assertArrayHasKey('markName', $carFirstArray);
        $this->assertArrayHasKey('modelName', $carFirstArray);
    }
}
