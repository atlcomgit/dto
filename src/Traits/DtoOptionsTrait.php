<?php

declare(strict_types=1);

namespace Atlcom\Traits;

use Atlcom\Exceptions\DtoException;
use ReflectionProperty;

/**
 * Трейт опций (скрытые свойства)
 * @mixin \Atlcom\Dto
 */
trait DtoOptionsTrait
{
    /**
     * Настройки для преобразования dto в массив
     *
     * @param bool|null $reset
     * @param bool|null $autoCasts
     * @param bool|null $autoMappings
     * @param bool|null $onlyFilled
     * @param bool|null $onlyNotNull
     * @param array|null $onlyKeys
     * @param bool|null $includeStyles
     * @param array|null $includeArray
     * @param array|null $excludeKeys
     * @param array|null $mappingKeys
     * @param array|string|bool|null $serializeKeys
     * @param array|string|bool|null $withProtectedKeys
     * @param array|string|bool|null $withPrivateKeys
     * @param array|string|bool|null $$withCustomOptions
     * @param bool|null $withoutOptions
     * @return array
     */
    protected function options(
        ?bool $reset = null,
        ?bool $autoCasts = null,
        ?bool $autoMappings = null,
        ?bool $onlyFilled = null,
        ?bool $onlyNotNull = null,
        ?array $onlyKeys = null,
        ?bool $includeStyles = null,
        ?array $includeArray = null,
        ?array $excludeKeys = null,
        ?array $mappingKeys = null,
        array|string|bool|null $serializeKeys = null,
        array|string|bool|null $withProtectedKeys = null,
        array|string|bool|null $withPrivateKeys = null,
        array|string|bool|null $withCustomOptions = null,
        bool|null $withoutOptions = null,
        array|null $customOptions = null,
        array|null $consts = null,
    ): array {
        static $options = [];
        $instance = md5(static::class . spl_object_id($this));

        if ($reset) {
            $customOptions ??= $options[$instance]['customOptions'] ?? [];
            $result = [];
            unset($options[$instance]);
        } else {
            $result = $options[$instance] ?? [];
        }

        is_null($autoCasts) ?: $result['autoCasts'] = $autoCasts;
        is_null($autoMappings) ?: $result['autoMappings'] = $autoMappings;
        is_null($onlyFilled) ?: $result['onlyFilled'] = $onlyFilled;
        is_null($onlyNotNull) ?: $result['onlyNotNull'] = $onlyNotNull;
        is_null($onlyKeys) ?: $result['onlyKeys'] = $onlyKeys;
        is_null($includeStyles) ?: $result['includeStyles'] = $includeStyles;
        is_null($includeArray) ?: $result['includeArray'] = $includeArray;
        is_null($excludeKeys) ?: $result['excludeKeys'] = $excludeKeys;
        is_null($mappingKeys) ?: $result['mappingKeys'] = $mappingKeys;
        is_null($serializeKeys) ?: $result['serializeKeys'] = $serializeKeys;
        is_null($withProtectedKeys) ?: $result['withProtectedKeys'] = $withProtectedKeys;
        is_null($withPrivateKeys) ?: $result['withPrivateKeys'] = $withPrivateKeys;
        is_null($withCustomOptions) ?: $result['withCustomOptions'] = $withCustomOptions;
        is_null($withoutOptions) ?: $result['withoutOptions'] = $withoutOptions;
        is_null($customOptions) ?: $result['customOptions'] = $customOptions;
        is_null($consts) ?: $result['consts'] = $consts;

        $options[$instance] = $result;

        if ($result['withoutOptions'] ?? false) {
            $result = [];
        }

        return [
            'autoCasts' => $result['autoCasts']
                ?? $result['consts']['AUTO_CASTS_ENABLED']
                ?? static::AUTO_CASTS_ENABLED,
            'autoMappings' => $result['autoMappings']
                ?? $result['consts']['AUTO_MAPPINGS_ENABLED']
                ?? static::AUTO_MAPPINGS_ENABLED,
            'onlyFilled' => $result['onlyFilled'] ?? false,
            'onlyNotNull' => $result['onlyNotNull'] ?? false,
            'onlyKeys' => $result['onlyKeys'] ?? [],
            'includeStyles' => $result['includeStyles'] ?? false,
            'includeArray' => $result['includeArray'] ?? [],
            'excludeKeys' => $result['excludeKeys'] ?? [],
            'mappingKeys' => $result['mappingKeys'] ?? [],
            'serializeKeys' => $result['serializeKeys']
                ?? $result['consts']['AUTO_SERIALIZE_ENABLED']
                ?? static::AUTO_SERIALIZE_ENABLED,
            'withProtectedKeys' => $result['withProtectedKeys'] ?? false,
            'withPrivateKeys' => $result['withPrivateKeys'] ?? false,
            'withCustomOptions' => $result['withCustomOptions']
                ?? $result['consts']['AUTO_DYNAMIC_PROPERTIES_ENABLED']
                ?? static::AUTO_DYNAMIC_PROPERTIES_ENABLED,
            'withoutOptions' => $result['withoutOptions'] ?? false,
            'customOptions' => $result['customOptions'] ?? [],
            'consts' => $result['consts'] ?? [],
        ];
    }


