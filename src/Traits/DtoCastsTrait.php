<?php

declare(strict_types=1);

namespace Atlcom\Traits;

use Atlcom\Exceptions\DtoException;
use BackedEnum;
use Carbon\Carbon;
use DateTime;
use DateTimeInterface;
use ReflectionProperty;
use stdClass;
use Throwable;
use UnitEnum;

/**
 * Трейт преобразования типов
 * @mixin \Atlcom\Dto
 */
trait DtoCastsTrait
{
    /**
     * @internal
     * Проверяет значение на соответствие типу
     * @see ../../tests/Examples/Example06/Example06Test.php
     * @see ../../tests/Examples/Example08/Example08Test.php
     * @see ../../tests/Examples/Example09/Example09Test.php
     * @see ../../tests/Examples/Example10/Example10Test.php
     * @see ../../tests/Examples/Example31/Example31Test.php
     * @see ../../tests/Examples/Example35/Example35Test.php
     * @see ../../tests/Examples/Example41/Example41Test.php
     * @see ../../tests/Examples/Example43/Example43Test.php
     * @see ../../tests/Examples/Example46/Example46Test.php
     * @see ../../tests/Examples/Example63/Example63Test.php
     *
     * @param string $key
     * @param string|array|callable $type
     * @param mixed $value
     * @return mixed
     * @throws DtoException
     */
    protected function matchValue(string $key, string|array|callable $type, mixed $value): mixed
    {
        try {
            $canNull = in_array('null', $this->getPropertyTypes($key));

            return match (true) {
                // is_null($value) => null, - не нужен
                // $type === gettype($value) => $value,
                // $type === DateTime::class => new DateTime($value),

                is_object($value) && $type === $value::class => $value,

                is_string($type) && in_array($type, [Carbon::class, DateTime::class, DateTimeInterface::class])
                => $this->castToDateTime($value, $type, $canNull),

                default =>
                    match (is_string($type) ? mb_strtolower($type) : null) {
                        'boolean', 'bool' => $this->castToBoolean($value, $canNull),
                        'string', 'str' => $this->castToString($value, $canNull),
                        'integer', 'int' => $this->castToInt($value, $canNull),
                        'float', 'numeric' => $this->castToFloat($value, $canNull),
                        'array', 'arr' => $this->castToArray($value, $canNull),
                        'object', 'obj' => $this->castToObject($key, stdClass::class, $value, $canNull),
                        'datetime', 'date' => $this->castToDateTime($value, $type, $canNull),
                        'positive' => $this->castToPositive($value, $canNull),
                        'mixed', 'any' => $value,

                        default =>
                            match (true) {
                                is_string($type) && class_exists($type)
                                => $this->castToObject($key, $type, $value, $canNull),
                                is_array($type),
                                is_string($type) && preg_match('/array\<.*\>/', $type)
                                => $this->castToArrayOfObjects($key, $type, $value),
                                is_callable($type) => $type($value, $key),
                                is_object($value) && $value instanceof $type => $value,
                                is_object($value) && is_subclass_of($value, $type) => $value,

                                default => throw new DtoException(
                                    $this->exceptions('TypeForCastNotFound', ['property' => $key, 'type' => $type]),
                                    500,
                                ),
                            },
                    }
            };
        } catch (Throwable $exception) {
            $this->onException($exception);
        }

        return $value;
    }


