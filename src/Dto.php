<?php

declare(strict_types=1);

namespace Atlcom;

use Atlcom\Traits\DtoCastsTrait;
use Atlcom\Traits\DtoConstsTrait;
use Atlcom\Traits\DtoConvertTrait;
use Atlcom\Traits\DtoCoreTrait;
use Atlcom\Traits\DtoFillTrait;
use Atlcom\Traits\DtoMagicTrait;
use Atlcom\Traits\DtoOptionsTrait;
use Atlcom\Traits\DtoOverrideTrait;
use Atlcom\Traits\DtoPropertiesTrait;
use Atlcom\Traits\DtoSerializeTrait;
use Atlcom\Traits\DtoStrTrait;

/**
 * Абстрактный класс Dto
 * @abstract
 * @version 2.62
 * 
 * @override @see self::mappings()
 * @override @see self::defaults()
 * @override @see self::casts()
 * @override @see self::exceptions()
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
 * @example
 * ExampleDto::fill([])->onlyKeys([])->excludeKeys([])->mappingKeys([])->serializeKeys(true)->toArray();
 * 
 * @see \Atlcom\Tests\DtoTest
 * @see ../../README.md
 * @link https://github.com/atlcomgit/dto
 */
abstract class Dto
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
    use DtoSerializeTrait;
    use DtoStrTrait;
}
