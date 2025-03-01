<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example47;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto1 extends \Atlcom\Dto
{
    public const AUTO_CASTS_OBJECTS_ENABLED = true;
    
    public string $markName;
    public CarDto2 $carDto2;
}
 
class CarDto2 extends \Atlcom\Dto
{
    public string $markName;
}
 
/**
 * Тест 47
 * Создание Dto с включенной опцией AUTO_CASTS_OBJECTS_ENABLED для авто приведения объектов при заполнении dto
 */

final class Example47Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDto1 = CarDto1::create(
            markName: 'Lexus',
            carDto2: ['markName' => 'Lexus'],
        );

        $this->assertObjectHasProperty('markName', $carDto1);
        $this->assertEquals('Lexus', $carDto1->markName);
        $this->assertObjectHasProperty('markName', $carDto1->carDto2);
        $this->assertEquals('Lexus', $carDto1->carDto2->markName);
    }
}