    /**
     * Проверка опции на содержание имени свойства
     *
     * @param mixed $option
     * @param string $key
     * @return bool
     */
    protected function isOptionContainKey(mixed $option, string $key): bool
    {
        return is_null($option)
            || ($option === true)
            || (is_array($option) && in_array($key, $option, true))
            || (is_string($option) && $option === $key);
    }


    /**
     * Устанавливает опции для преобразования dto в массив
     *
     * @param array $options
     * @param array|null $onlyOptions
     * @param array|null $excludeOptions
     * @return static
     */
    final public function setOptions(
        array $options,
        ?array $onlyOptions = null,
        ?array $excludeOptions = null,
    ): static {
        $options = array_filter(
            $options,
            static fn ($optionKey)
            => (!$onlyOptions || in_array($optionKey, $onlyOptions))
            && (!$excludeOptions || !in_array($optionKey, $excludeOptions)),
            ARRAY_FILTER_USE_KEY,
        );

        $this->options(...$options);

        return $this;
    }


    /**
     * Возвращает опции dto
     * @see ../../tests/Examples/Example37/Example37Test.php
     * 
     * @return array
     */
    final public function getOption(string $optionName): array
    {
        return $this->options()[$optionName] ?? null;
    }


    /**
     * Добавляет свои опции в dto
     * @see ../../tests/Examples/Example37/Example37Test.php
     *
     * @return static
     */
    final public function customOptions(array $options): static
    {
        $customOptions = $this->options()['customOptions'] ?? [];

        $this->options(customOptions: [...$customOptions, ...$options]);

        return $this;
    }


    /**
     * Добавляет свою опцию в dto
     *
     * @return static
     */
    final public function setCustomOption(string $optionName, mixed $optionValue): static
    {
        $this->customOptions([$optionName => $optionValue]);

        return $this;
    }


    /**
     * Возвращает значение своей опции в dto
     *
     * @return mixed
     */
    final public function getCustomOption(string $optionName, mixed $defaultValue = null): mixed
    {
        $customOptions = $this->getOption('customOptions');
        $optionName = $this->resolvePropertyName($optionName);

        return ($this->consts('AUTO_DYNAMIC_PROPERTIES_ENABLED') || isset($customOptions[$optionName]))
            ? ($customOptions[$optionName] ?? $defaultValue)
            : $this->onException(
                throw new DtoException(
                    $this->exceptions('PropertyNotFound', ['property' => $optionName]),
                    500,
                )
            )
        ;
    }


    /**
     * Включает опцию при преобразовании в массив: автоматическое преобразование типов
     *
     * @param bool $autoCasts
     * @return static
     */
    final public function autoCasts(bool $autoCasts = true): static
    {
        $this->options(autoCasts: $autoCasts);

        return $this;
    }


    /**
     * Включает опцию при заполнении в свойств: автоматическое преобразование стиля свойств
     * @see ../../tests/Examples/Example21/Example21Test.php
     *
     * @param bool $autoMappings
     * @return static
     */
    final public function autoMappings(bool $autoMappings = true): static
    {
        $this->options(autoMappings: $autoMappings);

        return $this;
    }


