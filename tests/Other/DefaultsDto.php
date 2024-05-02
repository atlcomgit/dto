<?php

namespace Atlcom\Tests\Other;

use Atlcom\Dto;

class DefaultsDto extends Dto
{
    public string $name;
    public int $value;


    protected function defaults(): array
    {
        return [
            'name' => 'default',
        ];
    }
}