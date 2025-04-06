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
    public const INTERFACE_COUNTABLE_ENABLED = true;
    public const AUTO_DYNAMIC_PROPERTIES_ENABLED = true;
}

class CarDto3 extends \Atlcom\Dto
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
        // enabled

        $carDto1 = CarDto1::create();

        $this->assertTrue($carDto1->count() === 2);

        // enabled with dynamic properties

        $carDto2 = CarDto2::create();

        $carDto2->markName = 'Lexus';
        $carDto2->modelName = 'RX500';

        $this->assertTrue($carDto2->count() === 2);

        // disabled

        $this->expectException(DtoException::class);

        $carDto3 = CarDto3::create();

        $carDto3->count();
    }
}