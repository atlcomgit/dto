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

    protected function mappings(): array
    {
        return [
            'markName' => 'mark_name',
            'modelName' => 'model_name',
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
        ]);

        $this->assertObjectHasProperty('markName', $carDto);
        $this->assertObjectHasProperty('modelName', $carDto);
        $this->assertEquals('Lexus', $carDto->markName);
        $this->assertEquals('RX500', $carDto->modelName);
    }
}
