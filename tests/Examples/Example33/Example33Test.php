<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example33;

use Exception;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto extends \Atlcom\Dto
{
    public string $markName;
    public string $modelName;

    protected function exceptions(string $messageCode, array $messageItems): string
    {
        return 'Текст ошибки';
    }
}

/**
 * Тест 33
 * Заполнение Dto с событием onAssigned (после изменения)
 */

final class Example33Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        try {
            $expectMessage = 'Текст ошибки';
            $carDto = CarDto::create();
        } catch (Exception $e) {
            $actualMessage = $e->getMessage();
        }

        $this->assertEquals($expectMessage, $actualMessage);
    }
}