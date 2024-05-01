<?php

namespace Atlcom\Dto\Tests\Examples\Example32;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class SumDto extends \Atlcom\Dto\DefaultDto
{
    protected int $x;
    protected int $y;
    protected int $sum = 0;

    protected function onAssigned(string $key): void
    {
        $this->sum = ($this->x ?? 0) + ($this->y ?? 0);
    }
}

/**
 * Тест 32
 * Заполнение Dto с событием onAssigned (после изменения)
 */

final class Example32Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $x = 1;
        $y = 2;
        $sum = $x + $y;

        $sumDto = SumDto::create(x: $x, y: $y);

        $this->assertObjectHasProperty('sum', $sumDto);
        $this->assertEquals($sum, $sumDto->sum);

        $sumDto->x = $x = 2;
        $sumDto->y = $y = 3;
        $sum = $x + $y;
        $this->assertEquals($sum, $sumDto->sum);

        $sumDto->sum = 0;
        $this->assertEquals($sum, $sumDto->sum);

        $sumArray = $sumDto->toArray();
        $this->assertArrayNotHasKey('sum', $sumArray);

        $sumArray = $sumDto->withProtectedKeys(true)->toArray();
        $this->assertArrayHasKey('sum', $sumArray);
        $this->assertEquals($sum, $sumArray['sum']);
    }
}
