<?php

declare(strict_types=1);

namespace Atlcom\Traits;

use Atlcom\Exceptions\DtoException;
use Throwable;

/**
 * Трейт магических методов
 * @mixin \Atlcom\Dto
 */
trait DtoMagicTrait
{
    /**
     * Магический метод вызывается при создании Dto
     * @see ../../tests/Examples/Example21/Example21Test.php
     * @see ../../tests/Examples/Example39/Example39Test.php
     * @see ../../tests/Examples/Example44/Example44Test.php
     *
     * @param array|object|string|null $data
     */
    public function __construct(array|object|string|null $constructData = null)
    {
        $this->onCreating($constructData);

        is_null($constructData) ?: $this->fillFromArray(static::convertDataToArray($constructData));

        $this->onCreated($constructData);
    }


    /**
     * destruct dto
     */
    public function __destruct()
    {
        $this->reset();
    }


    /**
     * Магический метод присвоения свойствам
     * - При заданном массиве mappings происходит поиск свойства согласно маппингу
     * - При включенной опции autoMappings или AUTO_MAPPINGS_ENABLED, поиск подменяет стили переменной camel, snake
     * - При отсутствии свойства, будет выброшено исключение в методе onException
     *
     * @param mixed $name
     * @param mixed $value
     * @return void
     * @throws DtoException
     */
    public function __set(mixed $name, mixed $value): void
    {
        try {
            $autoMappings = $this->options()['autoMappings'];
            $mappings = $this->mappings();

            if (property_exists($this, $name)) {
                $this->assignValue($name, $value);

                return;
            }

            if ($mappings) {
                foreach ($mappings as $mappingKey => $mappingValues) {
                    $mappingValues = is_array($mappingValues)
                        ? array_values($mappingValues)
                        : explode('|', $mappingValues ?? '');

                    if (in_array($name, $mappingValues, true) && property_exists($this, $mappingKey)) {
                        $this->assignValue($mappingKey, $value);
                        return;
                    }
                }
            }

            if ($autoMappings) {
                $keyCamelCase = $this->toCamelCase($name);
                if ($name !== $keyCamelCase && property_exists($this, $keyCamelCase)) {
                    $this->assignValue($keyCamelCase, $value);
                    return;
                }

                $keySnakeCase = $this->toSnakeCase($name);
                if ($name !== $keySnakeCase && property_exists($this, $keySnakeCase)) {
                    $this->assignValue($keySnakeCase, $value);
                    return;
                }
            }

            if ($this->consts('AUTO_DYNAMIC_PROPERTIES_ENABLED')) {
                $array = [$name => $value];
                $this->validateCasts($array);
                $this->assignValue($name, $array[$name]);
                return;
            }

            throw new DtoException(
                $this->exceptions('PropertyNotFound', ['property' => $name]),
                500,
            );

        } catch (Throwable $exception) {
            if (str_contains($exception->getMessage(), 'Cannot assign ')) {
                $type = is_object($value) ? $this->toBasename(get_class($value)) : mb_strtoupper(gettype($value));

                $this->onException(
                    new DtoException(
                        $this->exceptions('PropertyAssignType', ['property' => $name, 'type' => $type]),
                        500,
                    ),
                );
            } else {
                $this->onException($exception);
            }
        }
    }


