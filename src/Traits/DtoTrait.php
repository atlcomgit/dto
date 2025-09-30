<?php

declare(strict_types=1);

namespace Atlcom\Traits;

use Atlcom\Exceptions\DtoException;
use Throwable;

/**
 * Трейт конструктора и деструктора dto
 * @mixin \Atlcom\Dto
 */
trait DtoTrait
{
    /**
     * Конструктор Dto
     * @see ../../tests/Examples/Example21/Example21Test.php
     * @see ../../tests/Examples/Example39/Example39Test.php
     * @see ../../tests/Examples/Example44/Example44Test.php
     *
     * @param array|object|string|null $constructData
     */
    public function __construct(array|object|string|null $constructData = null)
    {
        $this->onCreating($constructData);

        try {
            is_null($constructData) ?: $this->fillFromArray(static::convertDataToArray($constructData));

        } catch (Throwable $exception) {

            throw new DtoException($exception->getMessage(), $exception->getCode(), $exception);
        }

        $this->onCreated($constructData);
    }


    /**
     * Деструктор dto
     */
    public function __destruct()
    {
        $this->reset();
    }
}
