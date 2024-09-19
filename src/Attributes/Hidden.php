<?php

namespace Atlcom\Attributes;

use Attribute;

/**
 * Атрибут указывает, что поле будет скрыто при преобразовании dto в массив
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Hidden
{
}