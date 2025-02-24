<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example09;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class MarkDto extends \Atlcom\Dto
{
    public int $id;
    public string $markName;
}

class ModelDto extends \Atlcom\Dto
{
    public int $id;
    public string $modelName;
}

class CarDto extends \Atlcom\Dto
{
    public MarkDto $markDto;
    public ModelDto $modelDto;

    protected function casts(): array
    {
        return [
            'markDto' => MarkDto::class,
            'modelDto' => ModelDto::class,
        ];
    }
}

/**
 * Тест 09
 * Заполнение Dto с вложенными Dto
 */

final class Example09Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDto = CarDto::create([
            'markDto' => [
                'id' => 1,
                'markName' => 'Lexus',
            ],
            'modelDto' => [
                'id' => 2,
                'modelName' => 'RX500',
            ],
        ]);

        $this->assertObjectHasProperty('markDto', $carDto);
        $this->assertObjectHasProperty('modelDto', $carDto);
        $this->assertInstanceOf(MarkDto::class, $carDto->markDto);
        $this->assertInstanceOf(ModelDto::class, $carDto->modelDto);
        $this->assertObjectHasProperty('markName', $carDto->markDto);
        $this->assertObjectHasProperty('modelName', $carDto->modelDto);
        $this->assertEquals('Lexus', $carDto->markDto->markName);
        $this->assertEquals('RX500', $carDto->modelDto->modelName);
    }
}
