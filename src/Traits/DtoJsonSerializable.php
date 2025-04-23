<?php

declare(strict_types=1);

namespace Atlcom\Traits;

use Atlcom\Exceptions\DtoException;
use Throwable;

/**
 * Трейт для реализации интерфейса JsonSerializable
 */
trait DtoJsonSerializable
{
    /**
     * Задаёт данные, которые должны быть сериализованы в JSON
     * Сериализует объект в значение, которое изначально может быть сериализовано функцией json_encode()
     * @see ../../tests/Examples/Example52/Example52Test.php
     * 
     * @return mixed
     */
    public function jsonSerialize(): mixed
    {
        try {
            static::INTERFACE_JSON_SERIALIZABLE_ENABLED ?: throw new DtoException(
                $this->exceptions('JsonSerializableDisabled', ['method' => __FUNCTION__]),
                500,
            );

        } catch (Throwable $exception) {
            $this->onException($exception);
        }

        return [
            ...(array)$this,
            ...(static::AUTO_DYNAMIC_PROPERTIES_ENABLED ? ($this->options()['customOptions'] ?? []) : []),
        ];
    }
}
