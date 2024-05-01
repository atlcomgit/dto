<?php

namespace Atlcom\Dto\Tests\Examples\Example21;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto extends \Atlcom\Dto\DefaultDto
{
    public string $markName = 'Lexus';
    public string $modelName = 'RX500';
}

/**
 * Тест 21
 * Сериализация Dto в массив с использованием autoMappings
 */

final class Example21Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carArray = (new CarDto())->autoMappings()->fillFromArray([
            'mark_name' => 'Lexus',
            'model_name' => 'RX500',
        ])->toArray();

        $this->assertArrayHasKey('markName', $carArray);
        $this->assertArrayHasKey('modelName', $carArray);
        $this->assertEquals('Lexus', $carArray['markName']);
        $this->assertEquals('RX500', $carArray['modelName']);
    }
}
