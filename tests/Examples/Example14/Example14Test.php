<?php

namespace Expo\Dto\Tests\Examples\Example14;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto extends \Expo\Dto\DefaultDto
{
    public string $markName;
    public ?string $modelName;

    protected function onMerged(array $array): void
    {
        $this->modelName = 'RX500';
    }
}

/**
 * Тест 14
 * Заполнение Dto с событием onMerged (после объединения)
 */

final class Example14Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDto = CarDto::create([
            'markName' => 'Lexus',
        ]);

        $this->assertObjectHasProperty('markName', $carDto);
        $this->assertObjectHasProperty('modelName', $carDto);
        $this->assertEquals('Lexus', $carDto->markName);
        $this->assertEquals(null, $carDto->modelName);

        $carDto->merge([
            'modelName' => 'Unknown',
        ]);

        $this->assertObjectHasProperty('markName', $carDto);
        $this->assertObjectHasProperty('modelName', $carDto);
        $this->assertEquals('Lexus', $carDto->markName);
        $this->assertEquals('RX500', $carDto->modelName);
    }
}
