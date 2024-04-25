<?php

namespace Expo\Dto\Tests\Examples\Example01;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Примеры классов dto для теста
 */

class IdDto extends \Expo\Dto\DefaultDto
{
    public int $markId;
    public int $modelId;
}

class CarDto extends \Expo\Dto\DefaultDto
{
    public string $markName;
    public string $modelName;
}

class CarService
{
    public static function getMarkById(int $markId): string
    {
        // Находим марку авто по $markId
        return 'Lexus';
    }

    public static function getModelById(int $modelId): string
    {
        // Находим модель авто тарифа по $modelId
        return 'RX500';
    }

    public static function getCar(IdDto $dto): CarDto
    {
        return CarDto::create(
            markName: self::getMarkById(1),
            modelName: self::getModelById(2),
        );
    }
}

/**
 * Тест 01
 * Имеется метод класса, который принимает на вход определённый объект Dto
 * и возвращает другой объект Dto
 */

final class Example01Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDto = CarService::getCar(IdDto::create(markId: 1, modelId: 2));
        
        $this->assertObjectHasProperty('markName', $carDto);
        $this->assertObjectHasProperty('modelName', $carDto);
        $this->assertEquals('Lexus', $carDto->markName);
        $this->assertEquals('RX500', $carDto->modelName);
    }
}
