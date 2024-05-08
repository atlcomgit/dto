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
}

class CarSecondDto extends \Atlcom\Dto
{
    public string $markName;
    public string $modelName;
    public int $year;
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
    }
}
