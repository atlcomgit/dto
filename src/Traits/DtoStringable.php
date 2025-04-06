<?php

declare(strict_types=1);

namespace Atlcom\Traits;

use Atlcom\Exceptions\DtoException;
use Throwable;

/**
 * Трейт для реализации интерфейса Stringable
 */
trait DtoStringable
{
    /**
     * Магический метод обращения к dto как к строке
     * 
     * @return string
     */
    public function __toString(): string
    {
        try {
            (static::INTERFACE_STRINGABLE_ENABLED) ?: throw new DtoException(
                $this->exceptions('StringableDisabled', ['method' => __FUNCTION__]),
                500,
            );

        } catch (Throwable $exception) {
            $this->onException($exception);
        }

        return $this->toJson();
    }
}