    /**
     * @internal
     * Сериализация значения для массива
     *
     * @param string $key
     * @param string|array|callable|null $type
     * @param mixed|\Atlcom\Dto $value
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
                ],
            )->toArray(),
            $value instanceof DateTimeInterface => $value->getTimestamp(),
            $value instanceof BackedEnum => $value->value,
            is_null($type) => $value,
            is_callable($type) => $type($value, $key),
            is_string($type) && mb_strtolower($type) === Carbon::class => $value->toDateTimeString(),
            is_string($type) && mb_strtolower($type) === 'libphonenumber\phonenumber'
            => 'libphonenumber\PhoneNumberUtil'::getInstance()
                ->format($value, 'libphonenumber\PhoneNumberFormat'::E164),
            is_array($value) => array_map(fn ($item) => $this->serializeValue($key, $type, $item), $value),
            is_object($value) => match (true) {
                    method_exists($value, 'toArray') => $value->toArray(),
                    method_exists($value, 'all') => $value->all(),

                    default => $value,
                },

            default => $value,
        };
    }


    /**
     * @internal
     * Преобразование значения к типу: object
     *
     * @param string $key
     * @param string $class
     * @param mixed $value
     * @param bool $canNull
     * @return mixed
     * @throws DtoException
     */
    protected function castToObject(string $key, string $class, mixed $value, bool $canNull = false): mixed
    {
        if (!property_exists($this, $key)) {
            return $value;
        }

        try {
            switch (true) {
                case is_object($value) && $value::class === $class:
                    return $value;

                case is_object($value) && $value instanceof $class:
                    return $value;

                case is_null($value) && $canNull:
                    return null;

                case $value === '' && $canNull && $this->consts('AUTO_EMPTY_STRING_TO_NULL_ENABLED'):
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
                                    new DtoException(
                                        $this->exceptions(
                                            'EnumValueNotSupported',
                                            ['class' => $class, 'property' => $key, 'value' => $value],
                                        ),
                                        409,
                                    ),
                                    );
                                }
                            })($class, $key, $value),

                        default => $value,
                        // default => throw new DtoException(
                        //     $this->exceptions('ScalarForCastNeed', ['property' => $key]),
                        //     409,
                        // ),
                    };

                default:
                    $laravelClassCollection = 'Illuminate\Support\Collection';
                    $laravelClassModel = 'Illuminate\Database\Eloquent\Model';
                    $laravelClassFormRequest = 'Illuminate\Foundation\Http\FormRequest';
                    $laravelClassRequest = 'Illuminate\Http\Request';

                    switch (true) {
                        case is_subclass_of($class, self::class):
                            $value = (is_object($value) && $value instanceof self)
                                ? $value->serializeKeys(true)->toArray()
                                : (is_array($value) ? $value : null);
                            $object = !is_null($value) ? $class::create($value) : $value;
                            break;

                        case is_array($value)
                        && (
                        $class === $laravelClassCollection
                        || is_subclass_of($class, $laravelClassCollection)
                        || $class === $laravelClassModel
                        || is_subclass_of($class, $laravelClassModel)
                        || $class === $laravelClassFormRequest
                        || is_subclass_of($class, $laravelClassFormRequest)
                        || $class === $laravelClassRequest
                        || is_subclass_of($class, $laravelClassRequest)
                        ):
                            $object = new $class($value);
                            break;

                        case is_array($value):
                            try {
                                $object = new $class($value);
                            } catch (Throwable $exception) {
                                try {
                                    $object = new $class(...$value);
                                } catch (Throwable $exception) {
                                    $object = $value;
                                }
                            }
                            break;

                        default:
                            $object = null;
                            $keyTypes = static::getPropertiesWithAllTypes(useMappings: true)[$key] ?? [];
                            $valueType = mb_strtolower(gettype($value));

                            foreach ($keyTypes as $keyType) {
                                if (
                                    mb_strtolower($keyType) === $valueType
                                    || (is_object($value) && $value instanceof $keyType)
                                ) {
                                    $object = $value;
                                    break;
                                }
                            }
                            
                            try {
                                !is_null($object) ?: $object = new $class();
                            } catch (Throwable $exception) {
                                $object = $value;
                            }
                    }

                    return $object;
            }

        } catch (Throwable $exception) {
            $this->onException(
                $exception instanceof DtoException
                ? throw $exception
                : throw new DtoException(
                    $this->exceptions('ClassCanNotBeCasted', ['class' => $class]),
                    500,
                )
            );
        }

        return $value;
    }


    /**
     * @internal
     * Преобразование значения к типу: boolean
     * @see ../../tests/Examples/Example63/Example63Test.php
     *
     * @param mixed $value
     * @param bool $canNull
     * @return mixed
     */
    protected function castToBoolean(mixed $value, bool $canNull = false): mixed
    {
        return match (true) {
            is_null($value) => $canNull ? null : false,
            is_bool($value) => $value,
            $value === '' => ($canNull && $this->consts('AUTO_EMPTY_STRING_TO_NULL_ENABLED')) ? null : false,
            in_array($value, ['true', 'True', 'TRUE', true, '1', 1], true) => true,
            in_array($value, ['false', 'False', 'FALSE', false, '0', 0, null], true) => false,
            is_numeric($value) => (bool)$value,
            is_callable($value) => static::castToBoolean($value(), $canNull),

            default => $value,
        };
    }


    /**
     * @internal
     * Преобразование значения к типу: string
     * @see ../../tests/Examples/Example63/Example63Test.php
     *
     * @param mixed $value
     * @param bool $canNull
     * @return mixed
     */
    protected function castToString(mixed $value, bool $canNull = false): mixed
    {
        return match (true) {
            is_null($value) => $canNull ? null : '',
            $value === '' => ($canNull && $this->consts('AUTO_EMPTY_STRING_TO_NULL_ENABLED')) ? null : '',
            $value instanceof self => json_encode(
                $value->serializeKeys()->toArray(),
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            ),
            is_array($value), is_object($value)
            => json_encode(
                    match (true) {
                        method_exists($value, 'toArray') => $value->toArray(),
                        method_exists($value, 'all') => $value->all(),

                        default => $value,
                    },
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            ),

            default => (string)$value,
        };
    }


    /**
     * @internal
     * Преобразование значения к типу: integer
     * @see ../../tests/Examples/Example63/Example63Test.php
     *
     * @param mixed $value
     * @param bool $canNull
     * @return mixed
     */
    protected function castToInt(mixed $value, bool $canNull = false): mixed
    {
        $a = $this->consts('AUTO_EMPTY_STRING_TO_NULL_ENABLED');
        return match (true) {
            is_null($value) => $canNull ? null : 0,
            is_integer($value) => $value,
            $value === '' => ($canNull && $this->consts('AUTO_EMPTY_STRING_TO_NULL_ENABLED')) ? null : 0,
            is_numeric($value) => (int)$value,
            is_callable($value) => static::castToInt($value(), $canNull),

            default => $value,
        };
    }


    /**
     * @internal
     * Преобразование значения к типу: float
     * @see ../../tests/Examples/Example63/Example63Test.php
     *
     * @param mixed $value
     * @param bool $canNull
     * @return mixed
     */
    protected function castToFloat(mixed $value, bool $canNull = false): mixed
    {
        return match (true) {
            is_null($value) => $canNull ? null : 0.0,
            is_float($value) => $value,
            $value === '' => ($canNull && $this->consts('AUTO_EMPTY_STRING_TO_NULL_ENABLED')) ? null : 0.0,
            is_numeric($value) => (float)$value,
            is_callable($value) => static::castToFloat($value(), $canNull),

            default => $value,
        };
    }


    /**
     * @internal
     * Преобразование значения к типу: array
     * @see ../../tests/Examples/Example63/Example63Test.php
     *
     * @param mixed $value
     * @param bool $canNull
     * @return array|null
     */
    protected function castToArray(mixed $value, bool $canNull = false): array|null
    {
        return match (true) {
            is_null($value) => $canNull ? null : [],
            $value instanceof self => $value->serializeKeys()->toArray(),
            $value === '' => ($canNull && $this->consts('AUTO_EMPTY_STRING_TO_NULL_ENABLED')) ? null : [],
            is_string($value) => self::jsonDecode($value, true),
            is_object($value) => match (true) {
                    method_exists($value, 'toArray') => $value->toArray(),
                    method_exists($value, 'all') => $value->all(),

                    default => (array)$value,
                },
            is_array($value) => $value,

            default => (array)$value,
        };
    }


    /**
     * @internal
     * Преобразование значения к типу: array<type>|Collection<type>
     * @see ../../tests/Examples/Example63/Example63Test.php
     * 
     * @param string $key
     * @param array|string $type
     * @param mixed $value
     * @return array|object
     * @throws DtoException
     */
    protected function castToArrayOfObjects(string $key, array|string $type, mixed $value): array|object
    {
        if (!property_exists($this, $key)) {
            return $value;
        }

        $value = is_object($value)
            ? match (true) {
                method_exists($value, 'toArray') => $value->toArray(),
                method_exists($value, 'all') => $value->all(),

                default => $value,
            }
            : ($value ?? []);

        if (!is_array($value)) {
            $this->onException(
                new DtoException(
                    $this->exceptions('ArrayForCastNeed', ['property' => $key]),
                    409,
                ),
            );
        }

        $class = (is_array($type) ? $type[0] : null)
            ?: (preg_match('/array<(.*)>/', $type, $matches) ? ($matches[1] ?: null) : null)
            ?: null;

        if ($class) {
            if (!class_exists($class)) {
                $this->onException(
                    throw new DtoException(
                        $this->exceptions('ClassNotFound', ['class' => $class]),
                        500,
                    )
                );
            }

            $items = method_exists($class, 'collect')
                ? $class::collect($value)
                : array_map(fn ($item) => $this->matchValue($key, $class, $item), $value);

            $keyClass = (new ReflectionProperty(get_class($this), $key))?->getType()?->getName() ?: 'mixed';
            $keyClass = explode('|', str_replace(['?', ' '], ['', ''], $keyClass))[0];

            return match (true) {
                $keyClass !== 'array' && class_exists($keyClass) => new $keyClass($items),

                default => $items,
            };

        } else {
            $this->onException(
                new DtoException(
                    $this->exceptions('TypeForCastNotSpecified', ['property' => $key]),
                    500,
                ),
            );
        }

        return [];
    }


    /**
     * @internal
     * Преобразование значения к типу: DateTime|Carbon
     * @see ../../tests/Examples/Example63/Example63Test.php
     *
     * @param mixed $value
     * @param string $type
     * @param bool $canNull
     * @return mixed
     */
    protected function castToDateTime(mixed $value, string $type, bool $canNull = false): mixed
    {
        $type = in_array($type, [Carbon::class, DateTime::class, DateTimeInterface::class])
            ? $type
            : $this->consts('AUTO_DATETIME_CLASS');

        return match (true) {
            is_null($value) => $canNull
            ? null
            : match ($type) {
                    Carbon::class => Carbon::now(),
                    DateTime::class, DateTimeInterface::class => new DateTime(),

                    default => $value,
                },

            $value === '' || $value == '-' => ($canNull && $this->consts('AUTO_EMPTY_STRING_TO_NULL_ENABLED'))
            ? null
            : match ($type) {
                    Carbon::class => Carbon::now(),
                    DateTime::class, DateTimeInterface::class => new DateTime(),

                    default => $value,
                },

            is_integer($value) => match ($type) {
                    Carbon::class => Carbon::createFromTimestamp($value),
                    DateTime::class, DateTimeInterface::class => (new DateTime())->setTimestamp($value),

                    default => $value,
                },

            is_float($value) => match ($type) {
                    Carbon::class => Carbon::createFromTimestamp($value),
                    DateTime::class, DateTimeInterface::class => $value,

                    default => $value,
                },

            is_string($value) => match ($type) {
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

            $value instanceof Carbon => match ($type) {
                    Carbon::class => $value,
                    DateTime::class, DateTimeInterface::class => $value->toDateTime(),

                    default => $value,
                },

            $value instanceof DateTimeInterface => match ($type) {
                    Carbon::class => Carbon::instance($value),
                    DateTime::class, DateTimeInterface::class => DateTime::createFromInterface($value),

                    default => $value,
                },

            default => $value,
        };
    }


    /**
     * @internal
     * Преобразование значения к типу с положительным значением: float|int
     *
     * @param mixed $value
     * @param bool $canNull
     * @return mixed
     */
    protected function castToPositive(mixed $value, bool $canNull = false): mixed
    {
        return match (true) {
            is_null($value) => $canNull ? null : 0,
            $value === '' => ($canNull && $this->consts('AUTO_EMPTY_STRING_TO_NULL_ENABLED')) ? null : 0,
            is_integer($value) && is_numeric($value) => filter_var($value, FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 0],
            ]),
            is_float($value) && is_numeric($value) => filter_var($value, FILTER_VALIDATE_FLOAT, [
                'options' => ['min_range' => 0],
            ]),
            is_bool($value) => (int)$this->castToBoolean($value, $canNull),

            default => $value,
        };
    }


    /**
     * Возвращает массив для преобразований типов по умолчанию
     * @see ../../tests/Examples/Example63/Example63Test.php
     *
     * @return array
     */
    protected function castDefault(): array
    {
        $laravelClassCollection = 'Illuminate\Support\Collection';

        return $this->consts('AUTO_CASTS_OBJECTS_ENABLED')
            ? array_filter(
                array_map(
                    static fn (string $type) => match (true) {
                        $type === $laravelClassCollection || is_subclass_of($type, $laravelClassCollection)
                        => static fn ($v) => new $laravelClassCollection($v),

                        default => match ($type) {
                                'int' => null,
                                'string' => null,
                                'bool' => null,
                                'array' => null,
                                static::AUTO_DATETIME_CLASS => 'datetime',

                                default => $type,
                            },
                    },
                    static::getPropertiesWithFirstType(),
                ),
            )
            : [];
    }


    /**
     * Возвращает массив всех свойств dto с его первым типом
     * @see ../../tests/Examples/Example63/Example63Test.php
     *
     * @return array
     */
    public function getCasts(): array
    {
        return static::getPropertiesWithFirstType();
    }
}
