<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example48;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto1 extends \Atlcom\Dto
{
    public string $markName;
    public string $modelName;
    public CarDto2 $carDto2;


    public function defaults(): array
    {
        return [
            'modelName' => 'RX350',
        ];
    }
}
 
class CarDto2 extends \Atlcom\Dto
{
    public string $markName = 'Toyota';
}
 
/**
 * Тест 48
 * Очистка свойств dto
 */

final class Example48Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDto1 = CarDto1::create(
            markName: 'Lexus',
            carDto2: CarDto2::create(['markName' => 'Lexus']),
        );

        $this->assertObjectHasProperty('markName', $carDto1);
        $this->assertEquals('Lexus', $carDto1->markName);
        $this->assertObjectHasProperty('markName', $carDto1->carDto2);
        $this->assertEquals('Lexus', $carDto1->carDto2->markName);

        $carDto1->clear();

        $this->assertEquals('', $carDto1->markName);
        $this->assertEquals('RX350', $carDto1->modelName);
        $this->assertEquals('Toyota', $carDto1->carDto2->markName);
    }
}