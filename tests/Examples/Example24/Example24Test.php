<?php

namespace Atlcom\Tests\Examples\Example24;

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
 * Тест 24
 * Сериализация Dto в массив с использованием includeStyles
 */

final class Example24Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carArray = CarDto::create()->includeStyles()->toArray();

        $this->assertArrayHasKey('markName', $carArray);
        $this->assertArrayHasKey('modelName', $carArray);
        $this->assertArrayHasKey('mark_name', $carArray);
        $this->assertArrayHasKey('model_name', $carArray);
        $this->assertEquals('Lexus', $carArray['markName']);
        $this->assertEquals('RX500', $carArray['modelName']);
        $this->assertEquals('Lexus', $carArray['mark_name']);
        $this->assertEquals('RX500', $carArray['model_name']);
    }
}
