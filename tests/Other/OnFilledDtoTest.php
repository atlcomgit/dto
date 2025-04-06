<?php

namespace Atlcom\Tests\Other;

use Atlcom\Dto;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class OnFilledDto extends Dto
{
    public string $name;
    public int $value;


    protected function onFilled(array $array): void
    {
        $this->name = 'default';
        $this->value = 1;
    }
}

class OnFilledDtoTest extends TestCase
{
    private Generator $faker;

    public function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create('ru_RU');
    }


    #[Test]
    public function onFilled(): void
    {
        $name = 'default';
        $value = 1;

        $dto = OnFilledDto::fill([
            'name' => '',
            'value' => 0,
        ]);

        $this->assertObjectHasProperty('name', $dto);
        $this->assertObjectHasProperty('value', $dto);
        $this->assertEquals($name, $dto->name);
        $this->assertEquals($value, $dto->value);
    }
}