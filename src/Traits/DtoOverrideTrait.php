<?php

declare(strict_types=1);

namespace Atlcom\Traits;

use Carbon\Carbon;
use ReflectionProperty;
use Throwable;

/**
 * Трейт переопределяемых методов
 */
trait DtoOverrideTrait
{
    /**
     * @override
     * Возвращает массив маппинга свойств
     *
     * @return array
     */
    // #[Override()]
    protected function mappings(): array
    {
        return [];
    }


    /**
     * @override
     * Возвращает массив значений по умолчанию
     *
     * @return array
     */
    // #[Override()]
    protected function defaults(): array
    {
        return [];
    }


    /**
     * @override
     * Возвращает массив преобразований типов
     *
     * @return array
     */
    // #[Override()]
    protected function casts(): array
    {
        $laravelClassCollection = 'Illuminate\Support\Collection';

        return static::AUTO_CASTS_OBJECTS_ENABLED
            ? array_filter(
                array_map(
                    static fn (string $type) => match ($type) {
                        $laravelClassCollection => static fn ($v) => new $laravelClassCollection($v),

                        default => strtr(
                            $type,
                            [
                                'int' => null,
                                'string' => null,
                                'bool' => null,
                                'array' => null,
                                Carbon::class => 'datetime',
                            ],
                        )
                    },
                    static::getPropertiesWithFirstType(),
                ),
            )
            : [];
    }


    /**
     * @override
     * Метод вызывается до создания и заполнения dto
     *
     * @param mixed $data
     * @return void
     */
    // #[Override()]
    protected function onCreating(mixed &$data): void {}


    /**
     * @override
     * Метод вызывается после создания и заполнения dto
     *
     * @param mixed $data
     * @return void
     */
    // #[Override()]
    protected function onCreated(mixed $data): void {}


    /**
     * @override
     * Метод вызывается до заполнения dto
     *
     * @param array $array
     * @return void
     */
    // #[Override()]
    protected function onFilling(array &$array): void
    {
        // !(  // приводим id к integer
        //     property_exists($this, 'id')
        //     && array_key_exists('id', $array)
        //     && str_contains((string)(new ReflectionProperty(get_class($this), 'id'))->getType(), 'int')
        //     && is_numeric($array['id'] ?? null)
        // ) ?: $array['id'] = (int)($array['id'] ?? 0);
    }


    /**
     * @override
     * Метод вызывается после заполнения dto
     *
     * @param array $array
     * @return void
     */
    // #[Override()]
    protected function onFilled(array $array): void {}


    /**
     * @override
     * Метод вызывается до объединения с dto
     *
     * @param array $array
     * @return void
     */
    // #[Override()]
    protected function onMerging(array &$array): void {}


    /**
     * @override
     * Метод вызывается после объединения с dto
     *
     * @param array $array
     * @return void
     */
    // #[Override()]
    protected function onMerged(array $array): void {}


    /**
     * @override
     * Метод вызывается перед изменением значения свойства dto
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    // #[Override()]
    protected function onAssigning(string $key, mixed $value): void {}


    /**
     * @override
     * Метод вызывается после изменения значения свойства dto
     *
     * @param string $key
     * @return void
     */
    // #[Override()]
    protected function onAssigned(string $key): void {}


    /**
     * @override
     * Метод вызывается до преобразования dto в массив
     *
     * @param array $array
     * @return void
     */
    // #[Override()]
    protected function onSerializing(array &$array): void {}


    /**
     * @override
     * Метод вызывается после преобразования dto в массив
     *
     * @param array $array
     * @return void
     */
    // #[Override()]
    protected function onSerialized(array &$array): void {}


    /**
     * @override
     * Метод вызывается во время исключения при заполнении dto
     *
     * @param Throwable $exception
     * @return void
     * @throws \Exception
     */
    // #[Override()]
    protected function onException(Throwable $exception): void
    {
        throw $exception;
    }


    /**
     * @override
     * Сообщения ошибок dto
     *
     * @param string $message
     * @param array $values
     * @return string
     */
    // #[Override()]
    protected function exceptions(string $messageCode, array $messageItems): string
    {
        return match ($messageCode) {
            'PropertyNotFound' => sprintf(
                $this->toBasename($this) . '->%s: свойство не найдено',
                $messageItems['property'],
            ),
            'PropertyAssignType' => sprintf(
                $this->toBasename($this) . '->%s' . ": невозможно присвоить свойству тип %s",
                $messageItems['property'],
                $messageItems['type'],
            ),
            'AttributeClassNotFound' => sprintf(
                "Класс атрибута не найден: %s",
                $messageItems['class'],
            ),
            'AttributeNotImplementsBy' => sprintf(
                "Класс аттрибута не реализован от: %s",
                $messageItems['class'],
            ),
            'AttributeMethodNotFound' => sprintf(
                "Атрибут не содержит метод: %s",
                $messageItems['method'],
            ),
            'ClassNotFound' => sprintf(
                "Класс не найден: %s",
                $messageItems['class'],
            ),
            'PropertyNotInitialized' => sprintf(
                $this->toBasename($this) . '->%s: свойство не инициализировано',
                $messageItems['property'],
            ),
            'EnumValueNotSupported' => sprintf(
                $this->toBasename($this) . '->%s: значение "%s" не поддерживается',
                $messageItems['property'],
                $messageItems['value'],
            ),
            'ClassCanNotBeCasted' => sprintf(
                "Класс не может быть приведён: %s",
                $messageItems['class'],
            ),
            'TypeForCastNotFound' => sprintf(
                "Тип приведения не найден: %s",
                $messageItems['type'],
            ),
            'ScalarForCastNeed' => sprintf(
                $this->toBasename($this) . '->%s: приведение типа требует SCALAR',
                $messageItems['property'],
            ),
            'ArrayForCastNeed' => sprintf(
                $this->toBasename($this) . '->%s: приведение типа требует ARRAY',
                $messageItems['property'],
            ),
            'TypeForCastNotSpecified' => sprintf(
                $this->toBasename($this) . '->%s: тип приведения не указан',
                $messageItems['property'],
            ),

            default => 'Неизвестный код сообщения',
        };
    }
}
