<?php

namespace Atlcom\Tests\Other;

use Atlcom\Dto;

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