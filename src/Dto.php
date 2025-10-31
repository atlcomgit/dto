<?php

declare(strict_types=1);

namespace Atlcom;

use ArrayAccess;
use Atlcom\Traits\DtoArrayAccess;
use Atlcom\Traits\DtoCastsTrait;
use Atlcom\Traits\DtoConstsTrait;
use Atlcom\Traits\DtoConvertTrait;
use Atlcom\Traits\DtoCoreTrait;
use Atlcom\Traits\DtoCountable;
use Atlcom\Traits\DtoFillTrait;
use Atlcom\Traits\DtoIteratorAggregate;
use Atlcom\Traits\DtoJsonSerializable;
use Atlcom\Traits\DtoMagicTrait;
use Atlcom\Traits\DtoOptionsTrait;
use Atlcom\Traits\DtoOverrideTrait;
use Atlcom\Traits\DtoPropertiesTrait;
use Atlcom\Traits\DtoLaravelTrait;
use Atlcom\Traits\DtoSerializeTrait;
use Atlcom\Traits\DtoStringable;
use Atlcom\Traits\DtoStrTrait;
use Atlcom\Traits\DtoTrait;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Stringable;

/**
 * Абстрактный класс Dto
 * 
 * @abstract
 * @version 2.76
 * 
 * static   @see self::create()
 *          @see self::fill()
 *          @see self::collect()
 * 
 * method   @see self::merge()
 *          @see self::clear()
 *          @see self::clone()
 *          @see self::transformToDto()
 *          @see self::toArray()
 *          @see self::toJson()
 *          @see self::getHash()
 * 
 *          @see self::toArrayBlank()
 *          @see self::toArrayBlankRecursive()
 * 
 *          @see self::for()
 *          @see self::autoCasts()
 *          @see self::autoMappings()
 *          @see self::onlyFilled()
 *          @see self::onlyNotNull()
 *          @see self::onlyKeys()
 *          @see self::includeStyles()
 *          @see self::includeArray()
 *          @see self::excludeKeys()
 *          @see self::mappingKeys()
 *          @see self::serializeKeys()
 *          @see self::withProtectedKeys()
 *          @see self::withPrivateKeys()
 *          @see self::setCustomOption()
 *          @see self::getCustomOption()
 *          @see self::withCustomOptions()
 * 
 * override @see self::rules()
 *          @see self::casts()
 *          @see self::defaults()
 *          @see self::mappings()
 *          @see self::exceptions()
 * 
 *          @see self::onCreating()
 *          @see self::onCreated()
 *          @see self::onFilling()
 *          @see self::onFilled()
 *          @see self::onMerging()
 *          @see self::onMerged()
 *          @see self::onSerializing()
 *          @see self::onSerialized()
 *          @see self::onAssigning()
 *          @see self::onAssigned()
 *          @see self::onException()
 * 
 * const    @see self::AUTO_CASTS_ENABLED
 *          @see self::AUTO_CASTS_OBJECTS_ENABLED
 *          @see self::AUTO_MAPPINGS_ENABLED
 *          @see self::AUTO_SERIALIZE_ENABLED
 *          @see self::AUTO_DYNAMIC_PROPERTIES_ENABLED
 *          @see self::AUTO_PROPERTIES_AS_METHODS_ENABLED
 *          @see self::AUTO_EMPTY_STRING_TO_NULL_ENABLED
 * 
 * @example
 * ExampleDto::create()->onlyNotNull()->onlyKeys([])->excludeKeys([])->mappingKeys([])->serializeKeys(true)->toArray();
 * 
 * @see ../README.md
 * @see ../docs/OVERRIDES.md
 * @link https://github.com/atlcomgit/dto
 */
abstract class Dto implements
    ArrayAccess,
    Countable,
    IteratorAggregate,
    JsonSerializable,
    Stringable
{
    use DtoTrait;
    use DtoArrayAccess;
    use DtoCastsTrait;
    use DtoConstsTrait;
    use DtoConvertTrait;
    use DtoCoreTrait;
    use DtoCountable;
    use DtoFillTrait;
    use DtoIteratorAggregate;
    use DtoJsonSerializable;
    use DtoMagicTrait;
    use DtoOptionsTrait;
    use DtoOverrideTrait;
    use DtoPropertiesTrait;
    use DtoLaravelTrait;
    use DtoSerializeTrait;
    use DtoStringable;
    use DtoStrTrait;
}
