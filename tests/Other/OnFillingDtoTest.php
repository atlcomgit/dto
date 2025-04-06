<?php

namespace Atlcom\Tests\Other;

use Atlcom\Dto;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class OnFillingDto extends Dto
{
    public string $name;
    public int $value;


    protected function onFilling(array &$array): void
    {
        $array['name'] = 'default';
    }
}

class OnFillingDtoTest extends TestCase
{
    private Generator $faker;

    public function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create('ru_RU');
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
}