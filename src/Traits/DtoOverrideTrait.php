<?php

namespace Atlcom\Traits;

use ReflectionProperty;
use Throwable;

/**
 * Трейт переопределяемых методов
 */
trait DtoOverrideTrait
{
    /**
     * @override
     * Возвращает массив маппинга свойств
     *
     * @return array
     */
    protected function mappings(): array
    {
        return [];
    }


    /**
     * @override
     * Возвращает массив значений по умолчанию
     *
     * @return array
     */
    protected function defaults(): array
    {
        return [];
    }


    /**
     * @override
     * Возвращает массив преобразований типов
     *
     * @return array
     */
    protected function casts(): array
    {
        return [];
    }


    /**
     * @override
     * Метод вызывается до заполнения dto
     *
     * @param array $array
     * @return void
     */
    protected function onFilling(array &$array): void
    {
        !(  // приводим id к integer
            property_exists($this, 'id')
            && array_key_exists('id', $array)
            && str_contains((string)(new ReflectionProperty(get_class($this), 'id'))->getType(), 'int')
            && is_numeric($array['id'] ?? null)
        ) ?: $array['id'] = (int)($array['id'] ?? 0);
    }


    /**
     * @override
     * Метод вызывается после заполнения dto
     *
     * @param array $array
     * @return void
     */
    protected function onFilled(array $array): void
    {
    }


    /**
     * @override
     * Метод вызывается до объединения с dto
     *
     * @param array $array
     * @return void
     */
    protected function onMerging(array &$array): void
    {
    }


    /**
     * @override
     * Метод вызывается после объединения с dto
     *
     * @param array $array
     * @return void
     */
    protected function onMerged(array $array): void
    {
    }


    /**
     * @override
     * Метод вызывается перед изменением значения свойства dto
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    protected function onAssigning(string $key, mixed $value): void
    {
    }


    /**
     * @override
     * Метод вызывается после изменения значения свойства dto
     *
     * @param string $key
     * @return void
     */
    protected function onAssigned(string $key): void
    {
    }


    /**
     * @override
     * Метод вызывается до преобразования dto в массив
     *
     * @param array $array
     * @return void
     */
    protected function onSerializing(array &$array): void
    {
    }


    /**
     * @override
     * Метод вызывается после преобразования dto в массив
     *
     * @param array $array
     * @return void
     */
    protected function onSerialized(array &$array): void
    {
    }


    /**
     * @override
     * Метод вызывается во время исключения при заполнении dto
     *
     * @param Throwable $exception
     * @return void
     * @throws \Exception
     */
    protected function onException(Throwable $exception): void
    {
        throw $exception;
    }


    /**
     * @override
     * Сообщения ошибок dto
     *
     * @param string $message
     * @param array $values
     * @return string
     */
    protected function exceptions(string $messageCode, array $messageItems): string
    {
        return match ($messageCode) {
            'PropertyNotFound' => sprintf(
                $this->toBasename($this) . '->%s: property not found',
                $messageItems['property'],
            ),
            'PropertyAssignType' => sprintf(
                $this->toBasename($this) . '->%s' . ": cannot assign property type %s",
                $messageItems['property'],
                $messageItems['type'],
            ),
            'AttributeClassNotFound' => sprintf(
                "Attribute class not found: %s",
                $messageItems['class'],
            ),
            'AttributeNotImplementsBy' => sprintf(
                "Attribute class not implements by: %s",
                $messageItems['class'],
            ),
            'AttributeMethodNotFound' => sprintf(
                "Attribute method not found: %s",
                $messageItems['method'],
            ),
            'ClassNotFound' => sprintf(
                "Class not found: %s",
                $messageItems['class'],
            ),
            'PropertyNotInitialized' => sprintf(
                $this->toBasename($this) . '->%s: property not initialized',
                $messageItems['property'],
            ),
            'EnumValueNotSupported' => sprintf(
                $this->toBasename($this) . '->%s: value "%s" not supported',
                $messageItems['property'],
                $messageItems['value'],
            ),
            'ClassCanNotBeCasted' => sprintf(
                "Class can not be casted: %s",
                $messageItems['class'],
            ),
            'TypeForCastNotFound' => sprintf(
                "Type for cast not found: %s",
                $messageItems['type'],
            ),
            'ScalarForCastNeed' => sprintf(
                $this->toBasename($this) . '->%s: for cast need SCALAR',
                $messageItems['property'],
            ),
            'ArrayForCastNeed' => sprintf(
                $this->toBasename($this) . '->%s: for cast need ARRAY',
                $messageItems['property'],
            ),
            'TypeForCastNotSpecified' => sprintf(
                $this->toBasename($this) . '->%s: type for cast not specified',
                $messageItems['property'],
            ),

            default => 'Unknown message code',
        };
    }
}