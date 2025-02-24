<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example27;

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
 * Тест 27
 * Сериализация Dto в массив с использованием mappingKeys
 */

final class Example27Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carArray = CarDto::create()->mappingKeys(['markName' => 'mark_name'])->toArray();

        $this->assertArrayHasKey('mark_name', $carArray);
        $this->assertArrayHasKey('modelName', $carArray);
        $this->assertEquals('Lexus', $carArray['mark_name']);
        $this->assertEquals('RX500', $carArray['modelName']);
    }
}
