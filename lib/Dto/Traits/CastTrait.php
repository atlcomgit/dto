<?php

namespace Atlcom\Dto\Traits;

use BackedEnum;
use DateTime;
use DateTimeInterface;
use Exception;
use JsonException;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;
use Throwable;
use UnitEnum;

/**
 * Трейт преобразования типов
 * @version 2.35
 */
trait CastTrait
{
    /**
     * Проверяет значение на соответствие типу
     * @version 2.35
     *
     * @param string $key
     * @param string|array|callable $type
     * @param mixed $value
     * @return mixed
     * @throws Throwable
     * @version 2.16
     */
    protected function matchValue(string $key, string|array|callable $type, mixed $value): mixed
    {
        try {
            return match (is_string($type) ? mb_strtolower($type) : null) {
                'boolean', 'bool' => $this->castToBoolean($value),
                'string', 'str' => $this->castToString($value),
                'integer', 'int' => $this->castToInt($value),
                'float', 'numeric' => $this->castToFloat($value),
                'array', 'arr' => $this->castToArray($value),
                'datetime', 'date' => $this->castToDateTime($value),
                'positive' => $this->castToPositive($value),
                'carbon', '\carbon\carbon', '\illuminate\support\carbon' => '\Carbon\Carbon'::parse($value),

                default => match (true) {
                        is_string($type) && class_exists($type) => $this->castToObject($key, $type, $value),

                        is_array($type),
                        is_string($type) && preg_match('/array\<.*\>/', $type)
                        => $this->castToArrayOfObjects($key, $type, $value),

                        is_callable($type) => $type($value, $key),

                        default => throw new Exception("Тип для преобразования не найден: {$type}", 409),
                    },
            };
        } catch (Throwable $e) {
            throw $e;
        }
    }

    /**
     * Сериализация значения для массива
     * @version 2.35
     *
     * @param string $key
     * @param string|array|callable|null $type
     * @param mixed $value
     * @return mixed
     */
    protected function serializeValue(string $key, string|array|callable|null $type, mixed $value): mixed
    {
        return match (true) {
            $value instanceof self => $value->setOptions(
                $this->options(),
                onlyOptions: ['autoCasts', 'autoMappings', 'onlyFilled', 'includeStyles', 'serializeKeys']
            )->toArray(),
            $value instanceof DateTimeInterface => $value->getTimestamp(),
            $value instanceof BackedEnum => $value->value,

            mb_strtolower($type) === 'carbon',
            mb_strtolower($type) === '\carbon\carbon',
            mb_strtolower($type) === '\illuminate\support\carbon'
            => $value->toDateTimeString(),

            mb_strtolower($type) === '\libphonenumber\phonenumber'
            => '\libphonenumber\PhoneNumberUtil'::getInstance()
                ->format($value, '\libphonenumber\PhoneNumberFormat'::E164),

            is_array($value) => array_map(fn ($item) => $this->serializeValue($key, $type, $item), $value),
            is_object($value) && method_exists($value, 'toArray') => $value->toArray(),

            default => $value,
        };
    }


    /**
     * Преобразование значения к типу: object
     * @version 2.33
     *
     * @param string $key
     * @param string $class
     * @param mixed $value
     * @return mixed
     * @throws ReflectionException
     * @throws Exception
     */
    protected function castToObject(string $key, string $class, mixed $value): mixed
    {
        if (!property_exists($this, $key)) {
            return $value;
        }

        $class = (new ReflectionProperty(get_class($this), $key))->getType();
        if ($class instanceof ReflectionNamedType && ($class = $class->getName())) {
            switch (true) {
                case is_null($value):
                    return null;

                case enum_exists($class):
                    return match (true) {
                        $value instanceof UnitEnum => $value,
                        is_string($value) && defined("$class::$value") => constant("$class::$value"),

                        default => $class::from($value),
                    };

                default:
                if (class_exists($class)) {
                    $object = new $class();
                    if ($object instanceof self && method_exists($object, 'fillFromArray')) {
                        $value = (is_object($value) && $value instanceof self)
                            ? $value->serializeKeys(true)->toArray()
                            : (is_array($value) ? $value : null);
                        !is_null($value) ? $object->fillFromArray($value) : $object = $value;
                    } else {
                        $object = $value;
                    }
                } else {
                    $object = $value;
                }

                return $object;
        }
        }

        throw new Exception(
            basename($this) . ': ' . 'класс для преобразования ' .
            basename($class) . ' не поддерживается в CastTrait',
            409
        );
    }

