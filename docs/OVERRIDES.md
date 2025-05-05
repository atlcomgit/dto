# Override methods (hooks)

@override [public function rules(): array](#rules)

@override [protected function casts(): array](#casts)
@override [protected function defaults(): array](#defaults)
@override [protected function mappings(): array](#mappings)
@override [protected function exceptions(string \$messageCode, array \$messageItems): string](#exceptions)

@override [protected function onCreating(mixed &amp;\$data): void](#onCreating)
@override [protected function onCreated(mixed \$data): void](#onCreated)
@override [protected function onFilling(array &amp;\$array): void](#onFilling)
@override [protected function onFilled(array \$array): void](#onFilled)
@override [protected function onMerging(array &amp;\$array): void](#onMerging)
@override [protected function onMerged(array \$array): void](#onMerged)
@override [protected function onSerializing(array &amp;\$array): void](#onSerializing)
@override [protected function onSerialized(array &amp;\$array): void](#onSerialized)
@override [protected function onAssigning(string \$key, mixed \$value): void](#onAssigning)
@override [protected function onAssigned(string \$key): void](#onAssigned)
@override [protected function onException(Throwable \$exception): void](#onException)

## rules
```php
/**
 * Правила валидации при использовании Dto вместо FormRequest
 * @link https://github.com/atlcomgit/dto/blob/master/docs/LARAVEL.md
 *
 * @return array
 */
#[Override()]
public function rules(): array
{
    return [];
}
```

## casts
```php
/**
 * Возвращает массив преобразований типов
 * @link https://github.com/atlcomgit/dto/blob/master/tests/Examples/Example08/Example08Test.php
 *
 * @return array
 */
#[Override()]
protected function casts(): array
{
    return [
        ...parent::casts(),
    ];
}
```

## defaults
```php
/**
 * Возвращает массив значений по умолчанию
 * @link https://github.com/atlcomgit/dto/blob/master/tests/Examples/Example05/Example05Test.php
 *
 * @return array
 */
#[Override()]
protected function defaults(): array
{
    return [];
}
```

## mappings
```php
/**
 * Возвращает массив маппинга свойств
 * @link https://github.com/atlcomgit/dto/blob/master/tests/Examples/Example06/Example06Test.php
 *
 * @return array
 */
#[Override()]
protected function mappings(): array
{
    return [];
}
```

## exceptions
```php
/**
 * Сообщения ошибок dto
 * @link https://github.com/atlcomgit/dto/blob/master/tests/Examples/Example33/Example33Test.php
 *
 * @param string $message
 * @param array $values
 * @return string
 */
#[Override()]
protected function exceptions(string $messageCode, array $messageItems): string
{
    return parent::exceptions($messageCode, $messageItems);
}
```

## onCreating
```php
/**
 * Метод вызывается до создания и заполнения dto
 * @link https://github.com/atlcomgit/dto/blob/master/tests/Examples/Example44/Example44Test.php
 *
 * @param mixed $data
 * @return void
 */
#[Override()]
protected function onCreating(mixed &$data): void {}
```

## onCreated
```php
/**
 * Метод вызывается после создания и заполнения dto
 * @link https://github.com/atlcomgit/dto/blob/master/tests/Examples/Example44/Example44Test.php
 *
 * @param mixed $data
 * @return void
 */
#[Override()]
protected function onCreated(mixed $data): void {}
```

## onFilling
```php
/**
 * Метод вызывается до заполнения dto
 * @link https://github.com/atlcomgit/dto/blob/master/tests/Examples/Example11/Example11Test.php
 *
 * @param array $array
 * @return void
 */
#[Override()]
protected function onFilling(array &$array): void {}
```

## onFilled
```php
/**
 * Метод вызывается после заполнения dto
 * @link https://github.com/atlcomgit/dto/blob/master/tests/Examples/Example12/Example12Test.php
 *
 * @param array $array
 * @return void
 */
#[Override()]
protected function onFilled(array $array): void {}
```

## onMerging
```php
/**
 * Метод вызывается до объединения с dto
 * @link https://github.com/atlcomgit/dto/blob/master/tests/Examples/Example13/Example13Test.php
 *
 * @param array $array
 * @return void
 */
#[Override()]
protected function onMerging(array &$array): void {}
```

## onMerged
```php
/**
 * Метод вызывается после объединения с dto
 * @link https://github.com/atlcomgit/dto/blob/master/tests/Examples/Example14/Example14Test.php
 *
 * @param array $array
 * @return void
 */
#[Override()]
protected function onMerged(array $array): void {}
```

## onSerializing
```php
/**
 * Метод вызывается до преобразования dto в массив
 * @link https://github.com/atlcomgit/dto/blob/master/tests/Examples/Example15/Example15Test.php
 *
 * @param array $array
 * @return void
 */
#[Override()]
protected function onSerializing(array &$array): void {}
```

## onSerialized
```php
/**
 * Метод вызывается после преобразования dto в массив
 * @link https://github.com/atlcomgit/dto/blob/master/tests/Examples/Example16/Example16Test.php
 *
 * @param array $array
 * @return void
 */
#[Override()]
protected function onSerialized(array &$array): void {}
```

## onAssigning
```php
/**
 * Метод вызывается перед изменением значения свойства dto
 *
 * @param string $key
 * @param mixed $value
 * @return void
 */
#[Override()]
protected function onAssigning(string $key, mixed $value): void {}
```

## onAssigned
```php
/**
 * Метод вызывается после изменения значения свойства dto
 * @link https://github.com/atlcomgit/dto/blob/master/tests/Examples/Example32/Example32Test.php
 *
 * @param string $key
 * @return void
 */
#[Override()]
protected function onAssigned(string $key): void {}
```

## onException
```php
/**
 * Метод вызывается во время исключения при заполнении dto
 * @link https://github.com/atlcomgit/dto/blob/master/tests/Examples/Example17/Example17Test.php
 *
 * @param Throwable $exception
 * @return void
 * @throws \Exception
 */
#[Override()]
protected function onException(Throwable $exception): void
{
    parent::onException($exception);
}
```
