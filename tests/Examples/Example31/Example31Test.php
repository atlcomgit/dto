<?php

namespace Atlcom\Tests\Examples\Example31;

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
    /** @var array<MarkDto> */
    public array $markNames;

    /** @var array<ModelDto> */
    #[\Atlcom\Attributes\Collection(ModelDto::class)]
    public array $modelNames;

    protected function casts(): array
    {
        return [
            'markNames' => [MarkDto::class],
        ];
    }
}

/**
 * Тест 31
 * Заполнение Dto с массивами объектов через casts и аттрибут
 */

final class Example31Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $array = [
            'markNames' => [
                ['id' => 1, 'markName' => 'Lexus'],
                ['id' => 2, 'markName' => 'Toyota'],
            ],
            'modelNames' => [
                ['id' => 3, 'modelName' => 'RX500'],
                ['id' => 4, 'modelName' => 'RAV4'],
            ],
        ];

        $carArray = CarDto::create($array)->toArray();

        $this->assertArrayHasKey('markNames', $carArray);
        $this->assertArrayHasKey('modelNames', $carArray);
        $this->assertIsArray($carArray['markNames']);
        $this->assertIsArray($carArray['modelNames']);
        $this->assertInstanceOf(MarkDto::class, $carArray['markNames'][0]);
        $this->assertInstanceOf(MarkDto::class, $carArray['markNames'][1]);
        $this->assertInstanceOf(ModelDto::class, $carArray['modelNames'][0]);
        $this->assertInstanceOf(ModelDto::class, $carArray['modelNames'][1]);

        $this->assertObjectHasProperty('id', $carArray['markNames'][0]);
        $this->assertObjectHasProperty('id', $carArray['markNames'][1]);
        $this->assertObjectHasProperty('markName', $carArray['markNames'][0]);
        $this->assertObjectHasProperty('markName', $carArray['markNames'][1]);
        $this->assertObjectHasProperty('id', $carArray['modelNames'][0]);
        $this->assertObjectHasProperty('id', $carArray['modelNames'][1]);
        $this->assertObjectHasProperty('modelName', $carArray['modelNames'][0]);
        $this->assertObjectHasProperty('modelName', $carArray['modelNames'][1]);

        $this->assertEquals(1, $carArray['markNames'][0]->id);
        $this->assertEquals('Lexus', $carArray['markNames'][0]->markName);
        $this->assertEquals(2, $carArray['markNames'][1]->id);
        $this->assertEquals('Toyota', $carArray['markNames'][1]->markName);
        $this->assertEquals(3, $carArray['modelNames'][0]->id);
        $this->assertEquals('RX500', $carArray['modelNames'][0]->modelName);
        $this->assertEquals(4, $carArray['modelNames'][1]->id);
        $this->assertEquals('RAV4', $carArray['modelNames'][1]->modelName);
    }
}
