<?php

declare(strict_types=1);

namespace Atlcom\Traits;

/**
 * Трейт для расширения своего Dto
 * @version 2.64
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