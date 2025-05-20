<?php

declare(strict_types=1);

namespace Atlcom\Traits;

use Atlcom\Exceptions\DtoException;

/**
 * Трейт для реализации интерфейса ArrayAccess
 * @mixin \Atlcom\Dto
 */
trait DtoArrayAccess
{
    /**
     * @internal
     * Определяет, существует или нет данное смещение (ключ)
     * @see ../../tests/Examples/Example49/Example49Test.php
     *
     * @param mixed $offset Смещение (ключ) для проверки
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        $this->consts('INTERFACE_ARRAY_ACCESS_ENABLED')
            ?: $this->onException(
                throw new DtoException(
                    $this->exceptions('ArrayAccessDisabled', ['property' => $offset, 'method' => __FUNCTION__]),
                    500,
                )
            );

        return property_exists($this, $offset)
            || ($this->consts('AUTO_DYNAMIC_PROPERTIES_ENABLED') && isset($this->getOption('customOptions')[$offset]));
    }


    /**
     * @internal
     * Возвращает заданное смещение (ключ)
     * @see ../../tests/Examples/Example49/Example49Test.php
     *
     * @param mixed $offset Смещение (ключ) для возврата
     * @return mixed
     */
    public function offsetGet(mixed $offset): mixed
    {
        $this->consts('INTERFACE_ARRAY_ACCESS_ENABLED')
            ?: $this->onException(
                throw new DtoException(
                    $this->exceptions('ArrayAccessDisabled', ['property' => $offset, 'method' => __FUNCTION__]),
                    500,
                )
            );

        return $this->{$offset};
    }


    /**
     * @internal
     * Присваивает значение указанному смещению (ключу)
     * @see ../../tests/Examples/Example49/Example49Test.php
     *
     * @param mixed $offset Смещение (ключ), которому будет присваиваться значение
     * @param mixed $value Значение для присвоения
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->consts('INTERFACE_ARRAY_ACCESS_ENABLED')
            ?: $this->onException(
                throw new DtoException(
                    $this->exceptions('ArrayAccessDisabled', ['property' => $offset, 'method' => __FUNCTION__]),
                    500,
                )
            );

        $this->{$offset} = $value;
    }


    /**
     * @internal
     * Удаляет смещение (ключ)
     * @see ../../tests/Examples/Example49/Example49Test.php
     *
     * @param mixed $offset Смещение (ключ) для удаления
     * @return void Функция не возвращает значения после выполнения
     */
    public function offsetUnset(mixed $offset): void
    {
        $this->consts('INTERFACE_ARRAY_ACCESS_ENABLED')
            ?: $this->onException(
                throw new DtoException(
                    $this->exceptions('ArrayAccessDisabled', ['property' => $offset, 'method' => __FUNCTION__]),
                    500,
                )
            );

        if (property_exists($this, $offset)) {
            $this->{$offset} = null;
        } else if ($this->consts('AUTO_DYNAMIC_PROPERTIES_ENABLED')) {
            $customOptions = $this->options()['customOptions'] ?? [];
            unset($customOptions[$offset]);
            $this->options(customOptions: $customOptions);
        }
    }
}
