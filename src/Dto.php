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
use Atlcom\Traits\DtoSerializable;
use Atlcom\Traits\DtoSerializeTrait;
use Atlcom\Traits\DtoStringable;
use Atlcom\Traits\DtoStrTrait;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Serializable;
use Stringable;

/**
 * Абстрактный класс Dto
 * @abstract
 * @version 2.66
 * 
 * @override @see self::rules()
 * @override @see self::mappings()
 * @override @see self::defaults()
 * @override @see self::casts()
 * @override @see self::exceptions()
 * 
 * @override @see self::onCreating()
 * @override @see self::onCreated()
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
 * @static   @see self::create()
 * @static   @see self::fill()
 * @static   @see self::collect()
 * 
 *           @see self::merge()
 *           @see self::clear()
 *           @see self::transformToDto()
 *           @see self::toArray()
 *           @see self::toJson()
 *           @see self::getHash()
 * 
 * @static   @see self::toArrayBlank()
 * @static   @see self::toArrayBlankRecursive()
 * 
 *           @see self::for()
 *           @see self::autoCasts()
 *           @see self::autoMappings()
 *           @see self::onlyFilled()
 *           @see self::onlyNotNull()
 *           @see self::onlyKeys()
 *           @see self::includeStyles()
 *           @see self::includeArray()
 *           @see self::excludeKeys()
 *           @see self::mappingKeys()
 *           @see self::serializeKeys()
 *           @see self::withProtectedKeys()
 *           @see self::withPrivateKeys()
 *           @see self::setCustomOption()
 *           @see self::getCustomOption()
 *           @see self::withCustomOptions()
 * 
 * @example
 * ExampleDto::fill([])->onlyKeys([])->excludeKeys([])->mappingKeys([])->serializeKeys(true)->toArray();
 * 
 * @see \Atlcom\Tests\DtoTest
 * @see ../../README.md
 * @link https://github.com/atlcomgit/dto
 */
abstract class Dto implements
    ArrayAccess,
    Countable,
    IteratorAggregate,
    JsonSerializable,
    Serializable,
    Stringable
{
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
    use DtoSerializable;
    use DtoSerializeTrait;
    use DtoStringable;
    use DtoStrTrait;
}