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
}
