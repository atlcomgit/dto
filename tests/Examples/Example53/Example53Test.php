<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example53;

use Atlcom\Exceptions\DtoException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto1 extends \Atlcom\Dto
{
    public const INTERFACE_SERIALIZABLE_ENABLED = true;

    public string $markName = 'Lexus';
    public string $modelName = 'RX500';
}

class CarDto2 extends \Atlcom\Dto
{
    public const INTERFACE_SERIALIZABLE_ENABLED = false;

    public string $markName = 'Lexus';
    public string $modelName = 'RX500';
}

/**
 * Тест 53
 * Включение константы INTERFACE_SERIALIZABLE_ENABLED
 */

final class Example53Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        // enabled

        $carDto1 = CarDto1::create();

        $this->assertEquals(
            serialize($carDto1),
            'C:39:"Atlcom\Tests\Examples\Example53\CarDto1":61:{a:2:{s:8:"markName";s:5:"Lexus";s:9:"modelName";s:5:"RX500";}}',
        );

        // disabled

        $this->expectException(DtoException::class);

        $carDto2 = CarDto2::create();

        serialize($carDto2);
    }
}