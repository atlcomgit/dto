<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example18;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto extends \Atlcom\Dto
{
    public const AUTO_MAPPINGS_ENABLED = true;

    public string $markName;
    public string $modelName;
}

/**
 * Тест 18
 * Заполнение Dto с автоматическим приведением стилей camelCase и snake_case
 */

final class Example18Test extends TestCase
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
