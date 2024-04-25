<?php

namespace Expo\Dto\Attributes;

use Attribute;
use Expo\Dto\Interfaces\AttributeDtoInterface;

/**
 * Атрибут указывает, что поле содержит коллекцию объектов
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Collection implements AttributeDtoInterface
{
    /**
     * @param class-string $class
     */
    public function __construct(
        private readonly string $class
    ) {
    }

    public function handle(string &$key, mixed &$value, mixed $defaultValue, string $dtoClass): void
    {
        $class = $this->class;
        $value = $value ? $class::collect($value) : $defaultValue;
    }
}