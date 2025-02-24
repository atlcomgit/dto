<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example26;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class CarDto extends \Atlcom\Dto
{
    public string $markName = 'Lexus';
    public string $modelName = 'RX500';

    protected function mappings(): array {
        return [
            'modelName' => 'model_name',
        ];
    }
}

class ModelDto extends \Atlcom\Dto
{
    public string $modelName = 'RX500';
    
    protected function mappings(): array {
        return [
            'modelName' => 'model_name',
        ];
    }

    protected function onSerializing(array &$array): void
    {
        $this->mappingKeys($this->mappings());
    }
}

class Entity
{
    public string $model_name;
}

/**
 * Тест 26
 * Сериализация Dto в массив с использованием excludeKeys
 */

final class Example26Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carArray = CarDto::create()->excludeKeys(['modelName'])->toArray();

        $this->assertArrayHasKey('markName', $carArray);
        $this->assertArrayNotHasKey('mark_name', $carArray);
        $this->assertArrayNotHasKey('modelName', $carArray);
        $this->assertEquals('Lexus', $carArray['markName']);

        $modelArray = ModelDto::create()
            ->includeStyles()
            ->onlyKeys(['model_name'])
            ->excludeKeys(['model_name'])
            ->toArray();

        $this->assertArrayNotHasKey('modelName', $modelArray);
        $this->assertArrayNotHasKey('model_name', $modelArray);

        $carArray = CarDto::create()->for(Entity::class)->toArray();

        $this->assertArrayNotHasKey('markName', $carArray);
        $this->assertArrayNotHasKey('mark_name', $carArray);
        $this->assertArrayNotHasKey('modelName', $carArray);
        $this->assertArrayHasKey('model_name', $carArray);
        $this->assertEquals('RX500', $carArray['model_name']);

        $modelArray = ModelDto::create()->toArray();

        $this->assertArrayNotHasKey('modelName', $modelArray);
        $this->assertArrayHasKey('model_name', $modelArray);
        $this->assertEquals('RX500', $modelArray['model_name']);

        $carArray = CarDto::create()->includeStyles(true)->excludeKeys(['modelName'])->toArray();

        $this->assertArrayHasKey('markName', $carArray);
        $this->assertArrayHasKey('mark_name', $carArray);
        $this->assertArrayNotHasKey('modelName', $carArray);
        $this->assertArrayHasKey('model_name', $carArray);

        $modelArray = ModelDto::create()->includeStyles(true)->excludeKeys(['modelName'])->toArray();

        $this->assertArrayNotHasKey('modelName', $modelArray);
        $this->assertArrayNotHasKey('model_name', $modelArray);
    }
}
