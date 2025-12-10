<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example20;

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
    public const AUTO_SERIALIZE_ENABLED = true;

    public string $markName;
    public CarTypeEnum $type;
    public \DateTime $date;
}

/**
 * Тест 20
 * Сериализация Dto в массив с автоматическим преобразованием к скалярным типам
 */

final class Example20Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carArray = CarDto::create([
            'markName' => 'Lexus',
            'type' => 'new',
            'date' => '2024-01-01 00:00:00',
        ])->toArray();

        $this->assertArrayHasKey('markName', $carArray);
        $this->assertArrayHasKey('type', $carArray);
        $this->assertArrayHasKey('date', $carArray);
        $this->assertIsString($carArray['markName']);
        $this->assertIsString($carArray['type']);
        $this->assertIsString($carArray['date']);
        $this->assertEquals('Lexus', $carArray['markName']);
        $this->assertEquals('new', $carArray['type']);
        $this->assertEquals('2024-01-01 00:00:00', $carArray['date']);
    }
}
