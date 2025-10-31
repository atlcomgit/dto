<?php

namespace Atlcom\Tests\Other;

use Atlcom\Dto;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class SomeTypesDto extends Dto
{
    public const AUTO_CASTS_OBJECTS_ENABLED = true;

    public int|array|null $id1;
    public array|int|null $id2;

    protected function casts(): array
    {
        $a = parent::getCasts();
        return [...parent::getCasts(), ...parent::castDefault()];
    }
}

class OnSomeTypesDtoTest extends TestCase
{
    #[Test]
    public function onFill(): void
    {
        $dto = SomeTypesDto::fill(['id1' => 1, 'id2' => 2]);

        $this->assertSame(1, $dto->id1);
        $this->assertSame([2], $dto->id2);

        $data = '{"id1":1, "id2":2}';
        $dto = SomeTypesDto::fill($data);

        $this->assertSame(1, $dto->id1);
        $this->assertSame([2], $dto->id2);
    }
}
