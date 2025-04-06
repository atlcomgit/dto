<?php

namespace Atlcom\Tests\Other;

use Atlcom\Dto;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CastsDto extends Dto
{
    public string $name;
    public int $value;


    protected function casts(): array
    {
        return [
            'value' => 'integer',
        ];
    }
}

class CastsDtoTest extends TestCase
{
    private Generator $faker;

    public function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create('ru_RU');
    }


    #[Test]
    public function casts(): void
    {
        $name = 'default';
        $value = $this->faker->randomFloat(5);

        $dto = CastsDto::fill([
            'name' => $name,
            'value' => $value,
        ]);

        $this->assertObjectHasProperty('name', $dto);
        $this->assertObjectHasProperty('value', $dto);
        $this->assertEquals($name, $dto->name);
        $this->assertEquals((int)$value, $dto->value);
    }
}