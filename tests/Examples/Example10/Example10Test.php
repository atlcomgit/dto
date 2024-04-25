<?php

namespace Expo\Dto\Tests\Examples\Example10;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class MarkDto extends \Expo\Dto\DefaultDto
{
    public int $id;
    public string $markName;

    protected function mappings(): array
    {
        return [
            'markName' => 'name',
        ];
    }
}

class ModelDto extends \Expo\Dto\DefaultDto
{
    public int $id;
    public string $modelName;

    protected function mappings(): array
    {
        return [
            'modelName' => 'name',
        ];
    }
}

class CarDto extends \Expo\Dto\DefaultDto
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

    protected function mappings(): array
    {
        return [
            'markDto' => 'mark',
            'modelDto' => 'model',
        ];
    }
}

/**
 * Тест 10
 * Заполнение Dto с вложенными Dto, маппингом свойств и сериализацией
 */

final class Example10Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDto = CarDto::create([
            'mark' => [
                'id' => 1,
                'name' => 'Lexus',
            ],
            'model' => [
                'id' => 2,
                'name' => 'RX500',
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
