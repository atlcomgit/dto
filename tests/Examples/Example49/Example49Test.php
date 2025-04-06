<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example49;

use Atlcom\Exceptions\DtoException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto1 extends \Atlcom\Dto
{
    public const INTERFACE_ARRAY_ACCESS_ENABLED = true;

    public string $markName = 'Lexus';
    public string $modelName = 'RX500';
}

class CarDto2 extends \Atlcom\Dto
{
    public const INTERFACE_ARRAY_ACCESS_ENABLED = false;

    public string $markName = 'Lexus';
    public string $modelName = 'RX500';
}

/**
 * Тест 49
 * Включение константы INTERFACE_ARRAY_ACCESS_ENABLED
 */

final class Example49Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDto1 = CarDto1::create();

        $this->assertArrayHasKey('markName', $carDto1);
        $this->assertTrue($carDto1['markName'] === 'Lexus');
        $this->assertArrayHasKey('modelName', $carDto1);
        $this->assertTrue($carDto1['modelName'] === 'RX500');

        $this->expectException(DtoException::class);
        $carDto2 = CarDto2::create();

        $carDto2['modelName'];
    }
}
