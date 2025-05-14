<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example19;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

enum CarTypeEnum: string
{
    case OLD = 'old';
    case NEW = 'new';
}

class CarDto extends \Atlcom\Dto
{
    public const AUTO_CASTS_ENABLED = true;

    public int $id;
    public string $markName;
    public CarTypeEnum $type;
    public \Carbon\Carbon $date1;
    public \DateTime $date2;
}

/**
 * Тест 19
 * Заполнение Dto с автоматическим преобразованием типов
 */

final class Example19Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDto = CarDto::create([
            'id' => '123',
            'markName' => 'Lexus',
            'type' => 'new',
            'date1' => '2024-01-01 00:00:00',
            'date2' => '2024-01-02 00:00:00',
        ]);

        $this->assertObjectHasProperty('id', $carDto);
        $this->assertObjectHasProperty('markName', $carDto);
        $this->assertObjectHasProperty('type', $carDto);
        $this->assertObjectHasProperty('date1', $carDto);
        $this->assertObjectHasProperty('date2', $carDto);
        $this->assertInstanceOf(CarTypeEnum::class, $carDto->type);
        $this->assertInstanceOf(\Carbon\Carbon::class, $carDto->date1);
        $this->assertInstanceOf(\DateTime::class, $carDto->date2);
        $this->assertSame(123, $carDto->id);
        $this->assertSame('Lexus', $carDto->markName);
        $this->assertEquals(CarTypeEnum::NEW , $carDto->type);
        $this->assertEquals(\Carbon\Carbon::parse('2024-01-01  00:00:00'), $carDto->date1);
        $this->assertEquals(\DateTime::createFromFormat('Y-m-d H:i:s', '2024-01-02  00:00:00'), $carDto->date2);
    }
}
