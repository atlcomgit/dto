<?php

declare(strict_types=1);

namespace Atlcom;

use Atlcom\Traits\DtoCastsTrait;
use Atlcom\Traits\DtoConvertTrait;
use Atlcom\Traits\DtoCoreTrait;
use Atlcom\Traits\DtoFillTrait;
use Atlcom\Traits\DtoMagicTrait;
use Atlcom\Traits\DtoOptionsTrait;
use Atlcom\Traits\DtoOverrideTrait;
use Atlcom\Traits\DtoPropertiesTrait;
use Atlcom\Traits\DtoSerializeTrait;
use Atlcom\Traits\DtoStrTrait;
use Carbon\Carbon;


/**
 * Абстрактный класс dto по умолчанию
 * @abstract
 * @version 2.56
 * 
 * @override @see self::mappings()
 * @override @see self::defaults()
 * @override @see self::casts()
 * @override @see self::exceptions()
 * @override @see self::onFilling()
 * @override @see self::onFilled()
 * @override @see self::onMerging()
 * @override @see self::onMerged()
 * @override @see self::onSerializing()
 * @override @see self::onSerialized()
 * @override @see self::onAssigning()
 * @override @see self::onAssigned()
 * @override @see self::onException()
 * 
 * @example
 * ExampleDto::fill([])->onlyKeys([])->excludeKeys([])->mappingKeys([])->serializeKeys(true)->toArray();
 * 
 * @see \Atlcom\Tests\DtoTest
 * @see ../../README.md
 */
abstract class Dto
{
    use DtoCastsTrait;
    use DtoConvertTrait;
    use DtoCoreTrait;
    use DtoFillTrait;
    use DtoMagicTrait;
    use DtoOptionsTrait;
    use DtoOverrideTrait;
    use DtoPropertiesTrait;
    use DtoSerializeTrait;
    use DtoStrTrait;


    /** Включает опцию авто приведения типов при заполнении dto или преобразовании в массив */
    public const AUTO_CASTS_ENABLED = false;
    /** Включает опцию авто маппинг свойств при заполнении dto или преобразовании в массив */
    public const AUTO_MAPPINGS_ENABLED = false;
    /** Включает опцию авто сериализации объектов при заполнении dto или преобразовании в массив */
    public const AUTO_SERIALIZE_ENABLED = false;
    /** Указывает класс для работы с датой и временем по умолчанию */
    public const AUTO_DATETIME_CLASS = Carbon::class;
    /** Включает опцию авто создания динамический свойств через опции */
    public const AUTO_DYNAMIC_PROPERTIES_ENABLED = false;


    /**
     * construct dto
     *
     * @param array|object|string|null $data
     */
    public function __construct(array|object|string|null $constructData = null)
    {
        is_null($constructData) ?: $this->fillFromArray(static::convertDataToArray($constructData));
    }


    /**
     * destruct dto
     */
    public function __destruct()
    {
        $this->reset();
    }
}
