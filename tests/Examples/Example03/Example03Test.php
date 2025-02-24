<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example03;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto extends \Atlcom\Dto
{
    public string $markName;
    public string $modelName;
}

/**
 * Тест 03
 * Заполнение Dto из объекта
 */

final class Example03Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDto1 = CarDto::create([
            'markName' => 'Lexus',
            'modelName' => 'RX500',
        ]);

        $carDto2 = CarDto::create($carDto1);

        $this->assertObjectHasProperty('markName', $carDto2);
        $this->assertObjectHasProperty('modelName', $carDto2);
        $this->assertEquals('Lexus', $carDto2->markName);
        $this->assertEquals('RX500', $carDto2->modelName);
    }
}
