<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example17;

use Atlcom\Exceptions\DtoException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto extends \Atlcom\Dto
{
    public string $markName;
    public string $modelName;

    protected function onException(\Throwable $exception): void
    {
        // Сохраняем $exception в лог
        throw $exception;
    }
}

/**
 * Тест 17
 * Заполнение Dto с событием onException (при исключении)
 */

final class Example17Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $this->expectException(DtoException::class);
        $carDto = CarDto::create();
    }
}
