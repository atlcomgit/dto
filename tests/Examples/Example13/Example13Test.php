<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example13;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto extends \Atlcom\Dto
{
    public string $markName;
    public ?string $modelName;

    protected function onMerging(array &$array): void
    {
        $array['modelName'] = 'RX500';
    }
}

/**
 * Тест 13
 * Заполнение Dto с событием onMerging (перед объединением)
 */

final class Example13Test extends TestCase
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
