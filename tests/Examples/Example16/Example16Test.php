<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example16;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto extends \Atlcom\Dto
{
    public string $markName = 'Lexus';
    public string $modelName = '';

    protected function onSerialized(array &$array): void
    {
        $array['modelName'] = 'RX500';
    }
}

/**
 * Тест 16
 * Заполнение Dto с событием onSerialized (после преобразования в массив)
 */

final class Example16Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carArray = CarDto::create()->toArray();

        $this->assertArrayHasKey('markName', $carArray);
        $this->assertArrayHasKey('modelName', $carArray);
        $this->assertEquals('Lexus', $carArray['markName'] ?? null);
        $this->assertEquals('RX500', $carArray['modelName'] ?? null);
    }
}
