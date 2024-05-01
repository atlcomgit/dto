<?php

namespace Atlcom\Dto\Tests\Examples\Example23;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto extends \Atlcom\Dto\DefaultDto
{
    public string $markName = 'Lexus';
    public string $modelName = 'RX500';
}

/**
 * Тест 23
 * Сериализация Dto в массив с использованием onlyKeys
 */

final class Example23Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carArray = CarDto::create()->onlyKeys(['markName'])->toArray();

        $this->assertArrayHasKey('markName', $carArray);
        $this->assertArrayNotHasKey('modelName', $carArray);
        $this->assertEquals('Lexus', $carArray['markName']);
    }
}
