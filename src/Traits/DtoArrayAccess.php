<?php

declare(strict_types=1);

namespace Atlcom\Traits;

use Atlcom\Exceptions\DtoException;
use Throwable;

/**
 * Трейт для реализации интерфейса ArrayAccess
 */
trait DtoArrayAccess
{
    /**
     * Определяет, существует или нет данное смещение (ключ)
     *
     * @param mixed $offset Смещение (ключ) для проверки
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        try {
            (static::INTERFACE_ARRAY_ACCESS_ENABLED) ?: throw new DtoException(
                $this->exceptions('ArrayAccessDisabled', ['property' => $offset, 'method' => __FUNCTION__]),
                500,
            );

        } catch (Throwable $exception) {
            $this->onException($exception);
        }

        return property_exists($this, $offset)
            || (static::AUTO_DYNAMIC_PROPERTIES_ENABLED && isset($this->getOption('customOptions')[$offset]));
    }


    /**
     * Возвращает заданное смещение (ключ)
     *
     * @param mixed $offset Смещение (ключ) для возврата
     * @return mixed
     */
    public function offsetGet(mixed $offset): mixed
    {
        try {
            (static::INTERFACE_ARRAY_ACCESS_ENABLED) ?: throw new DtoException(
                $this->exceptions('ArrayAccessDisabled', ['property' => $offset, 'method' => __FUNCTION__]),
                500,
            );

        } catch (Throwable $exception) {
            $this->onException($exception);
        }

        return $this->{$offset};
    }


    /**
     * Присваивает значение указанному смещению (ключу)
     *
     * @param mixed $offset Смещение (ключ), которому будет присваиваться значение
     * @param mixed $value Значение для присвоения
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        try {
            (static::INTERFACE_ARRAY_ACCESS_ENABLED) ?: throw new DtoException(
                $this->exceptions('ArrayAccessDisabled', ['property' => $offset, 'method' => __FUNCTION__]),
                500,
            );

        } catch (Throwable $exception) {
            $this->onException($exception);
        }

        $this->{$offset} = $value;
    }


    /**
     * Удаляет смещение (ключ).
     *
     * @param mixed $offset Смещение (ключ) для удаления
     * @return void Функция не возвращает значения после выполнения
     */
    public function offsetUnset(mixed $offset): void
    {
        try {
            (static::INTERFACE_ARRAY_ACCESS_ENABLED) ?: throw new DtoException(
                $this->exceptions('ArrayAccessDisabled', ['property' => $offset, 'method' => __FUNCTION__]),
                500,
            );

        } catch (Throwable $exception) {
            $this->onException($exception);
        }

        if (property_exists($this, $offset)) {
            $this->{$offset} = null;
        } else if (static::AUTO_DYNAMIC_PROPERTIES_ENABLED) {
            $customOptions = $this->options()['customOptions'] ?? [];
            unset($customOptions[$offset]);
            $this->options(customOptions: $customOptions);
        }
    }
}