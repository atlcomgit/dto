<?php

declare(strict_types=1);

namespace Atlcom\Traits;

use Atlcom\Attributes\Hidden;
use ReflectionProperty;

/**
 * Трейт сериализации
 * @mixin \Atlcom\Dto
 */
trait DtoSerializeTrait
{
    /**
     * Преобразование dto в массив
     * @see ../../tests/Examples/Example26/Example26Test.php
     * @see ../../tests/Examples/Example29/Example29Test.php
     *
     * @param bool|null $onlyFilled = false
     * @param bool|null $onlyNotNull
     * @param array|null $onlyKeys
     * @param array|null $excludeKeys
     * @param array|null $mappingKeys
     * @return array
     */
    public function toArray(
        ?bool $onlyFilled = null,
        ?bool $onlyNotNull = null,
        ?array $onlyKeys = null,
        ?array $excludeKeys = null,
        ?array $mappingKeys = null,
    ): array {
        $array = [];
        $this->onSerializing($array);

        $options = $this->options();
        $autoCasts = $options['autoCasts'];
        $autoMappings = $options['autoMappings'];
        $onlyFilled ??= $options['onlyFilled'];
        $onlyNotNull ??= $options['onlyNotNull'];
        $onlyKeys ??= $options['onlyKeys'];
        $includeStyles = $options['includeStyles'];
        $includeArray = $options['includeArray'];
        $excludeKeys ??= $options['excludeKeys'];
        $mappingKeys ??= $options['mappingKeys'];
        $serializeKeys = $options['serializeKeys'];
        $withProtectedKeys = $options['withProtectedKeys'];
        $withPrivateKeys = $options['withPrivateKeys'];
        $withCustomOptions = $options['withCustomOptions'];
        $customOptionsArray = $options['customOptions'];

        $keys = [];
        foreach ([...(array)$this, ...($withCustomOptions ? ($customOptionsArray ?? []) : [])] as $key => $value) {
            $keyParts = explode(chr(0), $key);
            $scope = $keyParts[1] ?? '';
            $key = $keyParts[2] ?? $keyParts[0];

            if (
                ($scope === '')
                || ($scope === '*' && $this->isOptionContainKey($withProtectedKeys, $key))
                || ($scope !== '*' && $this->isOptionContainKey($withPrivateKeys, $key))
            ) {
                $keys[$key] = $value;
            }
        }

        !($autoMappings && !$onlyKeys) ?: $onlyKeys = static::getProperties();
        !($includeStyles || $autoMappings) ?: $this->prepareStyles($keys, true);

        $mappingKeysFlip = $this->getFlipArray($mappingKeys);
        foreach ($keys as $key => $value) {
            if (
                $key
                && (!$onlyFilled || !empty($value))
                && (!$onlyNotNull || !is_null($value))
                && (
                    empty($onlyKeys)
                    || in_array($key, $onlyKeys, true)
                    || (is_string($mapKey = $mappingKeys[$key] ?? null) && in_array($mapKey, $onlyKeys, true))
                    || in_array($mappingKeysFlip[$key] ?? null, $onlyKeys, true)
                )
                && (
                    empty($excludeKeys)
                    || !(
                        in_array($key, $excludeKeys, true)
                        || (is_string($mapKey = $mappingKeys[$key] ?? null) && in_array($mapKey, $excludeKeys, true))
                        || in_array($mappingKeysFlip[$key] ?? null, $excludeKeys, true)
                    )
                )
            ) {
                $mappingValues = $mappingKeys[$key] ?: $key;
                $mappingValues = is_array($mappingValues)
                    ? array_values($mappingValues)
                    : explode('|', $mappingValues ?? '');

                foreach ($mappingValues as $mappingValue) {
                    $key = $mappingValue;

                    $attributes = property_exists($this, $key)
                        ? $this->getKeyAttributes($key)
                        : [];
                    in_array(Hidden::class, $attributes) ?: $array[$key] = $value;
                }
            }
        }

        $array = [...$array, ...$includeArray];
        !($autoCasts || $serializeKeys) ?: $this->serializeCasts($array);

        $this->onSerialized($array);
        $this->reset();

        return $array;
    }


    /**
     * Преобразование dto в json
     * @see ../../tests/Examples/Example30/Example30Test.php
     *
     * @param int|string $options = 0
     * @return string
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), (int)$options ?: JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }


    /**
     * Возвращает массив с пустыми значениями всех свойств dto
     * @see ../../tests/Examples/Example40/Example40Test.php
     * 
     * @param bool $allValuesToNull
     * @return array
     */
    public static function toArrayBlank(bool $allValuesToNull = true): array
    {
        $array = [];

        foreach (static::getProperties() as $key) {
            $array[$key] = static::convertTypeToEmptyValue(
                (string)(new ReflectionProperty(static::class, $key))->getType() ?: 'mixed',
                false,
                $allValuesToNull,
            );
        }

        return $array;
    }


    /**
     * Возвращает массив с пустыми значениями всех свойств dto с рекурсией по объектам
     * @see ../../tests/Examples/Example40/Example40Test.php
     * 
     * @param bool $allValuesToNull
     * @return array
     */
    public static function toArrayBlankRecursive(bool $allValuesToNull = true): array
    {
        $array = [];

        foreach (static::getProperties() as $key) {
            $array[$key] = static::convertTypeToEmptyValue(
                (string)(new ReflectionProperty(static::class, $key))->getType() ?: 'mixed',
                true,
                $allValuesToNull,
            );
        }

        return $array;
    }


    /**
     * Получение хеша dto
     * @see ../../tests/Examples/Example38/Example38Test.php
     *
     * @param string $keyPrefix = ''
     * @param string|null $class = null
     * @return string
     */
    public function getHash(string $keyPrefix = '', ?string $class = null): string
    {
        $class ??= get_class($this);
        $array = $this->toArray();
        asort($array);

        return ltrim(
            $keyPrefix
            . ':' . $this->toBasename($class)
            . ':' . hash('xxh128', $keyPrefix . $class . json_encode($array)),
            ':',
        );
    }
}
