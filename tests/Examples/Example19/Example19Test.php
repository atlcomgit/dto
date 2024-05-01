<?php

namespace Atlcom\Dto\Tests\Examples\Example19;

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

class CarDto extends \Atlcom\Dto\DefaultDto
{
    public const AUTO_CASTS_ENABLED = true;

    public string $markName;
    public CarTypeEnum $type;
    public \DateTime $date;
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
            'markName' => 'Lexus',
            'type' => 'new',
            'date' => '2024-01-01',
        ]);

        $this->assertObjectHasProperty('markName', $carDto);
        $this->assertObjectHasProperty('type', $carDto);
        $this->assertObjectHasProperty('date', $carDto);
        $this->assertInstanceOf(CarTypeEnum::class, $carDto->type);
        $this->assertInstanceOf(\DateTime::class, $carDto->date);
        $this->assertEquals('Lexus', $carDto->markName);
        $this->assertEquals(CarTypeEnum::NEW , $carDto->type);
        $this->assertEquals(\DateTime::createFromFormat('Y-m-d', '2024-01-01'), $carDto->date);
    }
}
