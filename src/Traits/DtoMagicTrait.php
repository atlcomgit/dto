<?php

declare(strict_types=1);

namespace Atlcom\Traits;

use Exception;
use Throwable;

/**
 * Трейт магических методов
 */
trait DtoMagicTrait
{
    /**
     * Магический метод присвоения свойствам
     * - При заданном массиве mappings происходит поиск свойства согласно маппингу
     * - При включенной опции autoMappings или AUTO_MAPPINGS_ENABLED, поиск подменяет стили переменной camel, snake
     * - При отсутствии свойства, будет выброшено исключение в методе onException
     *
     * @param mixed $name
     * @param mixed $value
     * @return void
     * @throws Exception
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

            if (
                $mappings
                && ($toName = array_search($name, $mappings, true))
                && is_string($toName)
                && property_exists($this, $toName)
            ) {
                $this->assignValue($toName, $value);
                return;
            }

            if ($autoMappings) {
                if (
                    $mappings
                    && array_key_exists($name, $mappings)
                    && property_exists($this, $mappings[$name])
                ) {
                    $this->assignValue($mappings[$name], $value);
                    return;
                }

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

            if (static::AUTO_DYNAMIC_PROPERTIES_ENABLED) {
                $array = [$name => $value];
                $this->validateCasts($array);
                $this->assignValue($name, $array[$name]);
                return;
            }

            throw new Exception(
                $this->exceptions('PropertyNotFound', ['property' => $name]),
                500
            );

        } catch (Throwable $exception) {
            if (str_contains($exception->getMessage(), 'Cannot assign ')) {
                $type = is_object($value) ? $this->toBasename(get_class($value)) : mb_strtoupper(gettype($value));

                $this->onException(
                    new Exception(
                        $this->exceptions('PropertyAssignType', ['property' => $name, 'type' => $type]),
                        500
                    )
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
     * @throws Exception
     */
    public function __get(mixed $name): mixed
    {
        try {
            if (property_exists($this, $name)) {
                return $this->$name;
            }

            $autoMappings = $this->options()['autoMappings'];
            $mappings = $this->mappings();

            if (
                $mappings
                && ($toName = array_search($name, $mappings, true))
                && is_string($toName)
                && property_exists($this, $toName)
            ) {
                return $this->$toName;
            }

            if ($autoMappings) {
                if (
                    $mappings
                    && array_key_exists($name, $mappings)
                    && property_exists($this, $mappings[$name])
                ) {
                    return $this->{$mappings[$name]};
                }

                $keyCamelCase = $this->toCamelCase($name);
                if ($keyCamelCase && property_exists($this, $keyCamelCase)) {
                    return $this->$keyCamelCase;
                }

                $keySnakeCase = $this->toSnakeCase($name);
                if ($keySnakeCase && property_exists($this, $keySnakeCase)) {
                    return $this->$keySnakeCase;
                }
            }

            if (static::AUTO_DYNAMIC_PROPERTIES_ENABLED) {
                return $this->getCustomOption($name);
            }

            throw new Exception(
                $this->exceptions('PropertyNotFound', ['property' => $name]),
                500
            );
        } catch (Throwable $exception) {
            $this->onException($exception);
        }

        return null;
    }


    /**
     * Магический метод обращения к свойствам как к методам
     * - При заданном массиве mappings происходит поиск свойства согласно маппингу
     * - При включенной опции autoMappings или AUTO_MAPPINGS_ENABLED, поиск подменяет стили переменной camel, snake
     * - При отсутствии свойства, будет выброшено исключение в методе onException
     *
     * @param mixed $name
     * @return mixed
     */
    public function __call(string $name, array $arguments): mixed
    {
        if ($arguments && isset($arguments[0])) {
            $this->__set($name, $arguments[0]);
        }

        return $this->__get($name);
    }
}