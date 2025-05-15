<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example59;

use Atlcom\Exceptions\DtoException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto extends \Atlcom\Dto
{
    public const AUTO_DYNAMIC_PROPERTIES_ENABLED = true;

    public string $markName = 'Lexus';
}

/**
 * Тест 59
 * Работа с удалением свойств из dto
 */

final class Example59Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDto = CarDto::create(modelName: 'RX500');

        $this->assertObjectHasProperty('markName', $carDto);
        $this->assertObjectNotHasProperty('modelName', $carDto);

        $this->assertSame('Lexus', $carDto->markName);
        $this->assertSame('RX500', $carDto->modelName);

        $carDto = $carDto->removeProperties(['markName', 'modelName']);

        $this->assertObjectHasProperty('markName', $carDto);
        $this->assertObjectNotHasProperty('modelName', $carDto);

        $carArray = $carDto->toArray();
        
        $this->assertArrayNotHasKey('markName', $carArray);
        $this->assertArrayNotHasKey('modelName', $carArray);

        $this->assertSame(null, $carDto->modelName);

        $this->expectException(DtoException::class);
        $this->assertSame(null, $carDto->markName);
    }
}
