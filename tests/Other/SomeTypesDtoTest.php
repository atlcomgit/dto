<?php

namespace Atlcom\Tests\Other;

use Atlcom\Dto;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SomeTypesFirstDto extends Dto
{
    public const AUTO_CASTS_OBJECTS_ENABLED = true;

    public int $id1;

    protected function casts(): array
    {
        return [...parent::getCasts(), ...parent::castDefault()];
    }
}

class SomeTypesSecondDto extends SomeTypesFirstDto
{
    public const AUTO_CASTS_OBJECTS_ENABLED = true;

    public int|array|null $id2;
    public array|int|null $id3;
}

class SomeTypesDtoTest extends TestCase
{
    #[Test]
    public function onFill(): void
    {
        $dto = SomeTypesSecondDto::create(['id1' => '1', 'id2' => 2, 'id3' => 3]);

        $this->assertSame(1, $dto->id1);
        $this->assertSame(2, $dto->id2);
        $this->assertSame([3], $dto->id3);

        $data = '{"id1":1, "id2": 2, "id3": 3}';
        $dto = SomeTypesSecondDto::fill($data);

        $this->assertSame(1, $dto->id1);
        $this->assertSame(2, $dto->id2);
        $this->assertSame([3], $dto->id3);
    }
}
