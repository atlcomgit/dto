<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example42;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto extends \Atlcom\Dto
{
    private ?int $id;
}

/**
 * Тест 42
 * Работа с getCustomOption, setCustomOption
 */

final class Example42Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDto = CarDto::create()->setCustomOption('test', 1);

        $this->assertEquals(1, $carDto->getCustomOption('test'));

   }
}