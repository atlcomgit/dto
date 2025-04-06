<?php

declare(strict_types=1);

namespace Atlcom\Traits;

use Carbon\Carbon;

/**
 * Трейт констант
 */
trait DtoConstsTrait
{
    /** Указывает класс для работы с датой и временем по умолчанию */
    public const AUTO_DATETIME_CLASS = Carbon::class;

    /** Включает опцию авто приведения типов при заполнении dto или преобразовании в массив */
    public const AUTO_CASTS_ENABLED = false;

    /** Включает опцию авто приведения объектов при заполнении dto */
    public const AUTO_CASTS_OBJECTS_ENABLED = false;

    /** Включает опцию авто маппинг свойств при заполнении dto или преобразовании в массив */
    public const AUTO_MAPPINGS_ENABLED = false;

    /** Включает опцию авто сериализации объектов при заполнении dto или преобразовании в массив */
    public const AUTO_SERIALIZE_ENABLED = false;

    /** Включает опцию для работы с динамическими свойствами через опции */
    public const AUTO_DYNAMIC_PROPERTIES_ENABLED = false;

    /** Включает реализацию интерфейса ArrayAccess для работы с dto как с массивом */
    public const INTERFACE_ARRAY_ACCESS_ENABLED = false;

    /** Включает реализацию интерфейса Countable для включения метода count() */
    public const INTERFACE_COUNTABLE_ENABLED = false;

    /** Включает реализацию интерфейса IteratorAggregate для включения метода getIterator() */
    public const INTERFACE_ITERATOR_AGGREGATE_ENABLED = false;

    /** Включает реализацию интерфейса JsonSerializable для метода json_encode($dto) */
    public const INTERFACE_JSON_SERIALIZABLE_ENABLED = false;

    /** Включает реализацию интерфейса Serializable для методов serialize($dto)/unserialize($dto) */
    public const INTERFACE_SERIALIZABLE_ENABLED = false;

    /** Включает реализацию интерфейса Stringable для работы с dto как со строкой (string)$dto */
    public const INTERFACE_STRINGABLE_ENABLED = false;
}