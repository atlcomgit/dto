<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example51;

use Atlcom\Exceptions\DtoException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto1 extends \Atlcom\Dto
{
    public const INTERFACE_ITERATOR_AGGREGATE_ENABLED = true;

    public string $markName = 'Lexus';
    public string $modelName = 'RX500';
}

class CarDto2 extends \Atlcom\Dto
{
    public const INTERFACE_ITERATOR_AGGREGATE_ENABLED = false;

    public string $markName = 'Lexus';
    public string $modelName = 'RX500';
}

/**
 * Тест 51
 * Включение константы INTERFACE_ITERATOR_AGGREGATE_ENABLED
 */

final class Example51Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDto1 = CarDto1::create();

        foreach ($carDto1 as $key => $value) {
            $this->assertTrue($carDto1->$key === $value);
        }

        $this->expectException(DtoException::class);
        $carDto2 = CarDto2::create();

        foreach ($carDto2 as $key => $value) {
        }
    }
}