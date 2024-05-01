<?php

namespace Atlcom\Dto\Tests\Examples\Example17;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto extends \Atlcom\Dto\DefaultDto
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
        try {
            $carDto = CarDto::create();
            $this->fail('Обработка исключений отключена');
        } catch (\Throwable $exception) {
            $this->assertTrue(true);
        }
    }
}
