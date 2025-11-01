<?php

namespace Atlcom\Tests\Other;

use Atlcom\Tests\Dto\ExampleTestDto;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ExtendTypesDto extends ExampleTestDto
{
    public const AUTO_CASTS_OBJECTS_ENABLED = true;

    public int|array|null $id2;
    public array|int|null $id3;

    protected function casts(): array
    {
        return [...parent::getCasts(), ...parent::castDefault()];
    }
}

class ExtendTypesDtoTest extends TestCase
{
    #[Test]
    public function onFill(): void
    {
        $dto = ExtendTypesDto::create(['exampleId' => '1', 'id2' => 2, 'id3' => 3]);

        $this->assertSame(1, $dto->exampleId);
        $this->assertSame(2, $dto->id2);
        $this->assertSame([3], $dto->id3);

        $data = '{"exampleId":1, "id2": 2, "id3": 3}';
        $dto = ExtendTypesDto::fill($data);

        $this->assertSame(1, $dto->exampleId);
        $this->assertSame(2, $dto->id2);
        $this->assertSame([3], $dto->id3);
    }
}
