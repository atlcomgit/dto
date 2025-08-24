<?php

namespace Atlcom\Tests\Other;

use Atlcom\Dto;
use Faker\Factory;
use Faker\Generator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class OnSerializingCastDto extends Dto
{
    public array $data;


    protected function casts(): array
    {
        return [
            'data' => static fn ($v) => array_map(static fn ($item) => (int)$item, $v),
        ];
    }


    protected function onSerializing(array &$array): void
    {
        $this->serializeKeys(true);
    }
}

class OnSerializingCastDtoTest extends TestCase
{
    #[Test]
    public function toArraySerializeCasts(): void
    {
        $data = [1, '2'];

        $dtoArray = OnSerializingCastDto::create([
            'data' => $data,
        ])->toArray();

        $this->assertArrayHasKey('data', $dtoArray);
        $this->assertSame(1, $dtoArray['data'][0]);
        $this->assertSame(2, $dtoArray['data'][1]);
    }
}
