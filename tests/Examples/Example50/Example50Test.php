<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example50;

use Atlcom\Exceptions\DtoException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto1 extends \Atlcom\Dto
{
    public const INTERFACE_COUNTABLE_ENABLED = true;

    public string $markName = 'Lexus';
    public string $modelName = 'RX500';
}

class CarDto2 extends \Atlcom\Dto
{
    public const INTERFACE_COUNTABLE_ENABLED = false;

    public string $markName = 'Lexus';
    public string $modelName = 'RX500';
}

/**
 * Тест 50
 * Включение константы INTERFACE_COUNTABLE_ENABLED
 */

final class Example50Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDto1 = CarDto1::create();

        $this->assertTrue($carDto1->count() === 2);

        $this->expectException(DtoException::class);
        $carDto2 = CarDto2::create();

        $carDto2->count();
    }
}