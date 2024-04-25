<?php

namespace Expo\Dto\Tests\Examples\Example07;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto extends \Expo\Dto\DefaultDto
{
    public string $markName;
    public string $modelName;

    protected function mappings(): array
    {
        return [
            'markName' => 'mark.name',
            'modelName' => 'model.name',
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
            'mark' => [
                'name' => 'Lexus',
            ],
            'model' => [
                'name' => 'RX500',
            ],
        ]);

        $this->assertObjectHasProperty('markName', $carDto);
        $this->assertObjectHasProperty('modelName', $carDto);
        $this->assertEquals('Lexus', $carDto->markName);
        $this->assertEquals('RX500', $carDto->modelName);
    }
}
