<?php

namespace Expo\Dto\Tests\Other;

use Expo\Dto\DefaultDto;

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