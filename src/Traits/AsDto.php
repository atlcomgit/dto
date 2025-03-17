<?php

declare(strict_types=1);

namespace Atlcom\Traits;

/**
 * Трейт для расширения своего Dto
 * @version 2.64
 * 
 * @override        @see self::rules()
 * @override        @see self::mappings()
 * @override        @see self::defaults()
 * @override        @see self::casts()
 * @override        @see self::exceptions()
 * 
 * @override        @see self::onCreating()
 * @override        @see self::onCreated()
 * @override        @see self::onFilling()
 * @override        @see self::onFilled()
 * @override        @see self::onMerging()
 * @override        @see self::onMerged()
 * @override        @see self::onSerializing()
 * @override        @see self::onSerialized()
 * @override        @see self::onAssigning()
 * @override        @see self::onAssigned()
 * @override        @see self::onException()
 * 
 * @final @static   @see self::create()
 * @final @static   @see self::fill()
 * @final @static   @see self::collect()
 * 
 * @final           @see self::merge()
 * @final           @see self::transformToDto()
 * @final           @see self::toArray()
 * @final           @see self::toJson()
 * @final           @see self::getHash()
 * 
 * @final @static   @see self::toArrayBlank()
 * @final @static   @see self::toArrayBlankRecursive()
 * 
 * @final           @see self::for()
 * @final           @see self::autoCasts()
 * @final           @see self::autoMappings()
 * @final           @see self::onlyFilled()
 * @final           @see self::onlyNotNull()
 * @final           @see self::onlyKeys()
 * @final           @see self::includeStyles()
 * @final           @see self::includeArray()
 * @final           @see self::excludeKeys()
 * @final           @see self::mappingKeys()
 * @final           @see self::serializeKeys()
 * @final           @see self::withProtectedKeys()
 * @final           @see self::withPrivateKeys()
 * @final           @see self::setCustomOption()
 * @final           @see self::getCustomOption()
 * @final           @see self::withCustomOptions()
 */
trait AsDto
{
    use DtoCastsTrait;
    use DtoConstsTrait;
    use DtoConvertTrait;
    use DtoCoreTrait;
    use DtoFillTrait;
    use DtoMagicTrait;
    use DtoOptionsTrait;
    use DtoOverrideTrait;
    use DtoPropertiesTrait;
    use DtoLaravelTrait;
    use DtoSerializeTrait;
    use DtoStrTrait;
}