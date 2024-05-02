<?php

namespace Atlcom\Tests\Other;

use Atlcom\Dto;

class OnFillingDto extends Dto
{
    public string $name;
    public int $value;


    protected function onFilling(array &$array): void
    {
        $array['name'] = 'default';
    }
}