<?php

namespace Expo\Dto\Interfaces;

interface AttributeDtoInterface
{
    public function handle(string &$key, mixed &$value, mixed $defaultValue, string $dtoClass): void;
}