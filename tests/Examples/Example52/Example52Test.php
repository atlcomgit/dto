<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example52;

use Atlcom\Exceptions\DtoException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto1 extends \Atlcom\Dto
{
    public const INTERFACE_JSON_SERIALIZABLE_ENABLED = true;

    public string $markName = 'Lexus';
    public string $modelName = 'RX500';
}

class CarDto2 extends \Atlcom\Dto
{
    public const INTERFACE_JSON_SERIALIZABLE_ENABLED = false;

    public string $markName = 'Lexus';
    public string $modelName = 'RX500';
}

/**
 * Тест 52
 * Включение константы INTERFACE_JSON_SERIALIZABLE_ENABLED
 */

final class Example52Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDto1 = CarDto1::create();

        $this->assertTrue(json_encode($carDto1) === '{"markName":"Lexus","modelName":"RX500"}');

        $this->expectException(DtoException::class);
        $carDto2 = CarDto2::create();

        json_encode($carDto2);
    }
}
