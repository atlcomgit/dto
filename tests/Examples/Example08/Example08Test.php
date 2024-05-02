<?php

namespace Atlcom\Tests\Examples\Example08;

use Atlcom\Interfaces\AttributeDtoInterface;
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

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class YearCast implements AttributeDtoInterface
{
    public function __construct(private ?bool $enabled = null)
    {
    }

    public function handle(string &$key, mixed &$value, mixed $default, string $class): void
    {
        $value = $this->enabled ? (int)$value : $value;
    }
}

class CarDto extends \Atlcom\Dto
{
    public int $id;
    public CarTypeEnum $type;
    public string $comment;
    #[YearCast(enabled: true)]
    public int $year;

    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'type' => CarTypeEnum::class,
            'comment' => static fn ($value) => mb_substr($value, 0, 6),
        ];
    }
}

/**
 * Тест 08
 * Заполнение Dto с преобразованием типов
 */

final class Example08Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDto = CarDto::create([
            'id' => '1',
            'type' => 'new',
            'comment' => 'Примерное описание',
            'year' => '2024',
        ]);

        $this->assertObjectHasProperty('id', $carDto);
        $this->assertObjectHasProperty('type', $carDto);
        $this->assertObjectHasProperty('comment', $carDto);
        $this->assertObjectHasProperty('year', $carDto);
        $this->assertEquals(1, $carDto->id);
        $this->assertEquals(CarTypeEnum::NEW , $carDto->type);
        $this->assertEquals('Пример', $carDto->comment);
        $this->assertEquals(2024, $carDto->year);
    }
}
