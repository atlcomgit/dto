<?php

namespace Atlcom\Tests\Other;

use Atlcom\Dto;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class DefaultsDto extends Dto
{
    public string $name;
    public int $value;


    protected function defaults(): array
    {
        return [
            'name' => 'default',
        ];
    }
}

class DefaultsDtoTest extends TestCase
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
}