    /**
     * Включает опцию при преобразовании в массив: только заполненные свойства
     * @see ../../tests/Examples/Example22/Example22Test.php
     *
     * @param bool $onlyFilled
     * @return static
     */
    final public function onlyFilled(bool $onlyFilled = true): static
    {
        $this->options(onlyFilled: $onlyFilled);

        return $this;
    }


    /**
     * Включает опцию при преобразовании в массив: только не null
     *
     * @return static
     */
    final public function onlyNotNull(): static
    {
        $this->options(onlyNotNull: true);

        return $this;
    }


    /**
     * Включает опцию при преобразовании в массив: заполнить только указанными ключами
     * @see ../../tests/Examples/Example23/Example23Test.php
     *
     * @param string|array|object ...$data
     * @return static
     */
    final public function onlyKeys(string|array|object ...$data): static
    {
        $onlyKeys = $this->options()['onlyKeys'];

        foreach ($data as $key) {
            !is_object($key) ?: $key = array_keys(static::convertDataToArray($key));
            $onlyKeys = [
                ...$onlyKeys,
                ...(is_string($key)
                    ? [$key]
                    : (is_string(key($key)) ? [key($key)] : $key)
                ),
            ];
        }

        $this->options(onlyKeys: $onlyKeys);

        return $this;
    }


    /**
     * Включает опцию при преобразовании в массив: дополнить ключами в разных стилях
     * @see ../../tests/Examples/Example24/Example24Test.php
     *
     * @param bool $includeStyles
     * @return static
     */
    final public function includeStyles(bool $includeStyles = true): static
    {
        $this->options(includeStyles: $includeStyles);

        return $this;
    }


    /**
     * Включает опцию при преобразовании в массив: дополнить другим массивом
     * @see ../../tests/Examples/Example25/Example25Test.php
     *
     * @param string|array ...$data
     * @return static
     */
    final public function includeArray(string|array ...$data): static
    {
        $includeArray = $this->options()['includeArray'];

        foreach ($data as $key) {
            $includeArray = [
                ...$includeArray,
                ...(is_string($key) ? [$key] : $key),
            ];
        }

        $this->options(includeArray: $includeArray);

        return $this;
    }


    /**
     * Включает опцию при преобразовании в массив: исключить из массива указанные ключи
     * @see ../../tests/Examples/Example26/Example26Test.php
     *
     * @param string|array ...$data
     * @return static
     */
    final public function excludeKeys(string|array ...$data): static
    {
        $excludeKeys = $this->options()['excludeKeys'];

        foreach ($data as $key) {
            $excludeKeys = [
                ...$excludeKeys,
                ...(is_string($key)
                    ? [$key]
                    : (is_string(key($key)) ? [key($key)] : $key)
                ),
            ];
        }

        $this->options(excludeKeys: $excludeKeys);

        return $this;
    }


    /**
     * Включает опцию при преобразовании в массив: маппинг указанных ключей с новым именем
     * @see ../../tests/Examples/Example27/Example27Test.php
     *
     * @param string|array|object ...$data
     * @return static
     */
    final public function mappingKeys(string|array|object ...$data): static
    {
        $mappingKeys = $this->options()['mappingKeys'];

        foreach ($data as $key) {
            $mappingKeys = [
                ...$mappingKeys,
                ...(is_string($key) ? [$key] : $key),
            ];
        }

        $this->options(mappingKeys: $mappingKeys);

        return $this;
    }


    /**
     * Включает опцию при преобразовании в массив: преобразование вложенных свойств к массиву
     * @see ../../tests/Examples/Example28/Example28Test.php
     * 
     * @param string|array|object|bool ...$data
     * @return static
     */
    final public function serializeKeys(string|array|object|bool ...$data): static
    {
        $serializeKeys = $this->options()['serializeKeys'];

        foreach ($data as $key) {
            if (is_bool($key) || is_string($key)) {
                $serializeKeys = $key;
                break;
            }

            !is_object($key) ?: $key = array_keys(static::convertDataToArray($key));
            $serializeKeys = [
                ...(is_array($serializeKeys) ? $serializeKeys : []),
                ...(is_string($key)
                    ? [$key]
                    : (is_string(key($key)) ? [key($key)] : $key)
                ),
            ];
        }

        $this->options(serializeKeys: $serializeKeys);

        return $this;
    }


