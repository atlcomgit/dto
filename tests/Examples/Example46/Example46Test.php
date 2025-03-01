<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example46;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto1 extends \Atlcom\Dto
{
    public string $markName;
    public CarDto2 $carDto2;
    public CarDto2 $carDto3;

    protected function casts(): array
    {
        return [
            'carDto3' => CarDto2::class,
        ];
    }
}
 
class CarDto2 extends \Atlcom\Dto
{
    public string $markName;

    protected function onCreated(mixed $data): void
    {
        $this->markName = 'Toyota';
    }
}
 
/**
 * Тест 46
 * Создание Dto внутри Dto
 */

final class Example46Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDto1 = CarDto1::create(
            markName: 'Lexus',
            carDto2: CarDto2::create(markName: 'Lexus'),
            carDto3: ['markName' => 'Lexus'],
        );

        $this->assertObjectHasProperty('markName', $carDto1);
        $this->assertEquals('Lexus', $carDto1->markName);
        $this->assertObjectHasProperty('markName', $carDto1->carDto2);
        $this->assertEquals('Toyota', $carDto1->carDto2->markName);
        $this->assertObjectHasProperty('markName', $carDto1->carDto3);
        $this->assertEquals('Toyota', $carDto1->carDto3->markName);
    }
}