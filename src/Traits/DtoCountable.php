<?php

declare(strict_types=1);

namespace Atlcom\Traits;

use Atlcom\Exceptions\DtoException;

/**
 * Трейт для реализации интерфейса Countable
 * @mixin \Atlcom\Dto
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
        $this->consts('INTERFACE_COUNTABLE_ENABLED') ?:
            $this->onException(
                new DtoException(
                    $this->exceptions('CountableDisabled', ['method' => __FUNCTION__]),
                    500,
                ),
            );

        return count([
            ...(array)$this,
            ...($this->consts('AUTO_DYNAMIC_PROPERTIES_ENABLED') ? ($this->options()['customOptions'] ?? []) : []),
        ]);
    }
}
