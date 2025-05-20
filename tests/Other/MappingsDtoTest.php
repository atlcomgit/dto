<?php

namespace Atlcom\Tests\Other;

use Atlcom\Dto;
use Atlcom\Exceptions\DtoException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MappingsDto extends Dto
{
    public const AUTO_MAPPINGS_ENABLED = true;
    public const AUTO_DYNAMIC_PROPERTIES_ENABLED = true;

    public int $userId;

    protected function casts(): array
    {
        return [...parent::getCasts(), ...parent::castDefault()];
    }
}

class MappingsDtoTest extends TestCase
{
    #[Test]
    public function mappings(): void
    {
        $dto = MappingsDto::create(userId: 1, userName: 'test');

        $this->assertSame(1, $dto->userId);
        $this->assertSame(1, $dto->user_id);
        $this->assertSame('test', $dto->userName);
        $this->assertSame('test', $dto->user_name);

        $dto->consts('AUTO_DYNAMIC_PROPERTIES_ENABLED', false);

        $this->assertSame(1, $dto->userId);
        $this->assertSame(1, $dto->user_id);

        $dto->consts('AUTO_MAPPINGS_ENABLED', false);

        $this->assertSame(1, $dto->userId);

        $this->expectException(DtoException::class);
        $this->assertSame(null, $dto->user_id);
    }
}
