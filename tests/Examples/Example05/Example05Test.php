<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example05;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto extends \Atlcom\Dto
{
    public string $markName = 'Lexus';
    public string $modelName;

    protected function defaults(): array
    {
        return [
            'modelName' => 'RX500',
        ];
    }
}

/**
 * Тест 05
 * Заполнение Dto с параметрами по умолчанию
 */

final class Example05Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDto = CarDto::create();

        $this->assertObjectHasProperty('markName', $carDto);
        $this->assertObjectHasProperty('modelName', $carDto);
        $this->assertEquals('Lexus', $carDto->markName);
        $this->assertEquals('RX500', $carDto->modelName);
    }
}