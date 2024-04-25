<?php

namespace Expo\Dto\Tests\Other;

use Expo\Dto\DefaultDto;

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