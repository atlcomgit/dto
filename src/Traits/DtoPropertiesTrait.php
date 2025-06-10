<?php

declare(strict_types=1);

namespace Atlcom\Traits;

use ReflectionProperty;

/**
 * Трейт свойств
 * @mixin \Atlcom\Dto
 */
trait DtoPropertiesTrait
{
    /**
     * Возвращает массив свойств dto
     * @see ../../tests/Examples/Example41/Example41Test.php
     *
     * @return array
     */
    public static function getProperties(): array
    {
        $array = [];

        foreach (array_keys(get_class_vars(static::class)) as $key) {
            if (
                !str_contains($key, '*')
                && !str_contains($key, chr(0))
                && !(new ReflectionProperty(static::class, $key))->isPrivate()
                && !(new ReflectionProperty(static::class, $key))->isProtected()
            ) {
                $array[] = $key;
            }
        }

        return $array;
    }


    /**
     * Возвращает массив всех свойств dto с его первым типом
     * @see ../../tests/Examples/Example41/Example41Test.php
     *
     * @param bool|array|null $useCasts
     * @param bool|array|null $useMappings
     * @return array
     */
    public static function getPropertiesWithFirstType(
        bool|array|null $useCasts = [],
        bool|array|null $useMappings = false,
    ): array {
        return array_map(
            static fn (array $v) => mb_strtolower($v[0]) === 'null' ? ($v[1] ?? $v[0]) : $v[0],
            static::getPropertiesWithAllTypes($useCasts, $useMappings),
        );
    }


    /**
     * Возвращает массив всех свойств dto со всеми его типами
     * @see ../../tests/Examples/Example41/Example41Test.php
     * 
     * @param bool|array|null $useCasts
     * @param bool|array|null $useMappings
     * @return array
     */
    public static function getPropertiesWithAllTypes(
        bool|array|null $useCasts = false,
        bool|array|null $useMappings = false,
    ): array {
        $array = [];
        $dto = null;
        $casts = $useCasts
            ? [...($dto ??= new static())->casts(), ...(is_array($useCasts) ? $useCasts : [])]
            : [];
        $mappings = $useMappings
            ? [...($dto ??= new static())->mappings(), ...(is_array($useMappings) ? $useMappings : [])]
            : [];

        foreach (static::getProperties() as $key) {
            $mapKey = $mappings[$key] ?? $key;
            $array[$mapKey] = match (true) {
                isset($casts[$mapKey]) => (is_array($casts[$mapKey]) ? $casts[$mapKey] : [$casts[$mapKey]]),
                isset($casts[$key]) => (is_array($casts[$key]) ? $casts[$key] : [$casts[$key]]),

                default => array_filter(
                    explode(
                        '|',
                        trim(
                            str_replace(
                                ['?', ' '],
                                ['|null|', ''],
                                (string)(new ReflectionProperty(static::class, $key))->getType() ?: 'mixed'
                            ),
                            '|',
                        ),
                    ),
                )
            };
        }

        return $array;
    }


    /**
     * Возвращает массив типов свойства dto
     * @see ../../tests/Examples/Example62/Example62Test.php
     *
     * @param string $name
     * @return array
     */
    public function getPropertyTypes(string $name): array
    {
        $name = $this->resolvePropertyName($name);

        return match (true) {
            property_exists($this, $name)
            => array_filter(
                explode(
                    '|',
                    trim(
                        str_replace(
                            ['?', ' '],
                            ['|null|', ''],
                            (string)(new ReflectionProperty(static::class, $name))->getType() ?: 'mixed'
                        ),
                        '|',
                    ),
                ),
            ),

            $this->consts('AUTO_DYNAMIC_PROPERTIES_ENABLED') === true
            => match (
                $type = gettype($this->getCustomOption($name))
                ) {
                    'null', 'NULL' => ['null'],
                    'integer', 'int' => ['int'],
                    'double', 'float' => ['float'],
                    'boolean', 'bool' => ['bool'],
                    'string' => ['string'],
                    'array' => ['array'],
                    'object' => ['object'],
                    'mixed' => ['mixed'],

                    default => [$type],
                },


            default => [],
        };
    }


    /**
     * Возвращает имя свойства после резолвинга
     *
     * @param string $name
     * @return string
     */
    public function resolvePropertyName(string $name): string
    {
        return (property_exists($this, $name) ? $name : null)
            ?? ($this->getFlipArray($this->mappings())[$name] ?? null)
            ?? (
                $this->consts('AUTO_MAPPINGS_ENABLED')
                ? ((property_exists($this, $name = $this->toCamelCase($name)) ? $name : null)
                    ?? (property_exists($this, $name = $this->toSnakeCase($name)) ? $name : null)
                )
                : null
            )
            ?? $name;
    }


    /**
     * Проверяет dto на заполнение хотя бы одного свойства
     * @see ../../tests/Examples/Example39/Example39Test.php
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        foreach (static::getPropertiesWithAllTypes() as $key => $types) {
            $value = $this->$key ?? null;
            $isEmpty = match (true) {
                is_callable($value) => false,
                $value instanceof self => $value->isEmpty(),
                is_object($value) && method_exists($value, 'isEmpty') => $value->isEmpty(),
                is_object($value) && method_exists($value, 'count') => $value->count() === 0,
                is_object($value) => empty((array)$value),
                is_array($value) => empty($value),
                is_scalar($value) =>
                    match (true) {
                        in_array('null', $types) => $value === null,

                        default => empty($value),
                    },

                default => true,
            };

            if (!$isEmpty) {
                return false;
            }
        }

        return true;
    }


    /**
     * Удаляет свойства из dto
     * @see ../../tests/Examples/Example59/Example59Test.php
     *
     * @param string|array ...$data
     * @return static
     */
    public function removeProperties(string|array ...$data): static
    {
        $removeKeys = [];

        foreach ($data as $key) {
            $removeKeys = [
                ...$removeKeys,
                ...(is_string($key)
                    ? [$key]
                    : (is_string(key($key)) ? [key($key)] : $key)
                ),
            ];
        }

        $customOptions = $this->options()['customOptions'] ?? [];

        foreach ($removeKeys as $key) {
            if (property_exists($this, $key)) {
                unset($this->$key);
            }

            if (isset($customOptions[$key])) {
                unset($customOptions[$key]);
            }
        }

        $this->options(customOptions: $customOptions);

        return $this;
    }


    /**
     * Скрывает свойства из dto
     * @see ../../tests/Examples/Example61/Example61Test.php
     *
     * @param string|array ...$data
     * @return static
     */
    public function hideProperties(string|array ...$data): static
    {
        $hideKeys = [];

        foreach ($data as $key) {
            $hideKeys = [
                ...$hideKeys,
                ...(is_string($key)
                    ? [$key]
                    : (is_string(key($key)) ? [key($key)] : $key)
                ),
            ];
        }

        $customOptions = $this->options()['customOptions'] ?? [];

        foreach ($hideKeys as $key) {
            if (property_exists($this, $key)) {
                $customOptions[$key] = $this->{$key};
                unset($this->$key);
            }
        }

        $this->options(customOptions: $customOptions);

        return $this;
    }
}
