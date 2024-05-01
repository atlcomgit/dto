<?php

namespace Atlcom\Dto\Tests\Other;

use Atlcom\Dto\DefaultDto;

class DefaultsDto extends DefaultDto
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