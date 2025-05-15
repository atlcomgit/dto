<?php

declare(strict_types=1);

namespace Atlcom\Traits;

use Atlcom\Exceptions\DtoException;
use ReflectionProperty;
use Throwable;

/**
 * Трейт заполнения dto
 * @mixin \Atlcom\Dto
 */
trait DtoFillTrait
{
    /**
     * Создает и заполняет dto
     * @see ../../tests/Examples/Example01/Example01Test.php
     * @see ../../tests/Examples/Example02/Example02Test.php
     * @see ../../tests/Examples/Example03/Example03Test.php
     * @see ../../tests/Examples/Example04/Example04Test.php
     *
     * @param mixed ...$data
     * @return static
     */
    public static function create(mixed ...$createData): static
    {
        $array = [];
        foreach ($createData as $key => $value) {
            $array = [
                ...$array,
                ...(is_string($key) ? [$key => $value] : static::convertDataToArray($value)),
            ];
        }

        return static::fill($array);
    }


    /**
     * Преобразование в другой dto
     * @see ../../tests/Examples/Example34/Example34Test.php
     *
     * @param class-string $dtoClass
     * @param array $array = []
     * @return mixed
     */
    public function transformToDto(string $dtoClass, array $array = []): mixed
    {
        if (!class_exists($dtoClass)) {
            $this->onException(
                new DtoException(
                    $this->exceptions('ClassNotFound', ['class' => $dtoClass]),
                    500,
                ),
            );

            return $this;
        }

        return $dtoClass::create($this, $array);
    }


    /**
     * Статический вызов создания объекта dto
     * @see ../../tests/Other/DefaultsDtoTest.php
     *
     * @param array|object|string|null $data
     * @return static
     */
    public static function fill(array|object|string|null $data = null): static
    {
        return new static($data);
    }


    /**
     * Заполнение dto из объекта
     * @see ../../tests/Other/DefaultsDtoTest.php
     *
     * @param mixed $data
     * @return static
     */
    public function fillFromData(mixed $data): static
    {
        return $this->fillFromArray(static::convertDataToArray($data));
    }


    /**
     * Заполнение dto из объекта
     * @see ../../tests/Other/DefaultsDtoTest.php
     *
     * @param object $data
     * @return static
     */
    public function fillFromObject(object $data): static
    {
        return $this->fillFromData($data);
    }


    /**
     * Заполнение dto из dto
     * @see ../../tests/Other/DefaultsDtoTest.php
     *
     * @param self $data
     * @return static
     */
    public function fillFromDto(self $data): static
    {
        return $this->fillFromData($data);
    }


    /**
     * Заполнение dto из json строки
     * @see ../../tests/Other/DefaultsDtoTest.php
     *
     * @param string $data
     * @return static
     */
    public function fillFromJson(string $data): static
    {
        return $this->fillFromData($data);
    }


    /**
     * Заполнение dto из массива
     * @see ../../tests/Examples/Example21/Example21Test.php
     *
     * @param array $array
     * @return static
     */
    public function fillFromArray(array $array): static
    {
        return $this->fillDto($array);
    }


    /**
     * Объединить массив с dto
     * @see ../../tests/Examples/Example13/Example13Test.php
     *
     * @param array|object|string|null $data
     * @return static
     * @throws DtoException
     */
    public function merge(array|object|string|null $data): static
    {
        try {
            $array = static::convertDataToArray($data);

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
                    throw new DtoException(
                        $this->exceptions('PropertyNotInitialized', ['property' => $key]),
                        500,
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
     * Возвращает массив из dto
     * @see ../../tests/Examples/Example31/Example31Test.php
     * @see ../../tests/Examples/Example58/Example58Test.php
     *
     * @param array $items
     * @return array<static>|static[]
     */
    public static function collect(array $items): array
    {
        return array_map(static fn ($item) => static::fill($item), $items);
    }


    /**
     * Очищает все свойства dto
     * @see ../../tests/Examples/Example48/Example48Test.php
     *
     * @return static
     */
    public function clear(): static
    {
        return $this
            ->autoCasts()
            ->fillFromData(static::toArrayBlank(false))
            ->merge($this->defaults())
            ->reset();
    }


    /**
     * Клонирует dto
     * @see ../../tests/Examples/Example57/Example57Test.php
     *
     * @return static
     */
    public function clone(): static
    {
        return (clone $this)->setOptions($this->options());
    }
}
