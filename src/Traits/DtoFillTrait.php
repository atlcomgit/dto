<?php

namespace Atlcom\Traits;

use Exception;
use ReflectionProperty;
use Throwable;

/**
 * Трейт заполнения dto
 */
trait DtoFillTrait
{
    /**
     * Создает и заполняет dto
     *
     * @param mixed ...$data
     * @return static
     */
    final public static function create(mixed ...$createData): static
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
     *
     * @param class-string $dtoClass
     * @param array $array = []
     * @return mixed
     */
    final public function transformToDto(string $dtoClass, array $array = []): mixed
    {
        if (!class_exists($dtoClass)) {
            $this->onException(
                new Exception(
                    $this->exceptions('ClassNotFound', ['class' => $dtoClass]),
                    500
                )
            );

            return $this;
        }

        return $dtoClass::create($this, $array);
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
     *
     * @param mixed $data
     * @return static
     */
    final public function fillFromData(mixed $data): static
    {
        return $this->fillFromArray(static::convertDataToArray($data));
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
     *
     * @param array $array
     * @return static
     */
    final public function fillFromArray(array $array): static
    {
        return $this->fillDto($array);
    }


    /**
     * Объединить массив с dto
     *
     * @param array|object|string|null $data
     * @return static
     * @throws Exception
     */
    final public function merge(array|object|string|null $data): static
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
                    throw new Exception(
                        $this->exceptions('PropertyNotInitialized', ['property' => $key]),
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
     *
     * @param array $items
     * @return array
     */
    public static function collect(array $items): array
    {
        return array_map(static fn ($item) => static::fill($item), $items);
    }
}