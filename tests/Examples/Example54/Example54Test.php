<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example54;

use Atlcom\Exceptions\DtoException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto1 extends \Atlcom\Dto
{
    public const INTERFACE_STRINGABLE_ENABLED = true;

    public string $markName = 'Lexus';
    public string $modelName = 'RX500';
}

class CarDto2 extends \Atlcom\Dto
{
    public const INTERFACE_STRINGABLE_ENABLED = false;

    public string $markName = 'Lexus';
    public string $modelName = 'RX500';
}

/**
 * Тест 54
 * Включение константы INTERFACE_STRINGABLE_ENABLED
 */

final class Example54Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        // enabled

        $carDto1 = CarDto1::create();

        $this->assertEquals(
            (string)$carDto1,
            '{"markName":"Lexus","modelName":"RX500"}',
        );

        // disabled

        $this->expectException(DtoException::class);

        $carDto2 = CarDto2::create();

        (string)$carDto2;
    }
}