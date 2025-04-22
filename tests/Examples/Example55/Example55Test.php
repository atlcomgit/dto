<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example55;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto extends \Atlcom\Dto
{
    public const AUTO_DYNAMIC_PROPERTIES_ENABLED = true;

    public string $markName;
}

/**
 * Тест 55
 * Работа с магическим методом __debugInfo (обращение к Dto через методы print_r и var_dump)
 */

final class Example55Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDto = CarDto::create(
            markName: 'Lexus',
            modelName: 'RX500',
        )
            ->setCustomOption('price1', '1000000');
        $carDto->price2 = 2000000;
        
        $this->assertTrue($carDto->markName === 'Lexus');
        $this->assertTrue($carDto->modelName === 'RX500');
        $this->assertTrue($carDto->price1 === '1000000');
        $this->assertTrue($carDto->price2 === 2000000);

        $carDtoDump = print_r($carDto, true);

        $this->assertStringContainsString('[markName] => Lexus', $carDtoDump);
        $this->assertStringContainsString('[modelName] => RX500', $carDtoDump);
        $this->assertStringContainsString('[price1] => 1000000', $carDtoDump);
        $this->assertStringContainsString('[price2] => 2000000', $carDtoDump);
    }
}