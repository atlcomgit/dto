<?php

declare(strict_types=1);

namespace Atlcom\Traits;

use Atlcom\Attributes\Hidden;
use ReflectionProperty;

/**
 * Трейт сериализации
 */
trait DtoSerializeTrait
{
    /**
     * Преобразование dto в массив
     *
     * @param bool|null $onlyFilled = false
     * @param bool|null $onlyNotNull
     * @param array|null $onlyKeys
     * @param array|null $excludeKeys
     * @param array|null $mappingKeys
     * @return array
     */
    final public function toArray(
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

        !($includeStyles || $autoMappings) ?: $this->prepareStyles($keys, true);

        $mappingKeysFlip = array_flip($mappingKeys);
        foreach ($keys as $key => $value) {
            if (
                $key
                && (!$onlyFilled || !empty($value))
                && (!$onlyNotNull || !is_null($value))
                && (
                    empty($onlyKeys)
                    || in_array($mappingKeys[$key] ?? null, $onlyKeys, true)
                    || in_array($mappingKeysFlip[$key] ?? null, $onlyKeys, true)
                    || in_array($key, $onlyKeys, true)
                )
                && (
                    empty($excludeKeys)
                    || !(
                        in_array($mappingKeys[$key] ?? null, $excludeKeys, true)
                        || in_array($mappingKeysFlip[$key] ?? null, $excludeKeys, true)
                        || in_array($key, $excludeKeys, true)
                    )
                )
            ) {
                $key = $mappingKeys[$key] ?? $key;
                $attributes = property_exists($this, $key)
                    ? $this->getKeyAttributes($key)
                    : (
                        (isset($mappingKeys[$key]) && property_exists($this, $mappingKeys[$key]))
                        ? $this->getKeyAttributes($mappingKeys[$key])
                        : []
                    );
                in_array(Hidden::class, $attributes) ?: $array[$key] = $value;
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
     *
     * @param int $options = 0
     * @return string
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options ?: JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }


    /**
     * Возвращает массив с пустыми значениями всех свойств dto
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
                $allValuesToNull
            );
        }

        return $array;
    }


    /**
     * Возвращает массив с пустыми значениями всех свойств dto с рекурсией по объектам
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
                $allValuesToNull
            );
        }

        return $array;
    }


    /**
     * Получение хеша dto
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
            . ':' . hash('sha256', $keyPrefix . $class . json_encode($array)),
            ':'
        );
    }
}