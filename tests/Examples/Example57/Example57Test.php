<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example57;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto extends \Atlcom\Dto
{
    public const AUTO_DYNAMIC_PROPERTIES_ENABLED = true;

    public string $markName;
    public ?string $modelName;

    protected function onCreating(mixed &$data): void
    {
        $data['markName'] = 'Toyota';
        $data['price'] += 1000000;
    }
}

/**
 * Тест 57
 * Работа с клонированием dto
 */

final class Example57Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDto = CarDto::create(markName: 'Lexus', modelName: 'RX500', price: 1000000);

        $this->assertSame($carDto->markName, 'Toyota');
        $this->assertSame($carDto->modelName, 'RX500');
        $this->assertSame($carDto->price, 2000000);

        $carDto->markName = 'Nissan';
        $carDtoClone = $carDto->clone();

        $this->assertSame($carDtoClone->markName, 'Nissan');
        $this->assertSame($carDtoClone->modelName, 'RX500');
        $this->assertSame($carDtoClone->price, 2000000);

        $carDto->markName = 'Mercedes';
        $carDtoClone = clone $carDto;

        $this->assertSame($carDtoClone->markName, 'Mercedes');
        $this->assertSame($carDtoClone->modelName, 'RX500');
        $this->assertSame($carDtoClone->price, null);
    }
}