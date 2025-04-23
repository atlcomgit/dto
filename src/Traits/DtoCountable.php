<?php

declare(strict_types=1);

namespace Atlcom\Traits;

use Atlcom\Exceptions\DtoException;
use Throwable;

/**
 * Трейт для реализации интерфейса Countable
 */
trait DtoCountable
{
    /**
     * Количество элементов объекта
     * Этот метод выполняется при использовании count() на объекте, реализующем интерфейс Countable
     * @see ../../tests/Examples/Example50/Example50Test.php
     * 
     * @return int
     */
    public function count(): int
    {
        try {
            static::INTERFACE_COUNTABLE_ENABLED ?: throw new DtoException(
                $this->exceptions('CountableDisabled', ['method' => __FUNCTION__]),
                500,
            );

        } catch (Throwable $exception) {
            $this->onException($exception);
        }

        return count([
            ...(array)$this,
            ...(static::AUTO_DYNAMIC_PROPERTIES_ENABLED ? ($this->options()['customOptions'] ?? []) : []),
        ]);
    }
}
