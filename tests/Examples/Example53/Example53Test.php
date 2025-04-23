<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example53;

use Atlcom\Exceptions\DtoException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto1 extends \Atlcom\Dto
{
    public const INTERFACE_SERIALIZABLE_ENABLED = true;

    public string $markName = '';
    public string $modelName = '';
}

class CarDto2 extends \Atlcom\Dto
{
    public const INTERFACE_SERIALIZABLE_ENABLED = true;
    public const AUTO_DYNAMIC_PROPERTIES_ENABLED = true;
}

class CarDto3 extends \Atlcom\Dto
{
    public const INTERFACE_SERIALIZABLE_ENABLED = false;

    public string $markName = 'Lexus';
    public string $modelName = 'RX500';
}

/**
 * Тест 53
 * Включение константы INTERFACE_SERIALIZABLE_ENABLED
 */

final class Example53Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        // enabled

        $carDto1 = CarDto1::create(markName: 'Lexus', modelName: 'RX500');

        $carDto1Serialize = serialize($carDto1);

        $this->assertEquals(
            $carDto1Serialize,
            'O:39:"Atlcom\Tests\Examples\Example53\CarDto1":2:{s:8:"markName";s:5:"Lexus";s:9:"modelName";s:5:"RX500";}',
        );

        $carDto1Unserialize = unserialize($carDto1Serialize);
        
        $this->assertInstanceOf(CarDto1::class, $carDto1Unserialize);
        $this->assertTrue($carDto1Unserialize->markName === 'Lexus');
        $this->assertTrue($carDto1Unserialize->modelName === 'RX500');

        // enabled with dynamic properties

        $carDto2 = CarDto2::create(markName: 'Lexus', modelName: 'RX500');

        $carDto2Serialize = serialize($carDto2);

        $this->assertEquals(
            $carDto2Serialize,
            'O:39:"Atlcom\Tests\Examples\Example53\CarDto2":2:{s:8:"markName";s:5:"Lexus";s:9:"modelName";s:5:"RX500";}',
        );

        $carDto2Unserialize = unserialize($carDto2Serialize);
        
        $this->assertInstanceOf(CarDto2::class, $carDto2Unserialize);
        $this->assertTrue($carDto2Unserialize->markName === 'Lexus');
        $this->assertTrue($carDto2Unserialize->modelName === 'RX500');

        // disabled

        $this->expectException(DtoException::class);

        $carDto3 = CarDto3::create();

        serialize($carDto3);
    }
}