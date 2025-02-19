# Data Transfer Object (dto)

Dto используется для передачи типизированных данных между слоями приложения, подготовки данных для сохранения в БД или отправки http запросов, с трансформацией в нужный формат.
Класс Dto расширяет функционал объекта для реализации возможности управления объектом и его данными:
    - Управление преобразованием типов;
    - Управление дефолтными значениями;
    - Управление маппингом свойств при заполнении;
    - Управление сериализацией объекта в массив или json;
    - Управление хуками объекта.

> Для реализации функционала работы с Dto необходимо расширить объект от класса **\Atlcom\Dto** или подключить к своему объекту трейт **\Atlcom\Traits\AsDto**.
>
> ```php
> class MyDto extends \Atlcom\Dto {}
> ```
>
> **ИЛИ**
>
> ```php
> class MyDto {
>     use \Atlcom\Traits\AsDto;
> }
> ```

**${\textsf{\color{red}Начиная с Dto версии 2.61 поддерживается PHP 8.2 и выше}}$**

<hr style="border:1px solid black">

## Описание методов

##### Создание и заполнение:

@method public static **[create](#пример-01)**(mixed ...$data)
    *Создает объект Dto из переданных именованных аргументов / ассоциативного массива / объекта / строки json.*
@method public static **[fill](#примеры)**(array $data)
    *Создает объект Dto из переданного ассоциативного массива.*
@method static **[collect](#пример-31)**(array $items)
    *Преобразует массив или коллекцию данных в коллекцию из dto.*
@method public **[fillFromArray](#пример-21)**(array $data)
    *Заполняет объект Dto из переданного ассоциативного массива.*
@method public **[fillFromData](#примеры)**(mixed $data)
    *Заполняет объект Dto из переданного ассоциативного массива / объекта / строки json.*
@method public **[fillFromObject](#примеры)**(array $data)
    *Заполняет объект Dto из переданного объекта с публичными свойствами.*
@method public **[fillFromDto](#примеры)**(self $data)
    *Заполняет объект Dto из другого объекта Dto с публичными свойствами.*
@method public **[fillFromJson](#примеры)**(self $data)
    *Заполняет объект Dto из строки json.*
@method public **[merge](#пример-13)**(object $data)
    *Объединяет объект Dto с переданным ассоциативным массивом.*
@method public **[transformToDto](#пример-34)**(string $dtoClass, array $array = [])
    *Трансформирует объект Dto в объект другого класса Dto и дополняет данными из массива.*
@method public **[isEmpty](#пример-39)**()
    *Проверяет dto на заполнение хотя бы одного свойства.*
@method public static **[getProperties](#пример-41)**()
    *Возвращает массив свойств dto.*
@method public static **[getPropertiesWithFirstType](#пример-41)**()
    *Возвращает массив всех свойств dto с его первым типом.*
@method public static **[getPropertiesWithAllTypes](#пример-41)**()
    *Возвращает массив всех свойств dto со всеми его типами.*

##### Методы сериализации (приведение к массиву/json):

@method public **[toArray](#пример-29)**(?bool $onlyFilled = null)
    *Сериализация Dto в массив.*@method public static **[toArrayBlank](#пример-40)**()
    *Возвращает массив с пустыми значениями всех свойств dto.*@method public static **[toArrayBlankRecursive](#пример-40)**()
    *Возвращает массив с пустыми значениями всех свойств dto с рекурсией по объектам.*\

* @method public **[toJson](#пример-30)**($options = 0)
  *Сериализация Dto в строку json.*

##### Переопределяемые методы в дочернем классе (события):

@override protected **[defaults](#пример-05)**(): array { return []; }
    *Задаёт массив значений для свойств при заполнении Dto по умолчанию.*
@override protected **[mappings](#пример-06)**(): array { return []; }
    *Задаёт массив имён свойств для маппинга в другие свойства.*
@override protected **[casts](#пример-08)**(): array { return []; }
    *Задаёт массив приведения типов свойств при заполнении Dto.*
@override protected **[exceptions](#пример-33)**(string $messageCode, array $messageItems): string {}
    *Возвращает сообщение об ошибке по его коду при работе с Dto.*
@override protected **[onCreating](#пример-44)**(array &$data): void {}
    *Метод-хук вызывается перед созданием и заполнением Dto.*
@override protected **[onCreated](#пример-44)**(array $data): void {}
    *Метод-хук вызывается после создания и заполнения Dto.*
@override protected **[onFilling](#пример-11)**(array &$array): void {}
    *Метод-хук вызывается перед заполнением Dto.*
@override protected **[onFilled](#пример-12)**(array $array): void {}
    *Метод-хук вызывается после заполнения Dto.*
@override protected **[onMerging](#пример-13)**(array &$array): void {}
    *Метод-хук вызывается перед объединением массива в Dto.*
@override protected **[onMerged](#пример-14)**(array $array): void {}
    *Метод-хук вызывается после объединения массива в Dto.*
@override protected **[onSerializing](#пример-15)**(array &$array): void {}
    *Метод-хук вызывается перед сериализацией в массив.*
@override-хук protected **[onAssigning](#пример-32)**(string $key, mixed $value): void {}
    *Выполняется перед изменением значения свойства Dto.*
@override-хук protected **[onAssigned](#пример-32)**(string $key): void {}
    *Метод вызывается после изменения значения свойства Dto.*
    *Для вызова метода при изменении отдельного свойства требуется PROTECTED или PRIVATE у этого свойства.*
@override-хук protected **[onException](#пример-32)**(Throwable $exception): void {}
    *Метод вызывается перед исключением ().*
    *Для вызова метода при изменении отдельного свойства требуется PROTECTED или PRIVATE у этого свойства.*

##### Цепочки опций сериализации (для приведения к array/json):

@method public **autoCasts**(bool $autoCasts = true)
    *Включение/отключение опции сериализации автоматического приведения типов при заполнении Dto.*@method public **[autoMappings](#пример-21)**(bool $autoMappings = true)
    *Включение/отключение опции автоматического маппинга свойств при заполнении Dto или преобразовании в массив.*@method public **[onlyFilled](#пример-22)**(bool $onlyFilled = true)
    *Включение/отключение опции сериализации в массив только заполненных свойств.*@method public **[onlyNotNull](#пример-22)**(bool $onlyNotNull = true)
    *Включение/отключение опции сериализации в массив только не null свойств.*@method public **[onlyKeys](#пример-23)**(string|array|object ...$data)
    *Добавление опции сериализации в массив только указанных свойств.*@method public **[includeStyles](#пример-24)**(bool $includeStyles = true)
    *Добавление опции сериализации в массив для добавления разных стилей свойств camel/snake.*@method public **[includeArray](#пример-25)**(string|array ...$data)
    *Добавление опции сериализации в массив для добавления дополнительных свойств.*@method public **[excludeKeys](#пример-26)**(string|array ...$data)
    *Добавление опции сериализации в массив для исключения свойств.*@method public **[mappingKeys](#пример-27)**(string|array|object ...$data)
    *Добавление опции сериализации в массив для маппинга имён свойств.*@method public **[serializeKeys](#пример-28)**(string|array|object|bool ...$data)
    *Добавление опции сериализации в массив для преобразования объектов к скалярному типу.*@method public **[withProtectedKeys](#пример-32)**(string|array|object|bool ...$data)
    *Добавление опции сериализации в массив для добавления protected свойств.*@method public **[withPrivateKeys](#пример-32)**(string|array|object|bool ...$data)
    *Добавление опции сериализации в массив для добавления private свойств.*@method public **[withoutOptions](#пример-36)**(string|array|object|bool ...$data)
    *Добавление опции отключения всех ранее установленных опций.*@method public **[withCustomOptions](#пример-43)**()
    *Включает опцию при преобразовании в массив: преобразование customOptions свойств к массиву.*@method public **[customOptions](#пример-37)**(array $options)
    *Добавление своих опций в dto.*@method public **[setCustomOption](#пример-42)**()
    *Добавляет свою опцию в dto.*@method public **[getCustomOption](#пример-42)**()
    *Возвращает значение своей опции в dto.*@method public **[for](#пример-26)**(object $object)
    *Добавление опции сериализации в массив для подготовки свойств к заданному объекту/сущности.*

> ${\textsf{\color{red}ВНИМАНИЕ}}$
> После каждого выполнения toArray() и toJson() все цепочки опций сбрасываются.

<hr style="border:1px solid black">

## Примеры:

```php
$exampleDto = ExampleDto::create(a: 1, b: 2, c: null);
$exampleDto = ExampleDto::fill(['a' => 1, 'b' => 2, 'c' => null]);
```

```php
$exampleDto = ExampleDto::create(['a' => 1, 'b' => 2]);
$exampleDto = (new ExampleDto())->fillFromArray(['a' => 1, 'b' => 2]);

$exampleDto = ExampleDto::create((object)['a' => 1, 'b' => 2]);
$exampleDto = (new ExampleDto())->fillFromObject((object)['a' => 1, 'b' => 2]);

$exampleDto = ExampleDto::create('{"a": 1, "b": 2}');
$exampleDto = (new ExampleDto())->fillFromJson('{"a": 1, "b": 2}');
```

```php
$exampleDto = ExampleDto::fill(['a' => 1, 'c' => 3])
    ->merge(['b' => 2]);
$exampleArray = $exampleDto
    ->onlyKeys(['a', 'b'])
    ->excludeKeys(['c'])
    ->mappingKeys(['a' => 'aa', 'b' => 'bb'])
    ->serializeKeys(['a', 'b'])
    ->toArray();
```

```php
$carEntity = new CarEntity();
$exampleDto = ExampleDto::create();
$exampleArray = $exampleDto->for($carEntity)->toArray();
$exampleArray = $exampleDto->for(CarEntity::class)->toArray(true);
```

## Пример Dependency Injection Dto вместо Request в Laravel

[Открыть пример для Laravel](LARAVEL.md)

## Примеры из тестов

[Открыть все примеры](EXAMPLES.md)

<hr style="border:1px solid black">

## Установка

Добавление пакета Dto в проект

1. Добавьте в **composer.json**

```json
{
    "require": {
        "atlcom/dto": "^2.55"
    },
    "repositories": {
        "gtl.atlcom/dto": {
            "type": "vcs",
            "url": "https://github.com/atlcomgit/dto.git"
        }
    }
}
```

2. Выполните команду

```bash
composer install
```

```

```
