<?php

declare(strict_types=1);

namespace Atlcom\Traits;

use BackedEnum;
use DateTime;
use ReflectionMethod;
use stdClass;

/**
 * Трейт преобразования значений
 * @mixin \Atlcom\Dto
 */
trait DtoConvertTrait
{
    /**
     * @override
     * Преобразование данных в массив перед заполнением dto
     *
     * @param mixed $data
     * @return array
     */
    protected static function convertDataToArray(mixed $data = null): array
    {
        $laravelClassSchema = 'Illuminate\Support\Facades\Schema';
        $laravelClassModel = 'Illuminate\Database\Eloquent\Model';
        $laravelClassArr = 'Illuminate\Support\Arr';
        $laravelClassFormRequest = 'Illuminate\Foundation\Http\FormRequest';
        $laravelClassRequest = 'Illuminate\Http\Request';
        $laravelClassCollection = 'Illuminate\Support\Collection';

        return match (true) {
            $data instanceof self => $data->withoutOptions()->toArray(),
            is_object($data) && (
                $data::class ===  $laravelClassModel || is_subclass_of($data, $laravelClassModel)
            ) => $laravelClassArr::except(
                $data->exists ? $data->toArray() : $data->getAttributes(),
                $data->getAppends(),
            )
            ?: array_diff_key(
                array_fill_keys($laravelClassSchema::getColumnListing($data->getTable()), null),
                [], // array_fill_keys(is_array($data->getGuarded()) ? $data->getGuarded() : [], null),
            ),
            is_object($data) && (
                $data::class ===  $laravelClassFormRequest || is_subclass_of($data, $laravelClassFormRequest)
            ) => $data->toArray(),
            is_object($data) && (
                $data::class ===  $laravelClassRequest || is_subclass_of($data, $laravelClassRequest)
            ) => $data->toArray(),
            is_object($data) && (
                $data::class ===  $laravelClassCollection || is_subclass_of($data, $laravelClassCollection)
            ) => $data->all(),
            is_object($data) && method_exists($data, 'toArray') => $data->toArray(),
            is_string($data) => static::jsonDecode($data, false),
            is_array($data) => $data,
            is_object($data) => (array)$data ?: get_class_vars(get_class($data)),

            default => [],
        };
    }


    /**
     * @override
     * Рекурсивно заполняет все свойства объекта или ключи массива значением null
     *
     * @param object|array $array
     * @param bool $allValuesToNull
     * @return array
     */
    protected static function convertArrayToEmptyRecursive(object|array $array, bool $allValuesToNull = false): array
    {
        $array = array_filter(
            (array)$array,
            static fn ($key) => !str_contains($key, '*') && !str_contains($key, chr(0)),
            ARRAY_FILTER_USE_KEY
        );

        array_walk_recursive(
            $array,
            static fn (&$item) => $item = match (true) {
                is_array($item) => static::convertArrayToEmptyRecursive($item, $allValuesToNull),
                is_object($item) => static::convertArrayToEmptyRecursive($item, $allValuesToNull),

                default => static::convertTypeToEmptyValue(gettype($item), true, $allValuesToNull),
            }
        );


        return $array;
    }


    /**
     * @override
     * Возвращает пустое значение относительно типа
     *
     * @param string $type
     * @param bool $recursive
     * @param bool $allValuesToNull
     * @return mixed
     */
    protected static function convertTypeToEmptyValue(string $type, bool $recursive, bool $allValuesToNull = true): mixed
    {
        $result = null;
        $types = array_filter(explode('|', str_replace(['?', ' '], ['|null|', ''], $type)));
        $lowerTypes = array_map(static fn ($value) => mb_strtolower($value), $types);

        switch (true) {
            case in_array('closure', $lowerTypes, true):
                $result = null;
                break;

            case count($types) === 1 && class_exists($type):
                $result = match (true) {
                    $type === stdClass::class => $allValuesToNull ? null : (object)[],
                    is_subclass_of($type, DateTime::class) => $allValuesToNull
                    ? null
                    : '', // static::convertArrayToEmptyRecursive(new $type(), $allValuesToNull),
                    is_subclass_of($type, BackedEnum::class) => null,
                    is_subclass_of($type, self::class) => $recursive
                    ? (
                        $type::toArrayBlankRecursive($allValuesToNull)
                    )
                    : (
                        $allValuesToNull ? null : (array)(new $type())
                    ),
                    method_exists($type, 'toArray') => $recursive
                    ? (
                        (new ReflectionMethod($type, 'toArray'))->isStatic()
                        ? static::convertArrayToEmptyRecursive($type::toArray(), $allValuesToNull)
                        : static::convertArrayToEmptyRecursive((new $type())->toArray(), $allValuesToNull)
                    )
                    : (
                        $allValuesToNull ? null : (array)(new $type())
                    ),

                    default => $allValuesToNull ? null : (array)(new $type()),
                };
                break;

            case in_array('array', $lowerTypes, true):
                $result = $allValuesToNull ? null : [];
                break;

            case in_array('object', $lowerTypes, true):
                $result = $allValuesToNull ? null : (object)[];
                break;

            case in_array($type, ['string', 'str']):
                $result = $allValuesToNull ? null : '';
                break;

            case in_array($type, ['integer', 'int']):
                $result = $allValuesToNull ? null : 0;
                break;

            case in_array($type, ['float', 'double']):
                $result = $allValuesToNull ? null : 0.0;
                break;

            case in_array($type, ['boolean', 'bool']):
                $result = $allValuesToNull ? null : false;
                break;

            default:
                $result = null;

                if (count($types) > 1) {
                    $result = null;

                    foreach ($types as $type) {
                        if (class_exists($type)) {
                            $result = static::convertTypeToEmptyValue($type, $recursive, $allValuesToNull);
                            break;
                        }
                    }
                }
        };

        return $result;
    }
}