    /**
     * Преобразование значения к типу: boolean
     *
     * @param mixed $value
     * @return bool|null
     */
    protected function castToBoolean(mixed $value): ?bool
    {
        return !is_null($value) ? filter_var($value, FILTER_VALIDATE_BOOLEAN) : null;
    }

    /**
     * Преобразование значения к типу: string
     * @version 2.29
     *
     * @param mixed $value
     * @return string|null
     */
    protected function castToString(mixed $value): ?string
    {
        return match (true) {
            is_null($value) => null,
            $value instanceof self => json_encode(
                $value->serializeKeys()->toArray(),
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            ),
            is_object($value) && method_exists($value, 'toArray') => $value->toArray(),
            is_array($value), is_object($value) => json_encode(
                $value,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            ),

            default => (string)$value,
        };
    }

    /**
     * Преобразование значения к типу: integer
     *
     * @param mixed $value
     * @return int|null
     */
    protected function castToInt(mixed $value): ?int
    {
        return !is_null($value) ? (int)$value : null;
    }

    /**
     * Преобразование значения к типу: float
     *
     * @param mixed $value
     * @return float|null
     */
    protected function castToFloat(mixed $value): ?float
    {
        return !is_null($value) ? (float)$value : null;
    }

    /**
     * Преобразование значения к типу: array
     * @version 2.32
     *
     * @param mixed $value
     * @return array|null
     * @throws JsonException
     */
    protected function castToArray(mixed $value): array|null
    {
        return match (true) {
            is_null($value) => null,
            $value instanceof self => $value->serializeKeys()->toArray(),
            is_string($value) => self::jsonDecode($value),
            is_object($value) && method_exists($value, 'toArray') => $value->toArray(),
            is_array($value) => $value,

            default => (array)$value,
        };
    }

    /**
     * Преобразование значения к типу: array<type>
     * @version 2.33
     * 
     * @param string $key
     * @param array|string $type
     * @param mixed $value
     * @return array
     */
    protected function castToArrayOfObjects(string $key, array|string $type, mixed $value): array
    {
        if (!property_exists($this, $key)) {
            return $value;
        }

        if (!is_array($value)) {
            throw new Exception(
                basename($this::class) . '->' . mb_strtoupper($key) . ': для преобразования требуется ARRAYABLE',
                409
            );
        }

        $class = (is_array($type) ? $type[0] : null)
            ?: (preg_match('/array<(.*)>/', $type, $matches) ? ($matches[1] ?: null) : null)
            ?: null;

        if ($class) {
            if (!class_exists($class)) {
                throw new Exception(
                    basename($this::class) . '->' . mb_strtoupper($key) . ": не найден класс для преобразования {$class}",
                    409
                );
            }

            return method_exists($class, 'collect')
                ? $class::collect($value)
                : array_map(fn ($item) => $this->matchValue($key, $class, $item), $value);

        } else {
            throw new Exception(
                basename($this::class) . '->' . mb_strtoupper($key) . ': не указан тип для преобразования',
                409
            );
        }
    }

    /**
     * Преобразование значения к типу: datetime
     * @version 2.31
     *
     * @param mixed $value
     * @return DateTime|null
     */
    protected function castToDateTime(mixed $value): DateTime|null
    {
        return match (true) {
            is_int($value) => DateTime::createFromFormat('U', $value),
            is_float($value) => DateTime::createFromFormat('U.u', $value),
            is_string($value) => DateTime::createFromFormat('Y-m-d H:i:s', $value)
                ?: DateTime::createFromFormat('Y-m-d/TH:i:s', $value)
                ?: DateTime::createFromFormat('Y.m.d H:i:s', $value)
                ?: DateTime::createFromFormat('Y.m.d\TH:i:s', $value)
                ?: DateTime::createFromFormat('Y-m-d', $value)
                ?: DateTime::createFromFormat('Y.m.d', $value)
                ?: null,
            $value instanceof DateTimeInterface => DateTime::createFromInterface($value),
            empty($value) => null,
            default => $value,
        };
    }

    /**
     * Преобразование значения к типу с положительным значением: float|int
     * @version 2.29
     *
     * @param mixed $value
     * @return float|null
     */
    protected function castToPositive(mixed $value): ?float
    {
        return match (true) {
            is_null($value) => null,
            is_integer($value) => filter_var($value, FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 0]
            ]),
            is_float($value) => filter_var($value, FILTER_VALIDATE_FLOAT, [
                'options' => ['min_range' => 0]
            ]),
            is_bool($value) => (int)$this->castToBoolean($value),

            default => (int)$value,
        };
    }
}