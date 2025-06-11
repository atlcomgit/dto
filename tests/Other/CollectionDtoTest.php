<?php

namespace Atlcom\Tests\Other;

use Atlcom\Dto;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CollectionUserDto extends Dto
{
    public string $name;
}
class CollectionDto extends Dto
{
    public array $users;

    protected function casts(): array
    {
        return [
            'users' => [CollectionUserDto::class],
        ];
    }
}

class CollectionDtoTest extends TestCase
{
    #[Test]
    public function collection(): void
    {
        $dto = CollectionDto::create(
            users: [
                ['name' => 'a'],
                ['name' => 'b'],
            ],
        );

        $this->assertIsArray($dto->users);

        $this->assertInstanceOf(CollectionUserDto::class, $dto->users[0]);
        $this->assertInstanceOf(CollectionUserDto::class, $dto->users[1]);

        $this->assertSame('a', $dto->users[0]->name);
        $this->assertSame('b', $dto->users[1]->name);
    }


    #[Test]
    public function serialize(): void
    {
        $dto = CollectionDto::create(
            users: [
                ['name' => 'a'],
                ['name' => 'b'],
            ],
        );

        $dtoArray = $dto->serializeKeys(true)->toArray();

        $this->assertIsArray($dtoArray);

        $this->assertSame('a', $dtoArray['users'][0]['name']);
        $this->assertSame('b', $dtoArray['users'][1]['name']);
    }
}
