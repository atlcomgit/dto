<?php

namespace Atlcom\Traits;

/**
 * Трейт работы со строками
 */
trait DtoStrTrait
{
    /**
     * Определение имени класса
     *
     * @param object|string $class
     * @return string
     */
    protected function toBasename(object|string $class): string
    {
        return basename(
            str_replace(
                '\\',
                '/',
                is_object($class) ? get_class($class) : $class
            )
        );
    }

    /**
     * Перевод строки в camelCase
     *
     * @param string $string
     * @return string
     */
    protected function toCamelCase(string $string): string
    {
        return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $string))));
    }


    /**
     * Перевод строки в snake_case
     *
     * @param string $string
     * @return string
     */
    protected function toSnakeCase(string $string): string
    {
        return strtolower(ltrim(preg_replace('/(?<!^)[A-Z]/', '_$0', $string), '_'));
    }
}