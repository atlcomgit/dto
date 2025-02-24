<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example32;

use Atlcom\Attributes\Hidden;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class SumDto extends \Atlcom\Dto
{
    #[Hidden]
    public int $x;
    #[Hidden]
    public int $y;
    public int $sum = 0;

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

        $x = $sumDto->x(2);
        $y = $sumDto->y(3);
        $sum = $x + $y;
        $this->assertEquals($sum, $sumDto->sum);

        // $sumDto->sum = 0;
        // $this->assertEquals($sum, $sumDto->sum);

        $sumArray = $sumDto->toArray();
        $this->assertArrayNotHasKey('x', $sumArray);
        $this->assertArrayNotHasKey('y', $sumArray);
        $this->assertArrayHasKey('sum', $sumArray);

        $sumArray = $sumDto->withProtectedKeys(true)->toArray();
        $this->assertArrayHasKey('sum', $sumArray);
        $this->assertEquals($sum, $sumArray['sum']);
    }
}
