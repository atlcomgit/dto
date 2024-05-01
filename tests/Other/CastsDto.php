<?php

namespace Atlcom\Dto\Tests\Other;

use Atlcom\Dto\DefaultDto;

class CastsDto extends DefaultDto
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