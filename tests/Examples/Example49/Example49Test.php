<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example49;

use Atlcom\Exceptions\DtoException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto1 extends \Atlcom\Dto
{
    public const INTERFACE_ARRAY_ACCESS_ENABLED = true;

    public ?string $markName = 'Lexus';
    public string $modelName = 'RX500';
}

class CarDto2 extends \Atlcom\Dto
{
    public const INTERFACE_ARRAY_ACCESS_ENABLED = true;
    public const AUTO_DYNAMIC_PROPERTIES_ENABLED = true;
}

class CarDto3 extends \Atlcom\Dto
{
    public const INTERFACE_ARRAY_ACCESS_ENABLED = false;

    public string $markName = 'Lexus';
    public string $modelName = 'RX500';
}

/**
 * Тест 49
 * Включение константы INTERFACE_ARRAY_ACCESS_ENABLED
 */

final class Example49Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        // enabled

        $carDto1 = CarDto1::create();

        $this->assertTrue(isset($carDto1['markName']) === true);
        $this->assertTrue(isset($carDto1['test']) === false);
        $this->assertArrayHasKey('markName', $carDto1);
        $this->assertTrue($carDto1['markName'] === 'Lexus');
        $this->assertArrayHasKey('modelName', $carDto1);
        $this->assertTrue($carDto1['modelName'] === 'RX500');

        $carDto1['markName'] = 'Toyota';
        $this->assertTrue($carDto1->markName === 'Toyota');
        $this->assertTrue($carDto1['markName'] === 'Toyota');

        unset($carDto1['markName']);
        $this->assertTrue($carDto1->markName === null);

        // enabled with dynamic properties

        $carDto2 = CarDto2::create();

        $this->assertTrue(isset($carDto2['markName']) === false);
        $this->assertTrue(isset($carDto2['modelName']) === false);

        $carDto2['markName'] = 'Toyota';
        $carDto2->modelName = 'Allion';

        $this->assertTrue(isset($carDto2['markName']) === true);
        $this->assertTrue(isset($carDto2['modelName']) === true);
        $this->assertObjectNotHasProperty('markName', $carDto2);
        $this->assertObjectNotHasProperty('modelName', $carDto2);
        $this->assertArrayHasKey('markName', $carDto2);
        $this->assertArrayHasKey('modelName', $carDto2);
        $this->assertTrue($carDto2->markName === 'Toyota');
        $this->assertTrue($carDto2->modelName === 'Allion');
        $this->assertTrue($carDto2['markName'] === 'Toyota');
        $this->assertTrue($carDto2['modelName'] === 'Allion');

        // disabled

        $this->expectException(DtoException::class);

        $carDto3 = CarDto3::create();

        $carDto3['modelName'] = 'Toyota';
        $this->assertTrue($carDto3->markName === 'Lexus');
        $this->assertTrue($carDto3['markName'] === 'Lexus');
    }
}