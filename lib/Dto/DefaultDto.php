<?php
declare(strict_types=1);

namespace Expo\Dto;

use DateTime;
use DateTimeInterface;
use Exception;
use Expo\Dto\Interfaces\AttributeDtoInterface;
use Expo\Dto\Traits\CastTrait;
use Expo\Dto\Traits\StrTrait;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;
use Throwable;
use UnitEnum;

/**
 * Абстрактный класс dto по умолчанию
 * @abstract
 * @version 2.42
 * 
 * @override (переопределяемые методы):
 * protected function defaults(): array { return []; } - Значения по умолчанию
 * protected function mappings(): array { return []; } - Маппинг имён свойств
 * protected function casts(): array { return []; } - Трансформация типов свойств
 * 
 * protected function onFilling(array &$array): void {} - Перед заполнением
 * protected function onFilled(array $array): void {} - После заполнения
 * protected function onMerging(array &$array): void {} - Перед объединением
 * protected function onMerged(array $array): void {} - После объединения
 * protected function onSerializing(array &$array): void {} - Перед сериализацией
 * protected function onSerialized(array &$array): void {} - После сериализации
 * protected function onException(Throwable $exception): void {} - Перед исключением
 * 
 * @final
 * @method autoCasts(bool $autoCasts = true)
 * @method autoMappings(bool $autoMappings = true)
 * @method onlyFilled(bool $onlyFilled = true)
 * @method onlyKeys(string|array|object ...$data)
 * @method includeStyles(bool $includeStyles = true)
 * @method includeArray(string|array ...$data)
 * @method excludeKeys(string|array ...$data)
 * @method mappingKeys(string|array|object ...$data)
 * @method serializeKeys(string|array|object|bool ...$data)
 * @method for(object $object)
 * @method toArray(?bool $onlyFilled = null)
 * @method toJson($options = 0)
 * 
 * @example
 * ExampleDto::fill([])->onlyKeys([])->excludeKeys([])->mappingKeys([])->serializeKeys(true)->toArray();
 * 
 * @see \Expo\Dto\Tests\DtoTest
 * @see ../../README.md
 */
abstract class DefaultDto
{
    use CastTrait;
    use StrTrait;


    /** Включает опцию авто приведения типов при заполнении dto или преобразовании в массив */
    public const AUTO_CASTS_ENABLED = false;
    /** Включает опцию авто маппинг свойств при заполнении dto или преобразовании в массив */
    public const AUTO_MAPPINGS_ENABLED = false;
    /** Включает опцию авто сериализации объектов при заполнении dto или преобразовании в массив */
    public const AUTO_SERIALIZE_ENABLED = false;

    /**
     * construct
     * @version 2.30
     *
     * @param array|object|string|null $data
     */
    public function __construct(array|object|string|null $data = null)
    {
        is_null($data) ?: $this->fillFromArray(self::convertDataToArray($data));
    }

    /**
     * destruct
     * @version 2.30
     */
    public function __destruct()
    {
        $this->reset();
    }

    /**
     * Защищённые методы
     */

    /**
     * Преобразование данных в массив
     * @version 2.37
     *
     * @param mixed $data
     * @return array
     */
    final protected static function convertDataToArray(mixed $data = null): array
    {
        return match (true) {
            ($data instanceof self) => $data->toArray(),

            is_object($data) && method_exists($data, 'toArray') => $data->toArray(),
            is_string($data) => self::jsonDecode($data),
            is_array($data) => $data,
            is_object($data) => (array)$data,

            default => [],
        };
    }

    /**
     * Преобразование данных из строки json в массив
     * @version 2.37
     *
     * @param string $data
     * @return array
     */
    final protected static function jsonDecode(string $data): array
    {
        try {
            $array = json_decode($data, true, 512, JSON_THROW_ON_ERROR) ?: [];
        } catch (Throwable $exception) {
            $array = (array)$data;

            (new static())->onException($exception);
        }

        return $array;
    }

    /**
     * Применение преобразований типов
     * @version 2.41
     *
     * @param array $array
     * @return void
     */
    final protected function validateCasts(array &$array): void
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
                $keySnakeCase = $this->toSnakeCase($key);
                
