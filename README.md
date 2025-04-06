# Data Transfer Object (dto)

Dto используется для передачи типизированных данных между слоями приложения, подготовки данных для сохранения в БД или отправки http запросов, с трансформацией в нужный формат.

Класс Dto расширяет функционал объекта для реализации возможности управления объектом и его данными:\

- Управление преобразованием типов;\
- Управление дефолтными значениями;\
- Управление маппингом свойств при заполнении;\
- Управление сериализацией объекта в массив или json;\
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

**${\textsf{\color{red}Начиная с версии Dto 2.61 поддерживается PHP 8.2 и выше}}$**

<hr style="border:1px solid black">

## Установка

```
composer require atlcom/dto
```

## История изменений

[Открыть историю](docs/CHANGELOG.md)

## Описание методов

### Создание и заполнение:

@method public static **[create](docs/EXAMPLES.md#пример-01)**(mixed ...$data)\
_Создает объект Dto из переданных именованных аргументов / ассоциативного массива / объекта / строки json._\
\
@method public static **[fill](#примеры)**(array $data)\
_Создает объект Dto из переданного ассоциативного массива._\
\
@method static **[collect](docs/EXAMPLES.md#пример-31)**(array $items)\
_Преобразует массив или коллекцию данных в коллекцию из dto._\
\
@method public **[fillFromArray](docs/EXAMPLES.md#пример-21)**(array $data)\
_Заполняет объект Dto из переданного ассоциативного массива._\
\
@method public **[fillFromData](#примеры)**(mixed $data)\
_Заполняет объект Dto из переданного ассоциативного массива / объекта / строки json._\
\
@method public **[fillFromObject](#примеры)**(array $data)\
_Заполняет объект Dto из переданного объекта с публичными свойствами._\
\
@method public **[fillFromDto](#примеры)**(self $data)\
_Заполняет объект Dto из другого объекта Dto с публичными свойствами._\
\
@method public **[fillFromJson](#примеры)**(self $data)\
_Заполняет объект Dto из строки json._\
\
@method public **[merge](docs/EXAMPLES.md#пример-13)**(object $data)\
_Объединяет объект Dto с переданным ассоциативным массивом._\
\
@method public **[clear](docs/EXAMPLES.md#пример-48)**()\
_Очищает все свойства Dto._\
\
@method public **[transformToDto](docs/EXAMPLES.md#пример-34)**(string $dtoClass, array $array = [])\
_Трансформирует объект Dto в объект другого класса Dto и дополняет данными из массива._\
\
@method public **[isEmpty](docs/EXAMPLES.md#пример-39)**()\
_Проверяет dto на заполнение хотя бы одного свойства._\
\
@method public static **[getProperties](docs/EXAMPLES.md#пример-41)**()\
_Возвращает массив свойств dto._\
\
@method public static **[getPropertiesWithFirstType](docs/EXAMPLES.md#пример-41)**()\
_Возвращает массив всех свойств dto с его первым типом._\
\
@method public static **[getPropertiesWithAllTypes](docs/EXAMPLES.md#пример-41)**()\
_Возвращает массив всех свойств dto со всеми его типами._

### Методы сериализации (приведение к массиву/json):

@method public **[toArray](docs/EXAMPLES.md#пример-29)**(?bool $onlyFilled = null)\
_Сериализация Dto в массив._\
\
@method public static **[toArrayBlank](docs/EXAMPLES.md#пример-40)**()\
_Возвращает массив с пустыми значениями всех свойств dto._\
\
@method public static **[toArrayBlankRecursive](docs/EXAMPLES.md#пример-40)**()\
_Возвращает массив с пустыми значениями всех свойств dto с рекурсией по объектам._\
\
@method public **[toJson](docs/EXAMPLES.md#пример-30)**($options = 0)\
_Сериализация Dto в строку json._

### Переопределяемые методы в дочернем классе (события):

@override @method protected **[defaults](docs/EXAMPLES.md#пример-05)**(): array { return []; }\
_Задаёт массив значений для свойств при заполнении Dto по умолчанию._\
\
@override @method protected **[mappings](docs/EXAMPLES.md#пример-06)**(): array { return []; }\
_Задаёт массив имён свойств для маппинга в другие свойства._\
\
@override @method protected **[casts](docs/EXAMPLES.md#пример-08)**(): array { return []; }\
_Задаёт массив приведения типов свойств при заполнении Dto._\
\
@override @method protected **[exceptions](docs/EXAMPLES.md#пример-33)**(string $messageCode, array $messageItems): string {}\
_Возвращает сообщение об ошибке по его коду при работе с Dto._\
\
@override @method protected **[onCreating](docs/EXAMPLES.md#пример-44)**(array &$data): void {}\
_Метод-хук вызывается перед созданием и заполнением Dto._\
\
@override @method protected **[onCreated](docs/EXAMPLES.md#пример-44)**(array $data): void {}\
_Метод-хук вызывается после создания и заполнения Dto._\
\
@override @method protected **[onFilling](docs/EXAMPLES.md#пример-11)**(array &$array): void {}\
_Метод-хук вызывается перед заполнением Dto._\
\
@override @method protected **[onFilled](docs/EXAMPLES.md#пример-12)**(array $array): void {}\
_Метод-хук вызывается после заполнения Dto._\
\
@override @method protected **[onMerging](docs/EXAMPLES.md#пример-13)**(array &$array): void {}\
_Метод-хук вызывается перед объединением массива в Dto._\
\
@override @method protected **[onMerged](docs/EXAMPLES.md#пример-14)**(array $array): void {}\
_Метод-хук вызывается после объединения массива в Dto._\
\
@override @method protected **[onSerializing](docs/EXAMPLES.md#пример-15)**(array &$array): void {}\
_Метод-хук вызывается перед сериализацией в массив._\
\
@override @method protected **[onAssigning](docs/EXAMPLES.md#пример-32)**(string $key, mixed $value): void {}\
_Метод-хук выполняется перед изменением значения свойства Dto._\
\
@override @method protected **[onAssigned](docs/EXAMPLES.md#пример-32)**(string $key): void {}\
_Метод-хук вызывается после изменения значения свойства Dto._\
_Для вызова метода при изменении отдельного свойства требуется PROTECTED или PRIVATE у этого свойства._\
\
@override @method protected **[onException](docs/EXAMPLES.md#пример-32)**(Throwable $exception): void {}\
_Метод-хук вызывается перед исключением ()._\
_Для вызова метода при изменении отдельного свойства требуется PROTECTED или PRIVATE у этого свойства._

### Цепочки опций сериализации (для приведения к array/json):

@method public **autoCasts**(bool $autoCasts = true)\
_Включение/отключение опции сериализации автоматического приведения типов при заполнении Dto._\
\
@method public **[autoMappings](docs/EXAMPLES.md#пример-21)**(bool $autoMappings = true)\
_Включение/отключение опции автоматического маппинга свойств при заполнении Dto или преобразовании в массив._\
\
@method public **[onlyFilled](docs/EXAMPLES.md#пример-22)**(bool $onlyFilled = true)\
_Включение/отключение опции сериализации в массив только заполненных свойств._\
\
@method public **[onlyNotNull](docs/EXAMPLES.md#пример-22)**(bool $onlyNotNull = true)\
_Включение/отключение опции сериализации в массив только не null свойств._\
\
@method public **[onlyKeys](docs/EXAMPLES.md#пример-23)**(string|array|object ...$data)\
_Добавление опции сериализации в массив только указанных свойств._\
\
@method public **[includeStyles](docs/EXAMPLES.md#пример-24)**(bool $includeStyles = true)\
_Добавление опции сериализации в массив для добавления разных стилей свойств camel/snake._\
\
@method public **[includeArray](docs/EXAMPLES.md#пример-25)**(string|array ...$data)\
_Добавление опции сериализации в массив для добавления дополнительных свойств._\
\
@method public **[excludeKeys](docs/EXAMPLES.md#пример-26)**(string|array ...$data)\
_Добавление опции сериализации в массив для исключения свойств._\
\
@method public **[mappingKeys](docs/EXAMPLES.md#пример-27)**(string|array|object ...$data)\
_Добавление опции сериализации в массив для маппинга имён свойств._\
\
@method public **[serializeKeys](docs/EXAMPLES.md#пример-28)**(string|array|object|bool ...$data)\
_Добавление опции сериализации в массив для преобразования объектов к скалярному типу._\
\
@method public **[withProtectedKeys](docs/EXAMPLES.md#пример-32)**(string|array|object|bool ...$data)\
_Добавление опции сериализации в массив для добавления protected свойств._\
\
@method public **[withPrivateKeys](docs/EXAMPLES.md#пример-32)**(string|array|object|bool ...$data)\
_Добавление опции сериализации в массив для добавления private свойств._\
\
@method public **[withoutOptions](docs/EXAMPLES.md#пример-36)**(string|array|object|bool ...$data)\
_Добавление опции отключения всех ранее установленных опций._\
\
@method public **[withCustomOptions](docs/EXAMPLES.md#пример-43)**()\
_Включает опцию при преобразовании в массив: преобразование customOptions свойств к массиву._\
\
@method public **[customOptions](docs/EXAMPLES.md#пример-37)**(array $options)\
_Добавление своих опций в dto._\
\
@method public **[setCustomOption](docs/EXAMPLES.md#пример-42)**()\
_Добавляет свою опцию в dto._\
\
@method public **[getCustomOption](docs/EXAMPLES.md#пример-42)**()\
_Возвращает значение своей опции в dto._\
\
@method public **[for](docs/EXAMPLES.md#пример-26)**(object $object)\
_Добавление опции сериализации в массив для подготовки свойств к заданному объекту/сущности._

### Реализация интерфейсов:

@interface **[ArrayAccess]()**\
_Включает реализацию интерфейса ArrayAccess для работы с dto как с массивом._\
\
@interface **[Countable]()**\
_Включает реализацию интерфейса Countable для включения метода count()._\
\
@interface **[IteratorAggregate]()**\
_Включает реализацию интерфейса IteratorAggregate для включения метода getIterator()._\
\
@interface **[JsonSerializable]()**\
_Включает реализацию интерфейса JsonSerializable для метода json_encode($dto)._\
\
@interface **[Serializable]()**\
_Включает реализацию интерфейса Serializable для методов serialize($dto)/unserialize($dto)._\
\
@interface **[Stringable]()**\
_Включает реализацию интерфейса Stringable для работы с dto как со строкой (string)$dto._

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
$exampleArray = $exampleDto->for(CarEntity::class)->toArray();
```

## Пример Dependency Injection Dto вместо Request в Laravel

[Открыть пример для Laravel](docs/LARAVEL.md)

## Примеры из тестов

[Открыть все примеры](docs/EXAMPLES.md)

<hr style="border:1px solid black">
