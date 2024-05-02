<?php

namespace Atlcom\Tests\Examples\Example28;

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
}

/**
 * Тест 28
 * Сериализация Dto в массив с использованием serializeKeys
 */

final class Example28Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDto = CarDto::create(
            markDto: MarkDto::create(id: 1, markName: 'Lexus'),
            modelDto: ModelDto::create(id: 2, modelName: 'RX500'),
        );

        $carArray = $carDto->serializeKeys(['markDto'])->toArray();

        $this->assertArrayHasKey('markDto', $carArray);
        $this->assertArrayHasKey('modelDto', $carArray);
        $this->assertIsArray($carArray['markDto']);
        $this->assertInstanceOf(ModelDto::class, $carArray['modelDto']);
        $this->assertArrayHasKey('id', $carArray['markDto']);
        $this->assertArrayHasKey('markName', $carArray['markDto']);
        $this->assertObjectHasProperty('id', $carArray['modelDto']);
        $this->assertObjectHasProperty('modelName', $carArray['modelDto']);

        $this->assertEquals(1, $carArray['markDto']['id']);
        $this->assertEquals('Lexus', $carArray['markDto']['markName']);
        $this->assertEquals(2, $carArray['modelDto']->id);
        $this->assertEquals('RX500', $carArray['modelDto']->modelName);

        $carArray = $carDto->serializeKeys(true)->toArray();

        $this->assertArrayHasKey('markDto', $carArray);
        $this->assertArrayHasKey('modelDto', $carArray);
        $this->assertIsArray($carArray['markDto']);
        $this->assertIsArray($carArray['modelDto']);
        $this->assertArrayHasKey('id', $carArray['markDto']);
        $this->assertArrayHasKey('markName', $carArray['markDto']);
        $this->assertArrayHasKey('id', $carArray['modelDto']);
        $this->assertArrayHasKey('modelName', $carArray['modelDto']);

        $this->assertEquals(1, $carArray['markDto']['id']);
        $this->assertEquals('Lexus', $carArray['markDto']['markName']);
        $this->assertEquals(2, $carArray['modelDto']['id']);
        $this->assertEquals('RX500', $carArray['modelDto']['modelName']);

        $carArray = $carDto->serializeKeys(false)->toArray();

        $this->assertArrayHasKey('markDto', $carArray);
        $this->assertArrayHasKey('modelDto', $carArray);
        $this->assertInstanceOf(MarkDto::class, $carArray['markDto']);
        $this->assertInstanceOf(ModelDto::class, $carArray['modelDto']);
        $this->assertObjectHasProperty('id', $carArray['markDto']);
        $this->assertObjectHasProperty('markName', $carArray['markDto']);
        $this->assertObjectHasProperty('id', $carArray['modelDto']);
        $this->assertObjectHasProperty('modelName', $carArray['modelDto']);

        $this->assertEquals(1, $carArray['markDto']->id);
        $this->assertEquals('Lexus', $carArray['markDto']->markName);
        $this->assertEquals(2, $carArray['modelDto']->id);
        $this->assertEquals('RX500', $carArray['modelDto']->modelName);
    }
}
