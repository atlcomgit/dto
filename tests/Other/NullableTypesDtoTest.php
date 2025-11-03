<?php

namespace Atlcom\Tests\Other;

use Atlcom\Dto;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class NullableTypesDto extends Dto
{
    public const AUTO_CASTS_ENABLED = true;
    public const AUTO_MAPPINGS_ENABLED = true;
    public const AUTO_SERIALIZE_ENABLED = true;

    public ?Carbon $date1;
    public Carbon|null $date2;
    public Carbon $date3;
    public ?Carbon $date4;


    protected function casts(): array
    {
        return [...parent::getCasts(), ...parent::castDefault()];
    }
}

class NullableTypesDtoTest extends TestCase
{
    #[Test]
    public function onFill(): void
    {
        $dto = NullableTypesDto::create(['date1' => null, 'date2' => null, 'date3' => $now = Carbon::now(), 'date4' => $now]);

        $this->assertSame(null, $dto->date1);
        $this->assertSame(null, $dto->date2);
        $this->assertSame($now, $dto->date3);
        $this->assertSame($now, $dto->date4);
    }
}
