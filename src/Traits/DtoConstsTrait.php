<?php

declare(strict_types=1);

namespace Atlcom\Traits;

use Atlcom\Exceptions\DtoException;
use Carbon\Carbon;

/**
 * Трейт констант
 * @mixin \Atlcom\Dto
 */
trait DtoConstsTrait
{
    /**
     * Указывает класс для работы с датой и временем по умолчанию
     * @see ../../tests/Examples/Example35/Example35Test.php
     */
    public const AUTO_DATETIME_CLASS = Carbon::class;

    /**
     * Включает опцию авто приведения типов при заполнении dto или преобразовании в массив
     * @see ../../tests/Examples/Example19/Example19Test.php
     */
    public const AUTO_CASTS_ENABLED = false;

    /**
     * Включает опцию авто приведения объектов при заполнении dto
     * @see ../../tests/Examples/Example47/Example47Test.php
     */
    public const AUTO_CASTS_OBJECTS_ENABLED = false;

    /**
     * Включает опцию авто маппинг свойств при заполнении dto или преобразовании в массив
     * @see ../../tests/Examples/Example18/Example18Test.php
     */
    public const AUTO_MAPPINGS_ENABLED = false;

    /**
     * Включает опцию авто сериализации объектов при заполнении dto или преобразовании в массив
     * @see ../../tests/Examples/Example20/Example20Test.php
     */
    public const AUTO_SERIALIZE_ENABLED = false;

    /**
     * Включает опцию для работы с динамическими свойствами через опции
     * @see ../../tests/Examples/Example43/Example43Test.php
     * @see ../../tests/Examples/Example59/Example59Test.php
     */
    public const AUTO_DYNAMIC_PROPERTIES_ENABLED = false;

    /**
     * Включает опцию для работы со свойствами как с методами
     * @see ../../tests/Examples/Example56/Example56Test.php
     */
    public const AUTO_PROPERTIES_AS_METHODS_ENABLED = true;

    /**
     * Включает опцию для преобразования пустой строки в null
     * @see ../../tests/Examples/Example63/Example63Test.php
     */
    public const AUTO_EMPTY_STRING_TO_NULL_ENABLED = true;

    /**
     * Включает реализацию интерфейса ArrayAccess для работы с dto как с массивом
     * @see ../../tests/Examples/Example49/Example49Test.php
     */
    public const INTERFACE_ARRAY_ACCESS_ENABLED = false;

    /**
     * Включает реализацию интерфейса Countable для включения метода count()
     * @see ../../tests/Examples/Example50/Example50Test.php
     */
    public const INTERFACE_COUNTABLE_ENABLED = true;

    /**
     * Включает реализацию интерфейса IteratorAggregate для включения метода getIterator()
     * @see ../../tests/Examples/Example51/Example51Test.php
     */
    public const INTERFACE_ITERATOR_AGGREGATE_ENABLED = true;

    /**
     * Включает реализацию интерфейса JsonSerializable для метода json_encode($dto)
     * @see ../../tests/Examples/Example52/Example52Test.php
     */
    public const INTERFACE_JSON_SERIALIZABLE_ENABLED = true;

    /**
     * Включает реализацию интерфейса Serializable для методов __serialize()/__unserialize()
     * @see ../../tests/Examples/Example53/Example53Test.php
     */
    public const INTERFACE_SERIALIZABLE_ENABLED = true;

    /**
     * Включает реализацию интерфейса Stringable для работы с dto как со строкой (string)$dto
     * @see ../../tests/Examples/Example54/Example54Test.php
     */
    public const INTERFACE_STRINGABLE_ENABLED = true;


    /**
     * Возвращает или устанавливает значение константы dto
     * @see ../../tests/Examples/Example60/Example60Test.php
     *
     * @param string $name
     * @param bool|null $value
     * @return static|bool
     */
    public function consts(string $name, ?bool $value = null): static|bool|string
    {
        $class = $this::class;

        if (!defined("$class::$name")) {
            $this->onException(
                new DtoException(
                    $this->exceptions('ConstantNotFound', ['name' => $name]),
                    500,
                ),
            );

            return false;
        }

        $consts = $this->options()['consts'] ?? [];

        if (is_null($value)) {
            return $consts[$name] ?? constant("$class::$name");
        }

        $consts[$name] = $value;
        $this->options(consts: $consts);

        return $this;
    }
}
