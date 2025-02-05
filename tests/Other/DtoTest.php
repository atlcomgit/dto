<?php

namespace Atlcom\Tests\Other;

use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Тест заполнения Dto
 */
final class DtoTest extends TestCase
{
    private Generator $faker;

    public function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create('ru_RU');
    }

    #[Test]
    public function defaults(): void
    {
        $name = 'default';
        $value = $this->faker->randomNumber(5);

        $dto = DefaultsDto::fill([
            'value' => $value,
        ]);

        $this->assertObjectHasProperty('name', $dto);
        $this->assertObjectHasProperty('value', $dto);
        $this->assertEquals($name, $dto->name);
        $this->assertEquals($value, $dto->value);
    }

    #[Test]
    public function casts(): void
    {
        $name = 'default';
        $value = $this->faker->randomFloat(5);

        $dto = CastsDto::fill([
            'name'  => $name,
            'value' => $value,
        ]);

        $this->assertObjectHasProperty('name', $dto);
        $this->assertObjectHasProperty('value', $dto);
        $this->assertEquals($name, $dto->name);
        $this->assertEquals((int)$value, $dto->value);
    }

    #[Test]
    public function onFilling(): void
    {
        $name = 'default';
        $value = $this->faker->randomNumber();

        $dto = OnFillingDto::fill([
            'value' => $value,
        ]);

        $this->assertObjectHasProperty('name', $dto);
        $this->assertObjectHasProperty('value', $dto);
        $this->assertEquals($name, $dto->name);
        $this->assertEquals($value, $dto->value);
    }

    #[Test]
    public function onFilled(): void
    {
        $name = 'default';
        $value = 1;

        $dto = OnFilledDto::fill([
            'name'  => '',
            'value' => 0,
        ]);

        $this->assertObjectHasProperty('name', $dto);
        $this->assertObjectHasProperty('value', $dto);
        $this->assertEquals($name, $dto->name);
        $this->assertEquals($value, $dto->value);
    }

    #[Test]
    public function onException(): void
    {
        $name = 'default';
        $value = null;

        $dto = OnExceptionDto::fill([
            'name'  => $name,
            'value' => $value,
        ]);

        $this->assertEquals($name, $dto->name);
        $this->assertFalse(isset($dto->value));
    }
}