<?php

namespace Expo\Dto\Tests\Other;

use Expo\Dto\DefaultDto;

class OnFillingDto extends DefaultDto
{
    public string $name;
    public int $value;


    protected function onFilling(array &$array): void
    {
        $array['name'] = 'default';
    }
}