    /**
     * Магический метод обращения к свойствам
     * - При заданном массиве mappings происходит поиск свойства согласно маппингу
     * - При включенной опции autoMappings или AUTO_MAPPINGS_ENABLED, поиск подменяет стили переменной camel, snake
     * - При отсутствии свойства, будет выброшено исключение в методе onException
     *
     * @param mixed $name
     * @return mixed
     * @throws DtoException
     */
    public function __get(mixed $name): mixed
    {
        try {
            if (property_exists($this, $name)) {
                return isset($this->$name)
                    ? $this->$name
                    : throw new DtoException(
                        $this->exceptions('PropertyNotInitialized', ['property' => $name]),
                        500,
                    )
                ;
            }

            $autoMappings = $this->options()['autoMappings'];
            $mappings = $this->mappings();

            if ($mappings) {
                foreach ($mappings as $mappingKey => $mappingValues) {
                    $mappingValues = is_array($mappingValues)
                        ? array_values($mappingValues)
                        : explode('|', $mappingValues ?? '');

                    if (in_array($name, $mappingValues, true) && property_exists($this, $mappingKey)) {
                        return $this->$mappingKey;
                    }
                }
            }

            if (
                $mappings
                && ($toName = array_search($name, $mappings, true))
                && is_string($toName)
                && property_exists($this, $toName)
            ) {
                return $this->$toName;
            }

            if ($autoMappings) {
                $keyCamelCase = $this->toCamelCase($name);
                if ($keyCamelCase && property_exists($this, $keyCamelCase)) {
                    return $this->$keyCamelCase;
                }

                $keySnakeCase = $this->toSnakeCase($name);
                if ($keySnakeCase && property_exists($this, $keySnakeCase)) {
                    return $this->$keySnakeCase;
                }
            }

            if ($this->consts('AUTO_DYNAMIC_PROPERTIES_ENABLED')) {
                return $this->getCustomOption($name);
            }

            throw new DtoException(
                $this->exceptions('PropertyNotFound', ['property' => $name]),
                500,
            );
        } catch (Throwable $exception) {
            $this->onException($exception);
        }

        return null;
    }


    /**
     * Магический метод обращения к свойствам через метод
     * @see ../../tests/Examples/Example56/Example56Test.php
     *
     * @param mixed $name
     * @return mixed
     */
    public function __call(string $name, array $arguments): mixed
    {
        try {
            $this->consts('AUTO_PROPERTIES_AS_METHODS_ENABLED') ?: throw new DtoException(
                $this->exceptions('PropertiesAsMethodsDisabled', ['method' => __FUNCTION__, 'property' => $name]),
                500,
            );

        } catch (Throwable $exception) {
            $this->onException($exception);
        }

        if ($arguments && !empty($arguments)) {
            $this->__set($name, count($arguments) === 1 ? $arguments[0] : $arguments);

            return $this;
        }

        return $this->__get($name);
    }


    /**
     * Магический метод при вызове print_r или var_dump
     * @see ../../tests/Examples/Example55/Example55Test.php
     *
     * @return array
     */
    public function __debugInfo(): array
    {
        return [
            ...(array)$this,
            ...($this->consts('AUTO_DYNAMIC_PROPERTIES_ENABLED') ? ($this->options()['customOptions'] ?? []) : []),
        ];
    }


    /**
     * Магический метод вызывается при сериализации Dto
     * @see ../../tests/Examples/Example53/Example53Test.php
     * 
     * @return array
     */
    public function __serialize(): array
    {
        try {
            $this->consts('INTERFACE_SERIALIZABLE_ENABLED') ?: throw new DtoException(
                $this->exceptions('SerializableDisabled', ['method' => __FUNCTION__]),
                500,
            );

        } catch (Throwable $exception) {
            $this->onException($exception);
        }

        return [
            ...(array)$this,
            ...($this->consts('AUTO_DYNAMIC_PROPERTIES_ENABLED') ? ($this->options()['customOptions'] ?? []) : []),
        ];
    }


    /**
     * Магический метод вызывается при десериализации Dto
     * @see ../../tests/Examples/Example53/Example53Test.php
     *
     * @param array $data Строковое представление объекта
     * @return void
     */
    public function __unserialize(array $data): void
    {
        try {
            $this->consts('INTERFACE_SERIALIZABLE_ENABLED') ?: throw new DtoException(
                $this->exceptions('SerializableDisabled', ['method' => __FUNCTION__]),
                500,
            );

        } catch (Throwable $exception) {
            $this->onException($exception);
        }

        $this->fillDto($data);
    }
}
