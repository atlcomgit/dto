<?php

namespace Expo\Dto\Tests\Other;

use Expo\Dto\DefaultDto;

class OnFilledDto extends DefaultDto
{
    public string $name;
    public int $value;


    protected function onFilled(array $array): void
    {
        $this->name = 'default';
        $this->value = 1;
    }
}