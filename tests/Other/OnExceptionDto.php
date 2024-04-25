<?php

namespace Expo\Dto\Tests\Other;

use Expo\Dto\DefaultDto;
use Throwable;

class OnExceptionDto extends DefaultDto
{
    public string $name;
    public int $value;


    protected function onException(Throwable $exception): void
    {
        // throw $exception;
    }
}