<?php

declare(strict_types=1);

namespace Atlcom\Traits;

use Atlcom\Interfaces\AttributeDtoInterface;
use Carbon\Carbon;
use DateTime;
use DateTimeInterface;
use Exception;
use ReflectionNamedType;
use ReflectionProperty;
use Throwable;

/**
 * Трейт обработки dto
 */
trait DtoCoreTrait
{
    /**
     * Преобразование данных из строки json в массив
     *
     * @param string $data
     * @return array
     */
    protected static function jsonDecode(string $data, bool $throwOnError = true): array
    {
        try {
            $array = json_decode($data, true, 512, $throwOnError ? JSON_THROW_ON_ERROR : 0) ?: [];
        } catch (Throwable $exception) {
            $array = (array)$data;

            (new static())->onException($exception);
        }

        return $array;
    }


    /**
     * Применение преобразований типов
     *
     * @param array $array
     * @return void
     */
    protected function validateCasts(array &$array): void
    {
        $casts = method_exists($this, 'casts') ? $this->casts() : [];
        $mappings = $this->mappings();
        $autoMappings = $this->options()['autoMappings'];
        !$autoMappings ?: $this->prepareStyles($casts);
        $this->prepareMappings($casts);

        foreach ($casts as $key => $cast) {
            if (
                (($keyMapped = $key) && array_key_exists($key, $array))
                || (($keyMapped = $mappings[$key] ?? null) && array_key_exists($mappings[$key], $array))
                || (($keyMapped = array_search($key, $mappings, true)) && array_key_exists($keyMapped, $array))
            ) {
                $casted = $this->matchValue($keyMapped, $cast, $array[$keyMapped] ?? null);
                $array[$keyMapped] = $casted;
                continue;
            }

            if ($autoMappings) {
                $keyCamelCase = $this->toCamelCase($key);
                if ($key !== $keyCamelCase && array_key_exists($keyCamelCase, $array)) {
                    $casted = $this->matchValue($key, $cast, $array[$keyCamelCase] ?? null);
                    $array[$keyCamelCase] = $casted;
                }

                $keySnakeCase = $this->toSnakeCase($key);
                if ($key !== $keySnakeCase && array_key_exists($keySnakeCase, $array)) {
                    $casted = $this->matchValue($key, $cast, $array[$keySnakeCase] ?? null);
                    $array[$keySnakeCase] = $casted;
                }
            }
        }
    }


    /**
     * Сериализация массива
     *
     * @param array $array
     * @return void
     */
    protected function serializeCasts(array &$array): void
    {
        $serializeKeys = $this->options()['serializeKeys'];
        $casts = method_exists($this, 'casts') ? $this->casts() : [];

        foreach ($array as $key => $value) {
            if ($this->isOptionContainKey($serializeKeys, $key)) {
                if (is_array($value)) {
                    array_walk_recursive(
                        $value,
                        fn (&$item) => $item = $this->serializeValue($key, $casts[$key] ?? null, $item)
                    );
                    $array[$key] = $value;
                } else {
                    $array[$key] = $this->serializeValue($key, $casts[$key] ?? null, $value);
                }
            }
        }
    }


    /**
     * Подготовка свойств по PSR (camelCase, snake_case)
     *
     * @param array $array
     * @param bool $forceMappings = false
     * @return void
     */
    protected function prepareStyles(array &$array, bool $forceMappings = false): void
    {
        $autoMappings = $this->options()['autoMappings'];

        if ($forceMappings || $autoMappings) {
            foreach ($array as $key => $value) {
                $keyCamelCase = $this->toCamelCase($key);
                isset($array[$keyCamelCase]) ?: $array[$keyCamelCase] = $value;

                $keySnakeCase = $this->toSnakeCase($key);
                isset($array[$keySnakeCase]) ?: $array[$keySnakeCase] = $value;
            }
        }
    }


    /**
     * Получение значения маппинга
     * 
     * @param array $array
     * @param array $pathKey
     * @param mixed $value
     * @return bool
     */
    protected function getMappingValue(array &$array, array $pathKey, mixed &$value): bool
    {
        $key = array_shift($pathKey);

        if (!array_key_exists($key, $array)) {
            return false;
        }

        if (empty($pathKey)) {
            $value = $array[$key];
            return true;
        }

        if (!is_array($array[$key])) {
            return false;
        }

        return $this->getMappingValue($array[$key], $pathKey, $value);
    }


    /**
     * Маппинг свойств
     *
     * @param array $array
     * @return void
     */
    protected function prepareMappings(array &$array): void
    {
        $autoMappings = $this->options()['autoMappings'];
        $mappings = $this->mappings();

        foreach ($mappings as $mapFrom => $mapTo) {
            $value = null;
            if (
                $mapFrom
                && is_string($mapFrom)
                && $this->getMappingValue($array, explode('.', $mapTo), $value)
                && property_exists($this, $mapFrom)
            ) {
                $array[$mapFrom] = $value;
                continue;
            }

            $value = null;
            if (
                $mapTo
                && $autoMappings
                && is_string($mapTo)
                && $this->getMappingValue($array, explode('.', $mapFrom), $value)
                && property_exists($this, $mapTo)
            ) {
                $array[$mapTo] = $value;
                continue;
            }
        }
    }


