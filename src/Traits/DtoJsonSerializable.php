<?php

declare(strict_types=1);

namespace Atlcom\Traits;

use Atlcom\Exceptions\DtoException;

/**
 * Трейт для реализации интерфейса JsonSerializable
 * @mixin \Atlcom\Dto
 */
trait DtoJsonSerializable
{
    /**
     * @internal
     * Задаёт данные, которые должны быть сериализованы в JSON
     * Сериализует объект в значение, которое изначально может быть сериализовано функцией json_encode()
     * @see ../../tests/Examples/Example52/Example52Test.php
     * 
     * @return mixed
     */
    public function jsonSerialize(): mixed
    {
        $this->consts('INTERFACE_JSON_SERIALIZABLE_ENABLED') ?:
            $this->onException(
                new DtoException(
                    $this->exceptions('JsonSerializableDisabled', ['method' => __FUNCTION__]),
                    500,
                ),
            );

        return [
            ...(array)$this,
            ...($this->consts('AUTO_DYNAMIC_PROPERTIES_ENABLED') ? ($this->options()['customOptions'] ?? []) : []),
        ];
    }
}
