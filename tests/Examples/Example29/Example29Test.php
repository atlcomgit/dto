<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example29;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto extends \Atlcom\Dto
{
    public string $markName = 'Lexus';
    public string $modelName = 'RX500';
}

/**
 * Тест 29
 * Сериализация Dto в массив с использованием toArray
 */

final class Example29Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carArray = CarDto::create()->toArray();

        $this->assertArrayHasKey('markName', $carArray);
        $this->assertArrayHasKey('modelName', $carArray);
        $this->assertEquals('Lexus', $carArray['markName']);
        $this->assertEquals('RX500', $carArray['modelName']);
    }
}
