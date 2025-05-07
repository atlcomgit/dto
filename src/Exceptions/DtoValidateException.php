<?php

declare(strict_types=1);

namespace Atlcom\Exceptions;

class DtoValidateException extends DtoException
{
    public $code = 422;
}
