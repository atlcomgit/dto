<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example60;

use Atlcom\Exceptions\DtoException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto extends \Atlcom\Dto
{
    public const AUTO_DYNAMIC_PROPERTIES_ENABLED = false;
}

/**
 * Тест 60
 * Работа с константами dto
 */

final class Example60Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDto = CarDto::create();

        $this->assertSame(false, $carDto->consts('AUTO_DYNAMIC_PROPERTIES_ENABLED'));

        $carDto->consts('AUTO_DYNAMIC_PROPERTIES_ENABLED', true);

        $this->assertSame(true, $carDto->consts('AUTO_DYNAMIC_PROPERTIES_ENABLED'));

        $carDto->markName = 'Lexus';

        $this->assertObjectNotHasProperty('markName', $carDto);
        $this->assertSame('Lexus', $carDto->markName);

        $carArray = $carDto->toArray();

        $this->assertArrayHasKey('markName', $carArray);
        $this->assertSame('Lexus', $carArray['markName']);
    }
}
