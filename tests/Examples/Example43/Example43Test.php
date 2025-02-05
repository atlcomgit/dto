<?php

namespace Atlcom\Tests\Examples\Example43;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto extends \Atlcom\Dto
{
    public const AUTO_DYNAMIC_PROPERTIES_ENABLED = true;

    public string $markName;

    protected function casts(): array
    {
        return [
            'markName' => 'string',
            'modelName' => 'string',
            'price1' => 'integer',
            'price2' => 'integer',
            'price3' => 'integer',
        ];
    }
}

/**
 * Тест 43
 * Работа с динамическими свойствами Dto через опции
 */

final class Example43Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDto = CarDto::create(
            markName: 'Lexus',
            price1: 1000000,
            price2: '2000000',
        )
            ->setCustomOption('markName', 'Toyota')
            ->setCustomOption('modelName', 'RX500');
        $carDto->price3 = '3000000';
        // var_dump($carDto);

        $this->assertTrue($carDto->markName === 'Lexus');
        $this->assertTrue($carDto->modelName === 'RX500');
        $this->assertTrue($carDto->price1 === 1000000);
        $this->assertTrue($carDto->price2 === 2000000);
        $this->assertTrue($carDto->price3 === 3000000);

        $carArray = $carDto->toArray();
        // var_dump($carArray);

        $this->assertTrue($carArray['markName'] === 'Toyota');
        $this->assertTrue($carArray['modelName'] === 'RX500');
        $this->assertTrue($carArray['price1'] === 1000000);
        $this->assertTrue($carArray['price2'] === 2000000);
        $this->assertTrue($carArray['price3'] === 3000000);

    }
}