    /**
     * Включает опцию при преобразовании в массив: преобразование protected свойств к массиву
     * 
     * @param string|array|object|bool ...$data
     * @return static
     */
    final public function withProtectedKeys(string|array|object|bool ...$data): static
    {
        $withProtectedKeys = $this->options()['withProtectedKeys'];

        foreach ($data as $key) {
            if (is_bool($key) || is_string($key)) {
                $withProtectedKeys = $key;
                break;
            }

            !is_object($key) ?: $key = array_keys(static::convertDataToArray($key));
            $withProtectedKeys = [
                ...(is_array($withProtectedKeys) ? $withProtectedKeys : []),
                ...(is_string($key)
                    ? [$key]
                    : (is_string(key($key)) ? [key($key)] : $key)
                ),
            ];
        }

        $this->options(withProtectedKeys: $withProtectedKeys);

        return $this;
    }


    /**
     * Включает опцию при преобразовании в массив: преобразование private свойств к массиву
     * 
     * @param string|array|object|bool ...$data
     * @return static
     */
    final public function withPrivateKeys(string|array|object|bool ...$data): static
    {
        $withPrivateKeys = $this->options()['withPrivateKeys'];

        foreach ($data as $key) {
            if (is_bool($key) || is_string($key)) {
                $withPrivateKeys = $key;
                break;
            }

            !is_object($key) ?: $key = array_keys(static::convertDataToArray($key));
            $withPrivateKeys = [
                ...(is_array($withPrivateKeys) ? $withPrivateKeys : []),
                ...(is_string($key)
                    ? [$key]
                    : (is_string(key($key)) ? [key($key)] : $key)
                ),
            ];
        }

        $this->options(withPrivateKeys: $withPrivateKeys);

        return $this;
    }


    /**
     * Включает опцию при преобразовании в массив: преобразование customOptions свойств к массиву
     * 
     * @param string|array|object|bool ...$data
     * @return static
     */
    final public function withCustomOptions(string|array|object|bool ...$data): static
    {
        $withCustomOptions = $this->options()['withCustomOptions'];

        foreach ($data as $key) {
            if (is_bool($key) || is_string($key)) {
                $withCustomOptions = $key;
                break;
            }

            !is_object($key) ?: $key = array_keys(static::convertDataToArray($key));
            $withCustomOptions = [
                ...(is_array($withCustomOptions) ? $withCustomOptions : []),
                ...(is_string($key)
                    ? [$key]
                    : (is_string(key($key)) ? [key($key)] : $key)
                ),
            ];
        }

        $this->options(withCustomOptions: $withCustomOptions);

        return $this;
    }


    /**
     * Включает опцию при преобразовании в массив: только не null
     * @see ../../tests/Examples/Example36/Example36Test.php
     *
     * @return static
     */
    final public function withoutOptions(): static
    {
        $this->options(withoutOptions: true);

        return $this;
    }


    /**
     * Включает опцию при преобразовании в массив: заполнить только свойствами из указанного объекта
     *
     * @param object|string $object
     * @return static
     * @throws DtoException
     */
    final public function for(object|string $object): static
    {
        if (is_string($object)) {
            if (!class_exists($object)) {
                $this->onException(
                    new DtoException(
                        $this->exceptions('ClassNotFound', ['class' => $object]),
                        500,
                    ),
                );

                return $this;
            }

            $object = new $object();
        }

        $this
            ->includeStyles(true)
            ->mappingKeys($this->mappings())
            ->onlyKeys($object)
        ;

        return $this;
    }


    /**
     * Возвращает список классов аттрибутов у свойства
     *
     * @param string $key
     * @return array
     */
    final public function getKeyAttributes(string $key): array
    {
        $result = [];
        $attributes = (new ReflectionProperty(get_class($this), $key))->getAttributes();

        foreach ($attributes as $attribute) {
            !class_exists($attributeClass = $attribute->getName()) ?: $result[] = $attributeClass;
        }

        return $result;
    }


    /**
     * @internal
     * Сбрасывает все опции при преобразовании
     *
     * @return static
     */
    final public function reset(): static
    {
        $this->options(reset: true);

        return $this;
    }
}
