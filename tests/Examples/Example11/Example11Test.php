<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example11;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto extends \Atlcom\Dto
{
    public string $markName;
    public string $modelName;

    protected function onFilling(array &$array): void
    {
        $array['markName'] = 'Lexus';
        $array['modelName'] = 'RX500';
    }
}

/**
 * Тест 11
 * Заполнение Dto с событием onFilling (перед заполнением)
 */

final class Example11Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDto = CarDto::create();

        $this->assertObjectHasProperty('markName', $carDto);
        $this->assertObjectHasProperty('modelName', $carDto);
        $this->assertEquals('Lexus', $carDto->markName);
        $this->assertEquals('RX500', $carDto->modelName);
    }
}
