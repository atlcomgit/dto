<?php

declare(strict_types=1);

namespace Atlcom\Traits;

use Atlcom\Exceptions\DtoException;
use Throwable;

/**
 * Трейт для реализации интерфейса Serializable
 */
trait DtoSerializable
{
    /**
     * Представляет объект в виде строки
     * Возвращает строковое представление объекта
     * 
     * @return string|null
     */
    public function serialize(): string
    {
        try {
            (static::INTERFACE_SERIALIZABLE_ENABLED) ?: throw new DtoException(
                $this->exceptions('SerializableDisabled', ['method' => __FUNCTION__]),
                500,
            );

        } catch (Throwable $exception) {
            $this->onException($exception);
        }

        return serialize([
            ...(array)$this,
            ...(static::AUTO_DYNAMIC_PROPERTIES_ENABLED ? ($this->options()['customOptions'] ?? []) : []),
        ]);
    }


    /**
     * Создаёт объект
     * Вызывается во время десериализации объекта
     *
     * @param string $data Строковое представление объекта
     * @return void
     */
    public function unserialize(string $data): void
    {
        try {
            (static::INTERFACE_SERIALIZABLE_ENABLED) ?: throw new DtoException(
                $this->exceptions('SerializableDisabled', ['method' => __FUNCTION__]),
                500,
            );

        } catch (Throwable $exception) {
            $this->onException($exception);
        }

        $this->fillDto(unserialize($data));
    }
}