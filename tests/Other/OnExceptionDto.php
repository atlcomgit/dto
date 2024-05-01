<?php

namespace Atlcom\Dto\Tests\Other;

use Atlcom\Dto\DefaultDto;
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