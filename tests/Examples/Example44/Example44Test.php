<?php

namespace Atlcom\Tests\Examples\Example44;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto1 extends \Atlcom\Dto
{
    public string $markName;

    protected function onCreating(mixed &$data): void
    {
        $data = ['markName' => 'Toyota'];
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
 * Тест 44
 * Создание Dto с событием onCreating и onCreated
 */

final class Example44Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDto1 = new CarDto1('Lexus');

        $this->assertObjectHasProperty('markName', $carDto1);
        $this->assertEquals('Toyota', $carDto1->markName);

        $carDto1 = new CarDto1(['markName' => 'Lexus']);

        $this->assertObjectHasProperty('markName', $carDto1);
        $this->assertEquals('Toyota', $carDto1->markName);

        $carDto1 = CarDto1::create(markName: 'Lexus');

        $this->assertObjectHasProperty('markName', $carDto1);
        $this->assertEquals('Toyota', $carDto1->markName);

        $carDto1 = CarDto1::create('Lexus');

        $this->assertObjectHasProperty('markName', $carDto1);
        $this->assertEquals('Toyota', $carDto1->markName);

        $carDto2 = CarDto2::create(markName: 'Lexus');

        $this->assertObjectHasProperty('markName', $carDto2);
        $this->assertEquals('Toyota', $carDto2->markName);
    }
}
