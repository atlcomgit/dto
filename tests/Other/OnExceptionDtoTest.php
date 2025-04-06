<?php

namespace Atlcom\Tests\Other;

use Atlcom\Dto;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Throwable;

class OnExceptionDto extends Dto
{
    public string $name;
    public int $value;


    protected function onException(Throwable $exception): void
    {
        // throw $exception;
    }
}

class OnExceptionDtoTest extends TestCase
{
    private Generator $faker;

    public function setUp(): void
    {
        parent::setUp();

        $this->faker = Factory::create('ru_RU');
    }


    #[Test]
    public function onException(): void
    {
        $name = 'default';
        $value = null;

        $dto = OnExceptionDto::fill([
            'name' => $name,
            'value' => $value,
        ]);

        $this->assertEquals($name, $dto->name);
        $this->assertFalse(isset($dto->value));
    }
}