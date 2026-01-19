<?php

namespace Atlcom\Tests\Other;

use Atlcom\Dto;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CastDateDto extends Dto
{
    public ?Carbon     $date1;
    public ?Carbon     $date2;
    public Carbon|null $date3;
    public Carbon|null $date4;
    public ?string     $date5;
    public string|null $date6;


    /**
     * @inheritDoc
     */
    protected function casts(): array
    {
        return [
            'date1' => 'date',
            'date2' => 'date',
            'date3' => 'datetime',
            'date4' => 'datetime',
            'date5' => 'string',
            'date6' => 'string',
        ];
    }


    /**
     * @inheritDoc
     */
    protected function onSerializing(array &$array): void
    {
        $this->serializeKeys([
            'date1',
            'date2',
            'date3',
            'date4',
            'date5' => 'date',
            'date6' => 'datetime',
        ]);
    }
}

class CastDateDtoTest extends TestCase
{
    #[Test]
    public function onFill(): void
    {
        $dto = CastDateDto::create([
            'date1' => Carbon::now(),
            'date2' => null,
            'date3' => Carbon::now(),
            'date4' => null,
            'date5' => Carbon::now()->toDateString(),
            'date6' => Carbon::now()->toDateTimeString(),
        ]);
        $dtoArray = $dto->toArray();

        $this->assertSame($dto->date1->toDateTimeString(), $dtoArray['date1']);
        $this->assertSame(null, $dtoArray['date2']);
        $this->assertSame($dto->date3->toDateTimeString(), $dtoArray['date3']);
        $this->assertSame(null, $dtoArray['date4']);
        $this->assertSame($dto->date5, $dtoArray['date5']);
        $this->assertSame($dto->date6, $dtoArray['date6']);
    }
}
