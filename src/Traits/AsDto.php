<?php

declare(strict_types=1);

namespace Atlcom\Traits;

/**
 * Трейт для расширения своего Dto
 * @version 2.62
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
    use DtoSerializeTrait;
    use DtoStrTrait;
}