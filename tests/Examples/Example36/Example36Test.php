<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example36;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto extends \Atlcom\Dto
{
    public string $markName = 'Lexus';
    public string $modelName = 'RX500';

    protected function onSerializing(array &$array): void
    {
        $this->onlyKeys('markName');
    }
}

/**
 * Тест 36
 * Сериализация Dto в массив с использованием withoutOptions
 */

final class Example36Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDto = CarDto::create();

        $this->assertObjectHasProperty('markName', $carDto);
        $this->assertObjectHasProperty('modelName', $carDto);

        $carArray = $carDto->toArray();

        $this->assertArrayHasKey('markName', $carArray);
        $this->assertArrayNotHasKey('modelName', $carArray);

        $carArray = $carDto->withoutOptions()->toArray();

        $this->assertArrayHasKey('markName', $carArray);
        $this->assertArrayHasKey('modelName', $carArray);
    }
}
