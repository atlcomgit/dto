<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example30;

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
 * Тест 30
 * Сериализация Dto в массив с использованием toArray
 */

final class Example30Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carJson = CarDto::create()->toJson();

        $this->assertJson($carJson);
        $this->assertJsonStringEqualsJsonString('{"markName": "Lexus", "modelName": "RX500"}', $carJson);

        $carJson = CarDto::create()->toJson(0);

        $this->assertJson($carJson);

        $carJson = CarDto::create()->toJson('0');

        $this->assertJson($carJson);
    }
}
