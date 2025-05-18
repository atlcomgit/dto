<?php

declare(strict_types=1);

namespace Atlcom\Traits;

use ArrayIterator;
use Atlcom\Exceptions\DtoException;
use Throwable;
use Traversable;

/**
 * Трейт для реализации интерфейса IteratorAggregate
 * @mixin \Atlcom\Dto
 */
trait DtoIteratorAggregate
{
    /**
     * Возвращает внешний итератор
     * @see ../../tests/Examples/Example51/Example51Test.php
     * 
     * @return Traversable<string, mixed>|mixed[]
     */
    public function getIterator(): Traversable
    {
        try {
            $this->consts('INTERFACE_ITERATOR_AGGREGATE_ENABLED') ?: throw new DtoException(
                $this->exceptions('IteratorAggregateDisabled', ['method' => __FUNCTION__]),
                500,
            );

        } catch (Throwable $exception) {
            $this->onException($exception);
        }

        return new ArrayIterator([
            ...(array)$this,
            ...($this->consts('AUTO_DYNAMIC_PROPERTIES_ENABLED') ? ($this->options()['customOptions'] ?? []) : []),
        ]);
    }
}
