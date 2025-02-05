<?php

namespace Atlcom\Traits;

use BackedEnum;
use Carbon\Carbon;
use DateTime;
use DateTimeInterface;
use Exception;
use ReflectionNamedType;
use ReflectionProperty;
use stdClass;
use Throwable;
use UnitEnum;

/**
 * Трейт преобразования типов
 */
trait DtoCastsTrait
{
    /**
     * Проверяет значение на соответствие типу
     *
     * @param string $key
     * @param string|array|callable $type
     * @param mixed $value
     * @return mixed
     * @throws Exception
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
                'object', 'obj' => $this->castToObject($key, stdClass::class, $value),
                'datetime', 'date' => $this->castToDateTime($value),
                'positive' => $this->castToPositive($value),
                mb_strtolower(DateTime::class) => new DateTime($value),
                mb_strtolower(Carbon::class) => Carbon::parse($value),
                mb_strtolower(DateTimeInterface::class) => $this->castToDateTime($value),

                default => match (true) {
                        is_string($type) && class_exists($type) => $this->castToObject($key, $type, $value),

                        is_array($type),
                        is_string($type) && preg_match('/array\<.*\>/', $type)
                        => $this->castToArrayOfObjects($key, $type, $value),

                        is_callable($type) => $type($value, $key),

                        default => throw new Exception(
                            $this->exceptions('TypeForCastNotFound', ['type' => $type]),
                            409
                        ),
                    },
            };
        } catch (Throwable $e) {
            throw $e;
        }
    }

    /**
     * Сериализация значения для массива
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
                onlyOptions: [
                    'autoCasts',
                    'autoMappings',
                    'onlyFilled',
                    'onlyNotNull',
                    'includeStyles',
                    'serializeKeys',
                ]
            )->toArray(),
            $value instanceof DateTimeInterface => $value->getTimestamp(),
            $value instanceof BackedEnum => $value->value,

            is_null($type) => $value,

            is_callable($type) => $type($value, $key),

            mb_strtolower($type) === Carbon::class
            => $value->toDateTimeString(),

            mb_strtolower($type) === 'libphonenumber\phonenumber'
            => 'libphonenumber\PhoneNumberUtil'::getInstance()
                ->format($value, 'libphonenumber\PhoneNumberFormat'::E164),

            is_array($value) => array_map(fn ($item) => $this->serializeValue($key, $type, $item), $value),
            is_object($value) && method_exists($value, 'toArray') => $value->toArray(),

            default => $value,
        };
    }


    /**
     * Преобразование значения к типу: object
     *
     * @param string $key
     * @param string $class
     * @param mixed $value
     * @return mixed
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
                        is_scalar($value) => (function ($class, $key, $value) {
                                try {
                                    return $class::from($value);
                                } catch (Throwable $e) {
                                    $this->onException(
                                    new Exception(
                                        $this->exceptions(
                                            'EnumValueNotSupported',
                                            ['class' => $class, 'property' => $key, 'value' => $value]
                                        ),
                                        409
                                    )
                                    );
                                }
                            })($class, $key, $value),

                        default => throw new Exception(
                            $this->exceptions('ScalarForCastNeed', ['property' => $key]),
                            409
                        ),
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
            $this->exceptions('ClassCanNotBeCasted', ['class' => $class]),
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
     *
     * @param mixed $value
     * @return array|null
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
     * 
     * @param string $key
     * @param array|string $type
     * @param mixed $value
     * @return array
     * @throws Exception
     */
    protected function castToArrayOfObjects(string $key, array|string $type, mixed $value): array
    {
        if (!property_exists($this, $key)) {
            return $value;
        }

        if (!is_array($value)) {
            throw new Exception(
                $this->exceptions('ArrayForCastNeed', ['property' => $key]),
                409
            );
        }

        $class = (is_array($type) ? $type[0] : null)
            ?: (preg_match('/array<(.*)>/', $type, $matches) ? ($matches[1] ?: null) : null)
            ?: null;

        if ($class) {
            if (!class_exists($class)) {
                throw new Exception(
                    $this->exceptions('ClassNotFound', ['class' => $class]),
                    409
                );
            }

            return method_exists($class, 'collect')
                ? $class::collect($value)
                : array_map(fn ($item) => $this->matchValue($key, $class, $item), $value);

        } else {
            throw new Exception(
                $this->exceptions('TypeForCastNotSpecified', ['property' => $key]),
                409
            );
        }
    }

    /**
     * Преобразование значения к типу: DateTime|Carbon
     *
     * @param mixed $value
     * @return DateTime|Carbon:null
     */
    protected function castToDateTime(mixed $value): mixed
    {
        return match (true) {
            is_integer($value) => match (static::AUTO_DATETIME_CLASS) {
                    Carbon::class => Carbon::createFromTimestamp($value),
                    DateTime::class, DateTimeInterface::class => (new DateTime())->setTimestamp($value),

                    default => $value,
                },

            is_float($value) => match (static::AUTO_DATETIME_CLASS) {
                    Carbon::class => Carbon::createFromTimestamp($value),
                    DateTime::class, DateTimeInterface::class => $value,

                    default => $value,
                },

            is_string($value) => match (static::AUTO_DATETIME_CLASS) {
                    Carbon::class => Carbon::parse($value),

                    DateTime::class, DateTimeInterface::class => DateTime::createFromFormat('Y-m-d H:i:s', $value)
                    ?: DateTime::createFromFormat('Y-m-d/TH:i:s', $value)
                    ?: DateTime::createFromFormat('Y.m.d H:i:s', $value)
                    ?: DateTime::createFromFormat('Y.m.d\TH:i:s', $value)
                    ?: DateTime::createFromFormat('Y-m-d', $value)
                    ?: DateTime::createFromFormat('Y.m.d', $value)
                    ?: DateTime::createFromFormat('U.u', $value)
                    ?: DateTime::createFromFormat('U', $value)
                    ?: (new DateTime())->setTimestamp(strtotime($value))
                    ?: $value,

                    default => $value,
                },

            $value instanceof Carbon => match (static::AUTO_DATETIME_CLASS) {
                    Carbon::class => $value,
                    DateTime::class, DateTimeInterface::class => $value->toDateTime(),

                    default => $value,
                },

            $value instanceof DateTimeInterface => match (static::AUTO_DATETIME_CLASS) {
                    Carbon::class => Carbon::instance($value),
                    DateTime::class, DateTimeInterface::class => DateTime::createFromInterface($value),

                    default => $value,
                },

            empty($value) || $value == '-' => null,

            default => $value,
        };
    }

    /**
     * Преобразование значения к типу с положительным значением: float|int
     *
     * @param mixed $value
     * @return float|null
     */
    protected function castToPositive(mixed $value): ?float
    {
        return match (true) {
            is_null($value) => null,
            is_integer($value) => filter_var($value, FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 0],
            ]),
            is_float($value) => filter_var($value, FILTER_VALIDATE_FLOAT, [
                'options' => ['min_range' => 0],
            ]),
            is_bool($value) => (int)$this->castToBoolean($value),

            default => (int)$value,
        };
    }


    /**
     * Возвращает массив всех свойств dto с его первым типом
     *
     * @return array
     */
    public function getCasts(): array
    {
        return static::getPropertiesWithFirstType();
    }
}