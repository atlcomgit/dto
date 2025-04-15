<?php

declare(strict_types=1);

namespace Atlcom\Traits;

/**
 * Трейт для расширения своего Dto
 * @version 2.67
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