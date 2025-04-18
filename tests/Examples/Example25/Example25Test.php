<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example25;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto extends \Atlcom\Dto
{
    public string $markName = 'Lexus';
}

/**
 * Тест 25
 * Сериализация Dto в массив с использованием includeArray
 */

final class Example25Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carArray = CarDto::create()->includeArray(['modelName' => 'RX500'])->toArray();

        $this->assertArrayHasKey('markName', $carArray);
        $this->assertArrayHasKey('modelName', $carArray);
        $this->assertEquals('Lexus', $carArray['markName']);
        $this->assertEquals('RX500', $carArray['modelName']);
    }
}
