<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example61;

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
    public string $modelName = 'RX500';
}

/**
 * Тест 61
 * Работа со скрытием свойств из dto
 */

final class Example61Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDto = CarDto::create();

        $this->assertObjectHasProperty('markName', $carDto);
        $this->assertObjectHasProperty('modelName', $carDto);

        $this->assertSame('Lexus', $carDto->markName);
        $this->assertSame('RX500', $carDto->modelName);

        $carDto->hideProperties(['modelName']);

        $this->assertObjectHasProperty('markName', $carDto);
        $this->assertObjectHasProperty('modelName', $carDto);

        $this->assertSame('Lexus', $carDto->markName);
        $this->assertFalse(isset($carDto->modelName));

        $carArray = $carDto->toArray();
        
        $this->assertArrayHasKey('markName', $carArray);
        $this->assertArrayHasKey('modelName', $carArray);

        $this->assertSame('Lexus', $carArray['markName']);
        $this->assertSame('RX500', $carArray['modelName']);

        $this->expectException(DtoException::class);
        $this->assertSame(null, $carDto->modelName);

        $carDto->modelName = 'LX200';

        $this->assertSame('LX200', $carDto->modelName);

        $carArray = $carDto->toArray();

        $this->assertSame('RX500', $carArray['markName']);

        $carDto->clear();
        $carDto->modelName = 'LX200';

        $this->assertSame('Lexus', $carDto->markName);
        $this->assertSame('LX200', $carDto->modelName);

        $carArray = $carDto->toArray();

        $this->assertSame('Lexus', $carArray['markName']);
        $this->assertSame('LX200', $carArray['markName']);
    }
}
