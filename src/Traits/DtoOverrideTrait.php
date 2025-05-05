<?php

declare(strict_types=1);

namespace Atlcom\Traits;

use Throwable;

/**
 * Трейт переопределяемых методов
 * @mixin \Atlcom\Dto
 */
trait DtoOverrideTrait
{
    /**
     * @override
     * Возвращает массив для преобразований типов
     * @see ../../tests/Examples/Example08/Example08Test.php
     * @see ../../tests/Examples/Example09/Example09Test.php
     * @see ../../tests/Examples/Example10/Example10Test.php
     * @link https://github.com/atlcomgit/dto/blob/master/tests/Examples/Example08/Example08Test.php
     * @link https://github.com/atlcomgit/dto/blob/master/tests/Examples/Example09/Example09Test.php
     * @link https://github.com/atlcomgit/dto/blob/master/tests/Examples/Example10/Example10Test.php
     *
     * @return array
     */
    // #[Override()]
    protected function casts(): array
    {
        return static::castDefault();
    }


    /**
     * @override
     * Возвращает массив для значений по умолчанию
     * @see ../../tests/Examples/Example05/Example05Test.php
     * @link https://github.com/atlcomgit/dto/blob/master/tests/Examples/Example05/Example05Test.php
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
     * Возвращает массив для маппинга имен свойств
     * @see ../../tests/Examples/Example06/Example06Test.php
     * @see ../../tests/Examples/Example07/Example07Test.php
     * @see ../../tests/Examples/Example10/Example10Test.php
     * @link https://github.com/atlcomgit/dto/blob/master/tests/Examples/Example06/Example06Test.php
     * @link https://github.com/atlcomgit/dto/blob/master/tests/Examples/Example07/Example07Test.php
     * @link https://github.com/atlcomgit/dto/blob/master/tests/Examples/Example10/Example10Test.php
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
     * Метод вызывается до создания и заполнения dto
     * @see ../../tests/Examples/Example44/Example44Test.php
     * @link https://github.com/atlcomgit/dto/blob/master/tests/Examples/Example44/Example44Test.php
     *
     * @param mixed $data
     * @return void
     */
    // #[Override()]
    protected function onCreating(mixed &$data): void {}


    /**
     * @override
     * Метод вызывается после создания и заполнения dto
     * @see ../../tests/Examples/Example44/Example44Test.php
     * @link https://github.com/atlcomgit/dto/blob/master/tests/Examples/Example44/Example44Test.php
     *
     * @param mixed $data
     * @return void
     */
    // #[Override()]
    protected function onCreated(mixed $data): void {}


    /**
     * @override
     * Метод вызывается до заполнения dto
     * @see ../../tests/Examples/Example11/Example11Test.php
     * @link https://github.com/atlcomgit/dto/blob/master/tests/Examples/Example11/Example11Test.php
     *
     * @param array $array
     * @return void
     */
    // #[Override()]
    protected function onFilling(array &$array): void {}


    /**
     * @override
     * Метод вызывается после заполнения dto
     * @see ../../tests/Examples/Example12/Example12Test.php
     * @link https://github.com/atlcomgit/dto/blob/master/tests/Examples/Example12/Example12Test.php
     *
     * @param array $array
     * @return void
     */
    // #[Override()]
    protected function onFilled(array $array): void {}


    /**
     * @override
     * Метод вызывается до объединения с dto
     * @see ../../tests/Examples/Example13/Example13Test.php
     * @link https://github.com/atlcomgit/dto/blob/master/tests/Examples/Example13/Example13Test.php
     *
     * @param array $array
     * @return void
     */
    // #[Override()]
    protected function onMerging(array &$array): void {}


    /**
     * @override
     * Метод вызывается после объединения с dto
     * @see ../../tests/Examples/Example14/Example14Test.php
     * @link https://github.com/atlcomgit/dto/blob/master/tests/Examples/Example14/Example14Test.php
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
     * @see ../../tests/Examples/Example32/Example32Test.php
     * @link https://github.com/atlcomgit/dto/blob/master/tests/Examples/Example32/Example32Test.php
     *
     * @param string $key
     * @return void
     */
    // #[Override()]
    protected function onAssigned(string $key): void {}


    /**
     * @override
     * Метод вызывается до преобразования dto в массив
     * @see ../../tests/Examples/Example15/Example15Test.php
     * @link https://github.com/atlcomgit/dto/blob/master/tests/Examples/Example15/Example15Test.php
     *
     * @param array $array
     * @return void
     */
    // #[Override()]
    protected function onSerializing(array &$array): void {}


    /**
     * @override
     * Метод вызывается после преобразования dto в массив
     * @see ../../tests/Examples/Example16/Example16Test.php
     * @link https://github.com/atlcomgit/dto/blob/master/tests/Examples/Example16/Example16Test.php
     *
     * @param array $array
     * @return void
     */
    // #[Override()]
    protected function onSerialized(array &$array): void {}


    /**
     * @override
     * Метод вызывается во время исключения при заполнении dto
     * @see ../../tests/Examples/Example17/Example17Test.php
     * @link https://github.com/atlcomgit/dto/blob/master/tests/Examples/Example17/Example17Test.php
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
     * @see ../../tests/Examples/Example33/Example33Test.php
     * @link https://github.com/atlcomgit/dto/blob/master/tests/Examples/Example33/Example33Test.php
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
            'ArrayAccessDisabled' => sprintf(
                $this->toBasename($this) . '->%s: доступ к свойству через массив отключен',
                $messageItems['property'],
            ),
            'CountableDisabled',
            'IteratorAggregateDisabled',
            'JsonSerializableDisabled',
            'StringableDisabled'
            => sprintf(
                $this->toBasename($this) . '->%s(): доступ к интерфейсу отключен',
                $messageItems['method'],
            ),
            'SerializableDisabled' => sprintf(
                $this->toBasename($this) . '->%s(): доступ к сериализации отключен',
                $messageItems['method'],
            ),
            'PropertiesAsMethodsDisabled' => sprintf(
                $this->toBasename($this) . '->%s(): доступ к свойству через метод отключен в %s',
                $messageItems['property'],
                $messageItems['method'],
            ),

            default => 'Неизвестный код сообщения',
        };
    }
}