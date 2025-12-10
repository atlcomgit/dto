<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example39;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Примеры классов dto для теста
 */

class CarDto extends \Atlcom\Dto
{
    public ?string      $markName;
    public ?array       $array;
    public ?object      $object;
    public ?\Atlcom\Dto $dto;
    public ?bool        $boolean;
}

/**
 * Тест 39
 * Проверка на пустой dto
 */

final class Example39Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDto = CarDto::create();
        $this->assertTrue($carDto->isEmpty());

        $carDto = new CarDto();
        $this->assertTrue($carDto->isEmpty());

        $carDto = CarDto::create(array: []);
        $this->assertTrue($carDto->isEmpty());

        $carDto = CarDto::create(object: new stdClass());
        $this->assertTrue($carDto->isEmpty());

        $carDto = CarDto::create(dto: CarDto::create());
        $this->assertTrue($carDto->isEmpty());


        $carDto = CarDto::create(markName: '');
        $this->assertFalse($carDto->isEmpty());

        $carDto = CarDto::create(markName: 'Lexus');
        $this->assertFalse($carDto->isEmpty());

        $carDto = CarDto::create(array: [1]);
        $this->assertFalse($carDto->isEmpty());

        $carDto = CarDto::create(object: (object)['modelName' => 'RX500']);
        $this->assertFalse($carDto->isEmpty());

        $carDto = CarDto::create(dto: CarDto::create(markName: 'Lexus'));
        $this->assertFalse($carDto->isEmpty());

        $carDto = CarDto::create(boolean: null);
        $this->assertTrue($carDto->isEmpty());

        $carDto = CarDto::create(boolean: true);
        $this->assertFalse($carDto->isEmpty());

        $carDto = CarDto::create(boolean: false);
        $this->assertFalse($carDto->isEmpty());

        $carDto = CarDto::create(
            markName: null,
            array: [],
            object: new stdClass(),
            dto: CarDto::create(),
            boolean: null,
        );
        $this->assertTrue($carDto->isEmpty());
    }
}