    /**
     * Присвоение значения свойству
     *
     * @param string $key
     * @param mixed $value
     * @param mixed|null $defaultValue
     * @return void
     * @throws Exception
     */
    private function assignValue(string $key, mixed $value, mixed $defaultValue = null): void
    {
        try {
            if (property_exists($this, $key)) {
                $attributes = (new ReflectionProperty(get_class($this), $key))->getAttributes();
                foreach ($attributes as $attribute) {
                    $attributeClass = $attribute->getName();
                    match (true) {
                        !class_exists($attributeClass) => false,
                        // => $this->onException(
                        //     new Exception(
                        //         $this->exceptions('AttributeClassNotFound', ['class' => $attributeClass]),
                        //         500
                        //     )
                        // ),

                        !in_array(AttributeDtoInterface::class, class_implements($attributeClass) ?: []) => false,
                        // => $this->onException(
                        //     new Exception(
                        //         $this->exceptions('AttributeNotImplementsBy', ['class' => AttributeDtoInterface::class]),
                        //         500
                        //     )
                        // ),

                        !method_exists($attributeClass, 'handle') => false,
                        // => $this->onException(
                        //     new Exception(
                        //         $this->exceptions('AttributeMethodNotFound', ['method' => "{$attributeClass}::handle"]),
                        //         500
                        //     )
                        // ),

                        default
                        => (static function () use (&$key, &$value, $defaultValue, $attribute) {
                                ($attribute->newInstance())->handle($key, $value, $defaultValue, static::class);
                            })(),
                    };
                }
            }

            $this->onAssigning($key, $value);
            $oldValue = $this->$key ?? null;

            $isDynamicProperty = static::AUTO_DYNAMIC_PROPERTIES_ENABLED && !property_exists($this, $key);
            $class = $isDynamicProperty
                ? $this->casts()[$key] ?? 'mixed'
                : (new ReflectionProperty(get_class($this), $key))->getType();
            if (
                $this->options()['autoCasts']
                && $class instanceof ReflectionNamedType
                && ($class = $class->getName()) && class_exists($class)
            ) {
                switch (true) {
                    case $class === DateTime::class:
                    case $class === DateTimeInterface::class:
                    case $class === Carbon::class:
                        $value = $this->castToDateTime($value);
                        $isDynamicProperty ? $this->setCustomOption($key, $value) : $this->$key = $value;
                        break;

                    case enum_exists($class):
                        $value =
                            match (true) {
                                is_null($value) => null,

                                $value instanceof UnitEnum => $value,

                                is_string($value) && defined("$class::$value") => constant("$class::$value"),

                                default => $class::from($value),
                            }
                            ?? match (true) {
                                is_null($defaultValue) => null,

                                $defaultValue instanceof UnitEnum => $defaultValue,

                                is_string($defaultValue) && defined("$class::$defaultValue")
                                => constant("$class::$defaultValue"),

                                default => $class::from($defaultValue),
                            }
                            ?? null;
                        $isDynamicProperty ? $this->setCustomOption($key, $value) : $this->$key = $value;
                        break;

                    case method_exists($class, 'fillFromArray'):
                        $value =
                            match (true) {
                                $value instanceof self => (new $class())->fillFromArray($value->toArray()),

                                is_array($value) => (new $class())->fillFromArray($value),

                                default => $value ?? $defaultValue,
                            };
                        $isDynamicProperty ? $this->setCustomOption($key, $value) : $this->$key = $value;
                        break;

                    default:
                        $value ??= $defaultValue;
                        $isDynamicProperty ? $this->setCustomOption($key, $value) : $this->$key = $value;
                }
            } else {
                $value ??= $defaultValue;
                $isDynamicProperty ? $this->setCustomOption($key, $value) : $this->$key = $value;
            }

            !($oldValue !== $value) ?: $this->onAssigned($key);

        } catch (Throwable $exception) {
            if (str_contains($exception->getMessage(), 'Cannot assign ')) {
                $type = is_object($value) ? $this->toBasename(get_class($value)) : mb_strtoupper(gettype($value));

                throw new Exception(
                    $this->exceptions('PropertyAssignType', ['property' => $key, 'type' => $type]),
                    500
                );
            }

            throw $exception;
        }
    }


    private function fillDto(array $array): static
    {
        try {
            $defaults = method_exists($this, 'defaults') ? $this->defaults() : [];

            $this->onFilling($array);

            $this->prepareStyles($array);
            $this->prepareMappings($defaults);
            $this->prepareMappings($array);
            $this->validateCasts($defaults);
            $this->validateCasts($array);

            foreach (get_class_vars(get_class($this)) as $key => $value) {
                $this->assignValue($key, $array[$key] ?? null, $defaults[$key] ?? $value ?? null);
                unset($array[$key]);
            }

            if (static::AUTO_DYNAMIC_PROPERTIES_ENABLED) {
                foreach ($array as $key => $value) {
                    $this->assignValue($key, $value, $defaults[$key] ?? $value ?? null);
                    unset($array[$key]);
                }
            }

            $this->onFilled($array);

        } catch (Throwable $exception) {
            $this->onException($exception);
        }

        return $this;
    }
}
