<?php

declare(strict_types=1);

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

class CarDto2 extends \Atlcom\Dto
{
    public string $markName;

    protected function mappings(): array
    {
        return [
            'markName' => 'mark_name',
        ];
    }

    protected function onSerializing(array &$array): void
    {
        $this->mappingKeys(['markName' => 'mark_name_new']);
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

        $carDto2 = CarDto2::create([
            'mark_name' => 'Lexus',
        ]);

        $carArray2 = $carDto2->toArray();

        $this->assertArrayHasKey('mark_name_new', $carArray2);
        $this->assertArrayNotHasKey('mark_name', $carArray2);
        $this->assertArrayNotHasKey('markName', $carArray2);
        $this->assertEquals('Lexus', $carArray2['mark_name_new']);
    }
}
