<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example38;

use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto extends \Atlcom\Dto
{
    public string $markName = 'Lexus';
    public Carbon $year;
    public Carbon $date;
}

/**
 * Тест 38
 * Получение хеша dto
 */

final class Example38Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDto = CarDto::create(
            year: Carbon::parse('2024-01-01 00:00:00'),
            date: Carbon::now(),
        );

        $carArray = $carDto->excludeKeys(['date'])->toArray();
        asort($carArray);

        $expectHash = ltrim(
            ''
            . ':' . basename(str_replace('\\', '/', $carDto::class))
            . ':' . hash('sha256', '' . $carDto::class . json_encode($carArray)),
            ':'
        );
        $actualHash = $carDto->excludeKeys(['date'])->getHash();

        $this->assertObjectHasProperty('year', $carDto);
        $this->assertArrayNotHasKey('date', $carArray);
        $this->assertEquals($expectHash, $actualHash);
        $this->assertEquals('CarDto:81f2a8e48ec40ca36faffa1eec01dc5c2b191b088adcccf7814214090218a308', $actualHash);
    }
}
