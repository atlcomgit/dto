<?php

namespace Atlcom\Tests\Examples\Example35;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarbonDto extends \Atlcom\Dto
{
    public const AUTO_DATETIME_CLASS = \Carbon\Carbon::class;

    public \Carbon\Carbon $date1;
    public \DateTime $date2;
    public \Carbon\Carbon|\DateTime $date3;

    protected function casts(): array
    {
        return [
            'date1' => \Carbon\Carbon::class,
            'date2' => \DateTime::class,
            'date3' => 'datetime',
        ];
    }
}

class DateTimeDto extends \Atlcom\Dto
{
    public const AUTO_DATETIME_CLASS = \DateTime::class;

    public \Carbon\Carbon $date1;
    public \DateTime $date2;
    public \Carbon\Carbon|\DateTime $date3;

    protected function casts(): array
    {
        return [
            'date1' => \Carbon\Carbon::class,
            'date2' => \DateTime::class,
            'date3' => 'datetime',
        ];
    }
}

/**
 * Тест 35
 * Работа со свойствами даты и времени
 */

final class Example35Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $date1 = '2024-01-01 00:00:00';
        $date2 = '2024-01-02 00:00:00';
        $date3 = '2024-01-03 00:00:00';

        $carbonDto = CarbonDto::create([
            'date1' => $date1,
            'date2' => $date2,
            'date3' => $date3,
        ]);
        
        $this->assertInstanceOf(\Carbon\Carbon::class, $carbonDto->date1);
        $this->assertInstanceOf(\DateTime::class, $carbonDto->date2);
        $this->assertInstanceOf(\Carbon\Carbon::class, $carbonDto->date3);
        $this->assertEquals(\Carbon\Carbon::parse($date1), $carbonDto->date1);
        $this->assertEquals(\DateTime::createFromFormat('Y-m-d H:i:s', $date2), $carbonDto->date2);
        $this->assertEquals(\Carbon\Carbon::parse($date3), $carbonDto->date3);

        $dateTimeDto = DateTimeDto::create([
            'date1' => $date1,
            'date2' => $date2,
            'date3' => $date3,
        ]);
        
        $this->assertInstanceOf(\Carbon\Carbon::class, $dateTimeDto->date1);
        $this->assertInstanceOf(\DateTime::class, $dateTimeDto->date2);
        $this->assertInstanceOf(\DateTime::class, $dateTimeDto->date3);
        $this->assertEquals(\Carbon\Carbon::parse($date1), $dateTimeDto->date1);
        $this->assertEquals(\DateTime::createFromFormat('Y-m-d H:i:s', $date2), $dateTimeDto->date2);
        $this->assertEquals(\DateTime::createFromFormat('Y-m-d H:i:s', $date3), $dateTimeDto->date3);
    }
}
