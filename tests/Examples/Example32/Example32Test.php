<?php

namespace Atlcom\Dto\Tests\Examples\Example32;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class SumDto extends \Atlcom\Dto\DefaultDto
{
    public int $x;
    public int $y;
    protected int $sum = 0;

    protected function onAssigned(string $key): void
    {
        $this->sum = ($this->x ?? 0) + ($this->y ?? 0);
    }
}

/**
 * Тест 32
 * Заполнение Dto с событием onChange (после изменения)
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
        var_dump($sumDto->toArray());

        $this->assertEquals($sum, $sumDto->sum);
    }
}
