<?php

namespace Atlcom\Tests\Other;

use Atlcom\Dto;

class OnFilledDto extends Dto
{
    public string $name;
    public int $value;


    protected function onFilled(array $array): void
    {
        $this->name = 'default';
        $this->value = 1;
    }
}