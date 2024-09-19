<?php

namespace Atlcom\Tests\Examples\Example06;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto extends \Atlcom\Dto
{
    public string $markName;
    public string $modelName;
    public int $year;

    protected function mappings(): array
    {
        return [
            'markName' => 'mark_name',
            'modelName' => 'model_name',
            'year' => 'params.year',
        ];
    }

    protected function casts(): array
    {
        return [
            'year' => 'integer',
        ];
    }
}

/**
 * Тест 06
 * Заполнение Dto с маппингом свойств из snake_case в camelCase
 */

final class Example06Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDto = CarDto::create([
            'mark_name' => 'Lexus',
            'model_name' => 'RX500',
            'params' => [
                'year' => '2024',
            ],
        ]);

        $this->assertObjectHasProperty('markName', $carDto);
        $this->assertObjectHasProperty('modelName', $carDto);
        $this->assertObjectHasProperty('year', $carDto);
        $this->assertEquals('Lexus', $carDto->markName);
        $this->assertEquals('RX500', $carDto->modelName);
        $this->assertTrue($carDto->year === 2024);
    }
}
