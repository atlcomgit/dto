<?php

namespace Atlcom\Tests\Other;

use Atlcom\Dto;
use Throwable;

class OnExceptionDto extends Dto
{
    public string $name;
    public int $value;


    protected function onException(Throwable $exception): void
    {
        // throw $exception;
    }
}