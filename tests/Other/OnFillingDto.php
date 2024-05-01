<?php

namespace Atlcom\Dto\Tests\Other;

use Atlcom\Dto\DefaultDto;

class OnFillingDto extends DefaultDto
{
    public string $name;
    public int $value;


    protected function onFilling(array &$array): void
    {
        $array['name'] = 'default';
    }
}