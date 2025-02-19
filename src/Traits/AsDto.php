<?php

declare(strict_types=1);

namespace Atlcom\Traits;

/**
 * Трейт для расширения своего Dto
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