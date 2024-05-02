<?php

namespace Atlcom\Tests\Examples\Example26;

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
 * Тест 26
 * Сериализация Dto в массив с использованием excludeKeys
 */

final class Example26Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carArray = CarDto::create()->excludeKeys(['modelName'])->toArray();

        $this->assertArrayHasKey('markName', $carArray);
        $this->assertArrayNotHasKey('modelName', $carArray);
        $this->assertEquals('Lexus', $carArray['markName']);
    }
}