                if ($key !== $keyCamelCase && array_key_exists($keyCamelCase, $array)) {
                    $casted = $this->matchValue($key, $cast, $array[$keyCamelCase] ?? null);
                    $array[$keyCamelCase] = $casted;
                }
                if ($key !== $keySnakeCase && array_key_exists($keySnakeCase, $array)) {
                    $casted = $this->matchValue($key, $cast, $array[$keySnakeCase] ?? null);
                    $array[$keySnakeCase] = $casted;
                }
            }
        }
    }

    /**
     * Сериализация массива
     * @version 2.34
     *
     * @param array $array
     * @return void
     */
    final protected function serializeCasts(array &$array): void
    {
        $serializeKeys = $this->options()['serializeKeys'];
        $casts = method_exists($this, 'casts') ? $this->casts() : [];

        foreach ($array as $key => $value) {
            if (
                is_null($serializeKeys)
                || $serializeKeys === true
                || (is_array($serializeKeys) && in_array($key, $serializeKeys, true))
            ) {
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
     * @version 2.33
     *
     * @param array $array
     * @param bool $forceMappings = false
     * @return void
     */
    final protected function prepareStyles(array &$array, bool $forceMappings = false): void
    {
        $autoMappings = $this->options()['autoMappings'];

        if ($forceMappings || $autoMappings) {
            foreach ($array as $key => $value) {
                $keyCamelCase = $this->toCamelCase($key);
                $keySnakeCase = $this->toSnakeCase($key);
    
                isset($array[$keyCamelCase]) ?: $array[$keyCamelCase] = $value;
                isset($array[$keySnakeCase]) ?: $array[$keySnakeCase] = $value;
            }
        }        
    }

    /**
     * Получение значения маппинга
     * @version 2.39
     * 
     * @param array $array
     * @param array $pathKey
     * @param mixed $value
     * @return bool
     */
    final protected function getMappingValue(array &$array, array $pathKey, mixed &$value): bool
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
     * @version 2.40
     *
     * @param array $array
     * @return void
     */
    final protected function prepareMappings(array &$array): void
    {
        $autoMappings = $this->options()['autoMappings'];
        $mappings = $this->mappings();

        foreach ($mappings as $mapFrom => $mapTo) {
            $value = null;
            if ($mapFrom
                && is_string($mapFrom)
                && $this->getMappingValue($array, explode('.', $mapTo), $value)
                && property_exists($this, $mapFrom)
            ) {
                $array[$mapFrom] = $value;
                continue;
            }

            $value = null;
            if ($mapTo
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
     * Магический метод присвоения свойствам
     * - При заданном массиве mappings происходит поиск свойства согласно маппингу
     * - При включенной опции autoMappings или AUTO_MAPPINGS_ENABLED, поиск подменяет стили переменной camel, snake
     * - При отсутствии свойства, будет выброшено исключение в методе onException
     * @version 2.42
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
                $this->$name = $value;
                return;
            }

            if ($mappings
                && ($toName = array_search($name, $mappings, true))
                && is_string($toName)
                && property_exists($this, $toName)
            ) {
                $this->$toName = $value;
                return;
            }

            if ($autoMappings) {
                if ($mappings
                    && array_key_exists($name, $mappings)
                    && property_exists($this, $mappings[$name])
                ) {
                    $this->{$mappings[$name]} = $value;
                    return;
                }
    
                $keyCamelCase = $this->toCamelCase($name);
                if ($name !== $keyCamelCase && property_exists($this, $keyCamelCase)) {
                    $this->$keyCamelCase = $value;
                    return;
                }
    
                $keySnakeCase = $this->toSnakeCase($name);
                if ($name !== $keySnakeCase && property_exists($this, $keySnakeCase)) {
                    $this->$keySnakeCase = $value;
                    return;
                }
            }

            throw new Exception(
                $this->toBasename($this) . '->' . mb_strtoupper($name) . ': свойство не найдено',
                500
            );

        } catch (Throwable $exception) {
            if (str_contains($exception->getMessage(), 'Cannot assign ')) {
                $type = is_object($value) ? $this->toBasename(get_class($value)) : mb_strtoupper(gettype($value));

                $this->onException(
                    new Exception(
                        $this->toBasename($this) . '->' . mb_strtoupper($name)
                        . ": невозможно присвоить свойству тип {$type}",
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
     * @version 2.42
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
    
            if ($mappings
                && ($toName = array_search($name, $mappings, true))
                && is_string($toName)
                && property_exists($this, $toName)
            ) {
                return $this->$toName;
            }
    
            if ($autoMappings) {
                if ($mappings
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
    
            throw new Exception(
                $this->toBasename($this) . '->' . mb_strtoupper($name) . ': свойство не найдено',
                500
            );
        } catch (Throwable $exception) {
            $this->onException($exception);
        }

        return null;
    }

    /**
     * Присвоение значения свойству
     * @version 2.41
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
            $attributes = (new ReflectionProperty(get_class($this), $key))->getAttributes();
            foreach ($attributes as $attribute) {
                $attributeClass = $attribute->getName();
                match (true) {
                    !class_exists($attributeClass)
                    => $this->onException(new Exception("Класс аттрибута не найден: {$attributeClass}", 500)),

                    !in_array(AttributeDtoInterface::class, class_implements($attributeClass) ?: [])
                    => $this->onException(
                        new Exception('Аттрибут не реализован от ' . AttributeDtoInterface::class, 500)
                    ),

                    !method_exists($attributeClass, 'handle')
                    => $this->onException(new Exception("Метод аттрибута не найден: {$attributeClass}::handle", 500)),

                    default
                    => (static function () use (&$key, &$value, $defaultValue, $attribute) {
                        ($attribute->newInstance())->handle($key, $value, $defaultValue, static::class);
                    })(),
                };
            }

            $class = (new ReflectionProperty(get_class($this), $key))->getType();
            if ($this->options()['autoCasts']
                && $class instanceof ReflectionNamedType
                && ($class = $class->getName()) && class_exists($class)
            ) {
                switch (true) {
                    case $class === DateTime::class:
                    case $class === DateTimeInterface::class:
                        $this->$key = $value = $this->castToDateTime($value);
                        break;

                    case enum_exists($class):
                        $this->$key = $value =
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
                        break;

                    case method_exists($class, 'fillFromArray'):
                        $this->$key = $value =
                            match (true) {
                                $value instanceof self => (new $class())->fillFromArray($value->toArray()),
                                is_array($value) => (new $class())->fillFromArray($value),

                                default => $value ?? $defaultValue,
                            };
                        break;

                    default:
                        $this->$key = $value ??= $defaultValue;
                }
            } else {
                $this->$key = $value ??= $defaultValue;
            }
        } catch (Throwable $exception) {
            if (str_contains($exception->getMessage(), 'Cannot assign ')) {
                $type = is_object($value) ? $this->toBasename(get_class($value)) : mb_strtoupper(gettype($value));

                throw new Exception(
                    $this->toBasename($this) . '->' . mb_strtoupper($key)
                    . ": невозможно присвоить свойству тип {$type}",
                    500
                );
            }

            throw $exception;
        }
    }

    /**
     * Публичные методы
     */

    /**
     * Создает и заполняет dto
     * @version 2.30
     *
     * @param mixed ...$data
     * @return static
     */
    final public static function create(mixed ...$data): static
    {
        $array = [];
        foreach ($data as $key => $value) {
            $array = [
                ...$array,
                ...(is_string($key) ? [$key => $value] : self::convertDataToArray($value)),
            ];
        }

        return static::fill($array);
    }

    /**
     * Преобразование в другой dto
     * @version 2.34
     *
     * @param string $dtoClass
     * @return mixed
     */
    final public function transformToDto(string $dtoClass): mixed
    {
        if (!class_exists($dtoClass)) {
            $this->onException(new Exception("Класс не найден: {$dtoClass}", 500));

            return $this;
        }

        return new $dtoClass($this);
    }

    /**
     * Статический вызов создания объекта dto
     *
     * @param array|object|string|null $data
     * @return static
     */
    final public static function fill(array|object|string|null $data = null): static
    {
        return new static($data);
    }

    /**
     * Заполнение dto из объекта
     * @version 2.30
     *
     * @param mixed $data
     * @return static
     */
    final public function fillFromData(mixed $data): static
    {
        return $this->fillFromArray(self::convertDataToArray($data));
    }

    /**
     * Заполнение dto из объекта
     *
     * @param object $data
     * @return static
     */
    final public function fillFromObject(object $data): static
    {
        return $this->fillFromData($data);
    }

    /**
     * Заполнение dto из dto
     *
     * @param self $data
     * @return static
     */
    final public function fillFromDto(self $data): static
    {
        return $this->fillFromData($data);
    }

    /**
     * Заполнение dto из json строки
     *
     * @param string $data
     * @return static
     */
    final public function fillFromJson(string $data): static
    {
        return $this->fillFromData($data);
    }

    /**
     * Заполнение dto из массива
     * @version 2.41
     *
     * @param array $array
     * @return static
     */
    final public function fillFromArray(array $array): static
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
            }

            $this->onFilled($array);
        } catch (Throwable $exception) {
            $this->onException($exception);
        }

        return $this;
    }

    /**
     * Объединить массив с dto
     * @version 2.30
     *
     * @param array|object|string|null $data
     * @return static
     * @throws Exception
     */
    final public function merge(array|object|string|null $data): static
    {
        try {
            $array = self::convertDataToArray($data);

            $this->onMerging($array);

            $this->prepareStyles($array);
            $this->prepareMappings($array);
            $this->validateCasts($array);

            foreach ($array as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->assignValue($key, $value ?? null);
                }
            }

            foreach (get_class_vars($class = get_class($this)) as $key => $value) {
                $reflection = new ReflectionProperty($class, $key);
                if (!$reflection->isInitialized($this)) {
                    throw new Exception(
                        $this->toBasename($this) . '->' . mb_strtoupper($key) . ': свойство не инициализировано',
                        500
                    );
                }
            }

            $this->onMerged($array);
        } catch (Throwable $exception) {
            $this->onException($exception);
        }

        return $this;
    }

    /**
     * Преобразует массив или коллекцию данных в коллекцию из dto
     * @version 2.19
     *
     * @param array $items
     * @return array
     */
    public static function collect(array $items): array
    {
        return array_map(static fn ($item) => static::fill($item), $items);
    }

    /**
     * Настройки для преобразования dto в массив
     * @version 2.34
     *
     * @param bool|null $reset
     * @param bool|null $autoCasts
     * @param bool|null $autoMappings
     * @param bool|null $onlyFilled
     * @param array|null $onlyKeys
     * @param bool|null $includeStyles
     * @param array|null $includeArray
     * @param array|null $excludeKeys
     * @param array|null $mappingKeys
     * @param array|bool|null $serializeKeys
     * @return array
     */
    final protected function options(
        ?bool $reset = null,
        ?bool $autoCasts = null,
        ?bool $autoMappings = null,
        ?bool $onlyFilled = null,
        ?array $onlyKeys = null,
        ?bool $includeStyles = null,
        ?array $includeArray = null,
        ?array $excludeKeys = null,
        ?array $mappingKeys = null,
        array|bool|null $serializeKeys = null,
    ): array {
        static $options = [];
        $instance = md5(static::class . spl_object_id($this));

        if ($reset) {
            unset($options[$instance]);
        }

        is_null($autoCasts) ?: $options[$instance]['autoCasts'] = $autoCasts;
        is_null($autoMappings) ?: $options[$instance]['autoMappings'] = $autoMappings;
        is_null($onlyFilled) ?: $options[$instance]['onlyFilled'] = $onlyFilled;
        is_null($onlyKeys) ?: $options[$instance]['onlyKeys'] = $onlyKeys;
        is_null($includeStyles) ?: $options[$instance]['includeStyles'] = $includeStyles;
        is_null($includeArray) ?: $options[$instance]['includeArray'] = $includeArray;
        is_null($excludeKeys) ?: $options[$instance]['excludeKeys'] = $excludeKeys;
        is_null($mappingKeys) ?: $options[$instance]['mappingKeys'] = $mappingKeys;
        is_null($serializeKeys) ?: $options[$instance]['serializeKeys'] = $serializeKeys;

        return [
            'autoCasts' => $options[$instance]['autoCasts'] ?? static::AUTO_CASTS_ENABLED,
            'autoMappings' => $options[$instance]['autoMappings'] ?? static::AUTO_MAPPINGS_ENABLED,
            'onlyFilled' => $options[$instance]['onlyFilled'] ?? false,
            'onlyKeys' => $options[$instance]['onlyKeys'] ?? [],
            'includeStyles' => $options[$instance]['includeStyles'] ?? false,
            'includeArray' => $options[$instance]['includeArray'] ?? [],
            'excludeKeys' => $options[$instance]['excludeKeys'] ?? [],
            'mappingKeys' => $options[$instance]['mappingKeys'] ?? [],
            'serializeKeys' => $options[$instance]['serializeKeys'] ?? static::AUTO_SERIALIZE_ENABLED,
        ];
    }

    /**
     * Устанавливает опции для преобразования dto в массив
     * @version 2.38
     *
     * @param array $options
     * @param array|null $onlyOptions
     * @param array|null $excludeOptions
     * @return static
     */
    final public function setOptions(
        array $options,
        ?array $onlyOptions = null,
        ?array $excludeOptions = null
    ): static {
        $options = array_filter(
            $options,
            static fn ($optionKey)
            => (!$onlyOptions || in_array($optionKey, $onlyOptions))
            && (!$excludeOptions || !in_array($optionKey, $excludeOptions)),
            ARRAY_FILTER_USE_KEY
        );

        $this->options(...$options);

        return $this;
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
     * @version 2.33
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
     * Включает опцию при преобразовании в массив: заполнить только указанными ключами
     * @version 2.30
     *
     * @param string|array|object ...$data
     * @return static
     */
    final public function onlyKeys(string|array|object ...$data): static
    {
        $onlyKeys = $this->options()['onlyKeys'];

        foreach ($data as $key) {
            !is_object($key) ?: $key = array_keys(self::convertDataToArray($key));
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
     * @version 2.36
     * 
     * @param string|array|object|bool ...$data
     * @return static
     */
    final public function serializeKeys(string|array|object|bool ...$data): static
    {
        $serializeKeys = $this->options()['serializeKeys'];

        foreach ($data as $key) {
            if (is_bool($key)) {
                $serializeKeys = $key;
                break;
            }

            !is_object($key) ?: $key = array_keys(self::convertDataToArray($key));
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
     * Включает опцию при преобразовании в массив: заполнить только свойствами из указанного объекта
     * @version 2.34
     *
     * @param object|string $object
     * @return static
     */
    final public function for(object|string $object): static
    {
        if (is_string($object)) {
            if (!class_exists($object)) {
                $this->onException(new Exception("Класс не найден: {$object}", 500));

                return $this;
            }

            $object = new $object();
        }

        $this->includeStyles(true)->serializeKeys()->onlyKeys($object);

        return $this;
    }

    /**
     * Сбрасывает все опции при преобразовании
     * @version 2.21
     *
     * @return static
     */
    final public function reset(): static
    {
        $this->options(reset: true);

        return $this;
    }

    /**
     * Преобразование dto в массив
     * @version 2.38
     *
     * @param bool|null $onlyFilled = false
     * @return array
     */
    final public function toArray(?bool $onlyFilled = null): array
    {
        $array = [];
        $this->onSerializing($array);

        $options = $this->options();
        $autoCasts = $options['autoCasts'];
        $autoMappings = $options['autoMappings'];
        $onlyFilled ??= $options['onlyFilled'];
        $onlyKeys = $options['onlyKeys'];
        $includeStyles = $options['includeStyles'];
        $includeArray = $options['includeArray'];
        $excludeKeys = $options['excludeKeys'];
        $mappingKeys = $options['mappingKeys'];
        $serializeKeys = $options['serializeKeys'];
        
        $keys = array_filter(
            (array)$this,
            static fn ($key) => !in_array($key[0], ['_', '*', ' ', CHR(0)]),
            ARRAY_FILTER_USE_KEY
        );
        !($includeStyles || $autoMappings) ?: $this->prepareStyles($keys, true);

        foreach ($keys as $key => $value) {
            if (
                $key
                && (!$onlyFilled || !empty($value))
                && (empty($onlyKeys) || in_array($key, $onlyKeys, true))
                && (empty($excludeKeys) || !in_array($key, $excludeKeys, true))
            ) {
                $array[$mappingKeys[$key] ?? $key] = $value;
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
     * @version 2.34
     *
     * @param int $options = 0
     * @return string
     */
    final public function toJson(int $options = 0): string
    {
        return json_encode($this->toArray(), $options ?: JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    /**
     * Получение хеша dto
     * @version 2.34
     *
     * @param string $keyPrefix = ''
     * @param string|null $class = null
     * @return string
     */
    final public function getHash(string $keyPrefix = '', ?string $class = null): string
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
     * Возвращает массив преобразований свойств
     *
     * @return array
     */
    protected function mappings(): array
    {
        return [];
    }

    /**
     * @override
     * Метод вызывается до заполнения dto
     *
     * @param array $array
     * @return void
     * @throws ReflectionException
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
     * @throws Exception
     */
    protected function onException(Throwable $exception): void
    {
        throw $exception;
    }
}