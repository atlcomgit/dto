# Data Transfer Object (dto)

Dto используется для передачи типизированных данных между методами/сервисами, подготовки данных для сохранения в БД или отправки http запросов, с трансформацией в нужный формат.\
Класс DefaultDto расширяет класс Dto для реализации возможности управления объектом и его данными:\
    - Управление преобразованием типов;\
    - Управление дефолтными значениями;\
    - Управление маппингом свойств при заполнении;\
    - Управление сериализацией объекта в массив или json.\

> Для реализации функционала работы с Dto необходимо расширить объект с публичными свойствами от класса **\Expo\Dto\DefaultDto**.

*Версия 2.42*

##### Создание и заполнение:

* @method static **[create](#пример-01)**(mixed ...$data)\
    *Создает объект Dto из переданных именованных аргументов / ассоциативного массива / объекта / строки json.*

* @method static **[fill](#примеры)**(array $data)\
    *Создает объект Dto из переданного ассоциативного массива.*

* @method static **[collect](#пример-31)**(array $items)\
    *Преобразует массив или коллекцию данных в коллекцию из dto.*

* @method public **[fillFromArray](#пример-21)**(array $data)\
    *Заполняет объект Dto из переданного ассоциативного массива.*

* @method public **[fillFromData](#примеры)**(mixed $data)\
    *Заполняет объект Dto из переданного ассоциативного массива / объекта / строки json.*

* @method public **[fillFromObject](#примеры)**(array $data)\
    *Заполняет объект Dto из переданного объекта с публичными свойствами.*

* @method public **[fillFromDto](#примеры)**(self $data)\
    *Заполняет объект Dto из другого объекта Dto с публичными свойствами.*

* @method public **[fillFromJson](#примеры)**(self $data)\
    *Заполняет объект Dto из строки json.*

* @method public **[merge](#пример-13)**(object $data)\
    *Объединяет объект Dto с переданным ассоциативным массивом.*

* @method public **transformToDto**(string $dtoClass)\
    *Трансформирует объект Dto в объект другого класса Dto.*

##### Методы сериализации (приведение к массиву/json):

* @method public **[toArray](#пример-29)**(?bool $onlyFilled = null)\
    *Сериализация Dto в массив.*

* @method public **[toJson](#пример-30)**($options = 0)\
    *Сериализация Dto в строку json.*

##### Переопределяемые методы в дочернем классе (события):

* @override protected **[defaults](#пример-05)**(): array { return []; }\
    *Задаёт массив значений для свойств при заполнении Dto по умолчанию.*

* @override protected **[mappings](#пример-06)**(): array { return []; }\
    *Задаёт массив имён свойств для маппинга в другие свойства.*

* @override protected **[casts](#пример-08)**(): array { return []; }\
    *Задаёт массив приведения типов свойств при заполнении Dto.*

* @override protected **[onFilling](#пример-11)**(array &$array): void {}\
    *Метод вызывается перед заполнением Dto.*

* @override protected **[onFilled](#пример-12)**(array $array): void {}\
    *Метод вызывается после заполнения Dto.*

* @override protected **[onMerging](#пример-13)**(array &$array): void {}\
    *Метод вызывается перед объединением массива в Dto.*

* @override protected **[onMerged](#пример-14)**(array $array): void {}\
    *Метод вызывается после объединения массива в Dto.*

* @override protected **[onSerializing](#пример-15)**(array &$array): void {}\
    *Метод вызывается перед сериализацией в массив.*

* @override protected **[onSerialized](#пример-16)**(array &$array): void {}\
    *Метод вызывается после сериализации в массив.*

* @override protected **[onException](#пример-17)**(Throwable $exception): void {}\
    *Метод вызывается перед исключением ().*

##### Цепочки методов опций сериализации (для приведения к массиву/json):

* @method public **autoCasts**(bool $autoCasts = true)\
    *Включение/отключение опции сериализации автоматического приведения типов при заполнении Dto.*

* @method public **[autoMappings](#пример-21)**(bool $autoMappings = true)\
    *Включение/отключение опции автоматического маппинга свойств при заполнении Dto или преобразовании в массив.*

* @method public **[onlyFilled](#пример-22)**(bool $onlyFilled = true)\
    *Включение/отключение опции сериализации в массив только заполненных свойств.*

* @method public **[onlyKeys](#пример-23)**(string|array|object ...$data)\
    *Добавление опции сериализации в массив только указанных свойств.*

* @method public **[includeStyles](#пример-24)**(bool $includeStyles = true)\
    *Добавление опции сериализации в массив для добавления разных стилей свойств camel/snake.*

* @method public **[includeArray](#пример-25)**(string|array ...$data)\
    *Добавление опции сериализации в массив для добавления дополнительных свойств.*

* @method public **[excludeKeys](#пример-26)**(string|array ...$data)\
    *Добавление опции сериализации в массив для исключения свойств.*

* @method public **[mappingKeys](#пример-27)**(string|array|object ...$data)\
    *Добавление опции сериализации в массив для маппинга имён свойств.*

* @method public **[serializeKeys](#пример-28)**(string|array|object|bool ...$data)\
    *Добавление опции сериализации в массив для преобразования объектов к скалярному типу.*

* @method public **[for](#примеры)**(object $object)\
    *Добавление опции сериализации в массив для подготовки свойств к заданному объекту/сущности.*

> ${\textsf{\color{red}WARNING}}$\
> ${\textsf{\color{red}После \space каждого \space выполнения \space toArray() \space и \space toJson() \space все \space цепочки \space опций \space сбрасываются.}}$

<hr style="border:1px solid black">

#### Примеры:

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
$exampleArray = $exampleDto->for($carEntity)->toArray();
$exampleArray = $exampleDto->for(CarEntity::class)->toArray(true);
```

---

###### Пример 01
**Работа с методами через Dto.**\
Имеется метод класса, который принимает на вход определённый объект Dto и возвращает другой объект Dto.

[Открыть тест](tests/Examples/Example01/Example01Test.php)

```php
class IdDto extends \Expo\Dto\DefaultDto
{
    public int $markId;
    public int $modelId;
}

class CarDto extends \Expo\Dto\DefaultDto
{
    public string $markName;
    public string $modelName;
}

class CarService
{
    public static function getMarkById(int $markId): string
    {
        // Находим марку авто по $markId
        return 'Lexus';
    }

    public static function getModelById(int $modelId): string
    {
        // Находим модель авто тарифа по $modelId
        return 'RX500';
    }

    public static function getCar(IdDto $dto): CarDto
    {
        return CarDto::create(
            markName: self::getMarkById(1),
            modelName: self::getModelById(2),
        );
    }
}

$carDto = CarService::getCar(IdDto::create(markId: 1, modelId: 2));

/* Вывод результата */
print_r($carDto->toArray());

```

Результат:

```text
[
    'markName' => 'Lexus',
    'modelName' => 'RX500',
]
```

---

###### Пример 02
**Заполнение Dto из массива.**\
Создание объекта Dto и заполнение его свойств из массива.

[Открыть тест](tests/Examples/Example02/Example02Test.php)

```php
class CarDto extends \Expo\Dto\DefaultDto
{
    public string $markName;
    public string $modelName;
}

$array = [
    'markName' => 'Lexus',
    'modelName' => 'RX500',
];

$carDto = CarDto::create($array);

/* Вывод результата */
print_r($carDto->toArray());
```

Результат:

```text
[
    'markName' => 'Lexus',
    'modelName' => 'RX500',
]
```

---

###### Пример 03
**Заполнение Dto из объекта.**\
Создание объекта Dto и заполнение его свойств из другого объекта.

[Открыть тест](tests/Examples/Example03/Example03Test.php)

```php
class CarDto extends \Expo\Dto\DefaultDto
{
    public string $markName;
    public string $modelName;
}

$carDto1 = CarDto::create([
    'markName' => 'Lexus',
    'modelName' => 'RX500',
]);

$carDto2 = CarDto::create($carDto1);

/* Вывод результата */
print_r($carDto2->toArray());
```

Результат:

```text
[
    'markName' => 'Lexus',
    'modelName' => 'RX500',
]
```

---

###### Пример 04
**Заполнение Dto из строки json.**\
Создание объекта Dto и заполнение его свойств из строки формата json.

[Открыть тест](tests/Examples/Example04/Example04Test.php)

```php
class CarDto extends \Expo\Dto\DefaultDto
{
    public string $markName;
    public string $modelName;
}

$carDto = CarDto::create('{"markName": "Lexus", "modelName": "RX500"}');

/* Вывод результата */
print_r($carDto->toArray());
```

Результат:

```text
[
    'markName' => 'Lexus',
    'modelName' => 'RX500',
]
```

---

###### Пример 05
**Заполнение Dto с параметрами по умолчанию.**\
Создание объекта Dto и заполнение его свойств значениями по умолчанию.
Значения из возвращаемого массива в методе defaults присваиваются свойству Dto в случае отсутствия значения для заполнения при его создании.

[Открыть тест](tests/Examples/Example05/Example05Test.php)

```php
class CarDto extends \Expo\Dto\DefaultDto
{
    public string $markName = 'Lexus';
    public string $modelName;

    protected function defaults(): array {
        return [
            'modelName' => 'RX500',
        ];
    }
}

$carDto = CarDto::create();

/* Вывод результата */
print_r($carDto->toArray());
```

Результат:

```text
[
    'markName' => 'Lexus',
    'modelName' => 'RX500',
]
```

---

###### Пример 06
**Заполнение Dto с маппингом свойств из snake_case в camelCase.**\
Создание объекта Dto и заполнение его свойств из других ключей передаваемого массива.
Метод mappings возвращает массив с указанием названия свойств Dto в ключах и значения, который содержит название ключа массива для заполнения.

[Открыть тест](tests/Examples/Example06/Example06Test.php)

```php
class CarDto extends \Expo\Dto\DefaultDto
{
    public string $markName;
    public string $modelName;

    protected function mappings(): array {
        return [
            'markName' => 'mark_name',
            'modelName' => 'model_name',
        ];
    }
}

$carDto = CarDto::create([
    'mark_name' => 'Lexus',
    'model_name' => 'RX500',
]);

/* Вывод результата */
print_r($carDto->toArray());
```

Результат:

```text
[
    'markName' => 'Lexus',
    'modelName' => 'RX500',
]
```

---

###### Пример 07
**Заполнение Dto с маппингом из многоуровневого массива.**\
Создание объекта Dto и заполнение его свойств из других ключей передаваемого массива.
Метод mappings возвращает массив с указанием названия свойств Dto в ключах и значения, который содержит название ключа с точками многоуровневого массива для заполнения.

[Открыть тест](tests/Examples/Example07/Example07Test.php)

```php
class CarDto extends \Expo\Dto\DefaultDto
{
    public string $markName;
    public string $modelName;

    protected function mappings(): array {
        return [
            'markName' => 'mark.name',
            'modelName' => 'model.name',
        ];
    }
}

$carDto = CarDto::create([
    'mark' => [
        'name' => 'Lexus',
    ],
    'model' => [
        'name' => 'RX500',
    ],
]);

/* Вывод результата */
print_r($carDto->toArray());
```

Результат:

```text
[
    'markName' => 'Lexus',
    'modelName' => 'RX500',
]
```

---

###### Пример 08
**Заполнение Dto с преобразованием типов.**\
Создание объекта Dto и заполнение его свойств с преобразованием типом значений из передаваемого массива.
Метод casts возвращает массив для преобразования, в ключе которого указывается название свойства Dto, а в значении тип для преобразования значения из массива заполнения.
Также, для свойства Dto можно указать аттрибут для преобразования типа, класс которого должен содержать метод handle и реализовывать интерфейс AttributeDtoInterface.

[Открыть тест](tests/Examples/Example08/Example08Test.php)

```php
enum CarTypeEnum: string
{
    case OLD = 'old';
    case NEW = 'new';
}

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class YearCast implements \Expo\Dto\Interfaces\AttributeDtoInterface
{
    public function __construct(private ?bool $enabled = null)
    {
    }

    public function handle(string &$key, mixed &$value, mixed $defaultValue, string $dtoClass): void
    {
        $value = $this->enabled ? (int)$value : $value;
    }
}

class CarDto extends \Expo\Dto\DefaultDto
{
    public int $id;
    public CarTypeEnum $type;
    public string $comment;
    #[YearCast(enabled: true)]
    public int $year;

    protected function casts(): array {
        return [
            'id' => 'integer',
            'type' => CarTypeEnum::class,
            'comment' => static fn ($value) => mb_substr($value, 0, 6),
        ];
    }
}

$carDto = CarDto::create([
    'id' => '1',
    'type' => 'new',
    'comment' => 'Примерное описание',
    'year' => '2024',
]);

/* Вывод результата */
print_r($carDto->toArray());
```

Результат:

```text
[
    'id' => 1,
    'type' => enum CarTypeEnum {CarTypeEnum::NEW: 'new'},
    'comment' => 'Пример',
    'year' => 2024,
]
```

---

###### Пример 09
**Заполнение Dto с вложенными Dto.**\
Создание объекта Dto и заполнение его свойств с типом Dto.
Переданный массив передается по цепочке в создаваемые объекты Dto по ключу с названием свойства в родительском Dto.

[Открыть тест](tests/Examples/Example09/Example09Test.php)

```php
class MarkDto extends \Expo\Dto\DefaultDto
{
    public int $id;
    public string $markName;
}

class ModelDto extends \Expo\Dto\DefaultDto
{
    public int $id;
    public string $modelName;
}

class CarDto extends \Expo\Dto\DefaultDto
{
    public MarkDto $markDto;
    public ModelDto $modelDto;

    protected function casts(): array {
        return [
            'markDto' => MarkDto::class,
            'modelDto' => ModelDto::class,
        ];
    }
}

$carDto = CarDto::create([
    'markDto' => [
        'id' => 1,
        'markName' => 'Lexus',
    ],
    'modelDto' => [
        'id' => 2,
        'modelName' => 'RX500',
    ],
]);

/* Вывод результата */
print_r($carDto->toArray());
```

Результат:

```text
[
    'markDto' => object MarkDto {id: 1, markName: 'Lexus'}
    'modelDto' => object ModelDto {id: 2, modelName: 'Lexus'}
]
```

---

###### Пример 10
**Заполнение Dto с вложенными Dto, маппингом свойств и сериализацией.**\
Создание объекта Dto и заполнение его свойств с применением метода mappings и преобразованием к массиву всех его свойств при вызове метода toArray.

[Открыть тест](tests/Examples/Example10/Example10Test.php)

```php
class MarkDto extends \Expo\Dto\DefaultDto
{
    public int $id;
    public string $markName;

    protected function mappings(): array {
        return [
            'markName' => 'name',
        ];
    }
}

class ModelDto extends \Expo\Dto\DefaultDto
{
    public int $id;
    public string $modelName;

    protected function mappings(): array {
        return [
            'modelName' => 'name',
        ];
    }
}

class CarDto extends \Expo\Dto\DefaultDto
{
    public MarkDto $markDto;
    public ModelDto $modelDto;

    protected function casts(): array {
        return [
            'markDto' => MarkDto::class,
            'modelDto' => ModelDto::class,
        ];
    }

    protected function mappings(): array {
        return [
            'markDto' => 'mark',
            'modelDto' => 'model',
        ];
    }
}

$carDto = CarDto::create([
    'mark' => [
        'id' => 1,
        'name' => 'Lexus',
    ],
    'model' => [
        'id' => 2,
        'name' => 'RX500',
    ],
]);

/* Вывод результата */
print_r($carDto->serializeKeys()->toArray());
```

Результат:

```text
[
    'markDto' => [
        'id' => 1,
        'markName' => 'Lexus',
    ],
    'modelDto' => [
        'id' => 2,
        'modelName' => 'RX500',
    ],
]
```

---

###### Пример 11
**Заполнение Dto с событием onFilling (перед заполнением).**\
Создание объекта Dto и заполнение его свойств вызовом метода onFilling перед его заполнением.

[Открыть тест](tests/Examples/Example11/Example11Test.php)

```php
class CarDto extends \Expo\Dto\DefaultDto
{
    public string $markName;
    public string $modelName;

    protected function onFilling(array &$array): void
    {
        $array['markName'] = 'Lexus';
        $array['modelName'] = 'RX500';
    }
}

$carDto = CarDto::create();

/* Вывод результата */
print_r($carDto->toArray());
```

Результат:

```text
[
    'markName' => 'Lexus',
    'modelName' => 'RX500',
]
```

---

###### Пример 12
**Заполнение Dto с событием onFilled (после заполнения).**\
Создание объекта Dto и заполнение его свойств вызовом метода onFilled после его заполнения.

[Открыть тест](tests/Examples/Example12/Example12Test.php)

```php
class CarDto extends \Expo\Dto\DefaultDto
{
    public ?string $markName;
    public ?string $modelName;

    protected function onFilled(array $array): void
    {
        $this->markName = 'Lexus';
        $this->modelName = 'RX500';
    }
}

$carDto = CarDto::create();

/* Вывод результата */
print_r($carDto->toArray());
```

Результат:

```text
[
    'markName' => 'Lexus',
    'modelName' => 'RX500',
]
```

---

###### Пример 13
**Заполнение Dto с событием onMerging (перед объединением).**\
Создание объекта Dto и объединение значений его свойств с массивом уже заполненного Dto и вызовом метода onMerging до его объединения.

[Открыть тест](tests/Examples/Example13/Example13Test.php)

```php
class CarDto extends \Expo\Dto\DefaultDto
{
    public string $markName;
    public ?string $modelName;

    protected function onMerging(array &$array): void
    {
        $array['modelName'] = 'RX500';
    }
}

$carDto = CarDto::create([
    'markName' => 'Lexus',
]);

/* Вывод результата */
print_r($carDto->toArray());

$carDto->merge([
    'modelName' => 'Unknown',
]);

/* Вывод результата */
print_r($carDto->toArray());
```

Результат:

```text
[
    'markName' => 'Lexus',
    'modelName' => null,
]
[
    'markName' => 'Lexus',
    'modelName' => 'RX500',
]
```

---

###### Пример 14
**Заполнение Dto с событием onMerged (после объединения).**\
Создание объекта Dto и объединение значений его свойств с массивом уже заполненного Dto и вызовом метода onMerged после его объединения.

[Открыть тест](tests/Examples/Example14/Example14Test.php)

```php
class CarDto extends \Expo\Dto\DefaultDto
{
    public string $markName;
    public ?string $modelName;

    protected function onMerged(array $array): void
    {
        $this->modelName = 'RX500';
    }
}

$carDto = CarDto::create([
    'markName' => 'Lexus',
]);

/* Вывод результата */
print_r($carDto->toArray());

$carDto->merge([
    'modelName' => 'Unknown',
]);

/* Вывод результата */
print_r($carDto->toArray());
```

Результат:

```text
[
    'markName' => 'Lexus',
    'modelName' => null,
]
[
    'markName' => 'Lexus',
    'modelName' => 'RX500',
]
```

---

###### Пример 15
**Заполнение Dto с событием onSerializing (перед преобразованием в массив).**\
Создание объекта Dto и преобразование его свойств и значений в массив с вызовом метода onSerializing до его преобразования в методе toArray.

[Открыть тест](tests/Examples/Example15/Example15Test.php)

```php
class CarDto extends \Expo\Dto\DefaultDto
{
    public string $markName = 'Lexus';

    protected function onSerializing(array &$array): void
    {
        $array['modelName'] = 'RX500';
    }
}

$carArray = CarDto::create()->toArray();

/* Вывод результата */
print_r($carArray);
```

Результат:

```text
[
    'markName' => 'Lexus',
    'modelName' => RX500,
]
```

---

###### Пример 16
**Заполнение Dto с событием onSerialized (после преобразования в массив).**\
Создание объекта Dto и преобразование его свойств и значений в массив с вызовом метода onSerialized после его преобразования в методе toArray.

[Открыть тест](tests/Examples/Example16/Example16Test.php)

```php
class CarDto extends \Expo\Dto\DefaultDto
{
    public string $markName = 'Lexus';
    public string $modelName = '';

    protected function onSerialized(array &$array): void
    {
        $array['modelName'] = 'RX500';
    }
}

$carArray = CarDto::create()->toArray();

/* Вывод результата */
print_r($carArray);
```

Результат:

```text
[
    'markName' => 'Lexus',
    'modelName' => RX500,
]
```

---

###### Пример 17
**Заполнение Dto с событием onException (при исключении).**\
Создание объекта Dto и вызовом метода onException при исключительной ситуации во время заполнения или преобразования в массив его свойств.

[Открыть тест](tests/Examples/Example17/Example17Test.php)

```php
class CarDto extends \Expo\Dto\DefaultDto
{
    public string $markName;
    public string $modelName;

    protected function onException(\Throwable $exception): void
    {
        // Сохраняем $exception в лог
        throw $exception;
    }
}

$carDto = CarDto::create();
```

Результат:

```text
    Exception
```

---

###### Пример 18
**Заполнение Dto с автоматическим приведением стилей camelCase и snake_case.**\
Создание объекта Dto и заполнение его свойств с автоматическим маппингом названий ключей в передаваемом массиве.

[Открыть тест](tests/Examples/Example18/Example18Test.php)

```php
class CarDto extends \Expo\Dto\DefaultDto
{
    public const AUTO_MAPPINGS_ENABLED = true;

    public string $markName;
    public string $modelName;
}

$carDto = CarDto::create([
    'mark_name' => 'Lexus',
    'model_name' => 'RX500',
]);

/* Вывод результата */
print_r($carDto->toArray());
```

Результат:

```text
[
    'markName' => 'Lexus',
    'modelName' => RX500,
]
```

---

###### Пример 19
**Заполнение Dto с автоматическим преобразованием типов.**\
Создание объекта Dto и заполнение его свойств с автоматическим преобразованием типов значений в передаваемом массиве.
Поддерживаются: DefaultDto, DateTime, Enum, string, integer, float, bool, null.

[Открыть тест](tests/Examples/Example19/Example19Test.php)

```php
enum CarTypeEnum: string
{
    case OLD = 'old';
    case NEW = 'new';
}

class CarDto extends \Expo\Dto\DefaultDto
{
    public const AUTO_CASTS_ENABLED = true;

    public string $markName;
    public CarTypeEnum $type;
    public \DateTime $date;
}

$carDto = CarDto::create([
    'markName' => 'Lexus',
    'type' => 'new',
    'date' => '2024-01-01',
]);

/* Вывод результата */
print_r($carDto->toArray());
```

Результат:

```text
[
    'markName' => 'Lexus',
    'type' => enum CarTypeEnum {CarTypeEnum::NEW: 'new'},
    'date' => object DateTime {date: '2024-01-01'},
]
```

---

###### Пример 20
**Сериализация Dto в массив с автоматическим преобразованием к скалярным типам.**\
Преобразование свойств объекта Dto к массиву с автоматическим преобразованием типов к скалярному.
Поддерживаются: DefaultDto, DateTime, Enum, object, array.

[Открыть тест](tests/Examples/Example20/Example20Test.php)

```php
class CarTypeEnum
{
    case OLD = 'old';
    case NEW = 'new';
}

class CarDto extends \Expo\Dto\DefaultDto
{
    public const AUTO_CASTS_ENABLED = true;
    public const AUTO_SERIALIZE_ENABLED = true;

    public string $markName;
    public CarTypeEnum $type;
    public \DateTime $date;
}

$carDto = CarDto::create([
    'markName' => 'Lexus',
    'type' => 'new',
    'date' => '2024-01-01 00:00:00',
]);

/* Вывод результата */
print_r($carDto->toArray());
```

Результат:

```text
[
    'markName' => 'Lexus',
    'type' => 'new',
    'date' => 1704067200,
]
```

---

###### Пример 21
**Сериализация Dto в массив с использованием autoMappings.**\
Заполнение свойств Dto с включением опции автоматического маппинга названий ключей из массива заполнения.

[Открыть тест](tests/Examples/Example21/Example21Test.php)

```php
class CarDto extends \Expo\Dto\DefaultDto
{
    public string $markName = 'Lexus';
    public string $modelName = 'RX500';
}

$carDto = (new CarDto())->autoMappings()->fillFromArray([
    'mark_name' => 'Lexus',
    'model_name' => 'RX500',
]);

/* Вывод результата */
print_r($carDto->toArray());
```

Результат:

```text
[
    'markName' => 'Lexus',
    'modelName' => 'RX500',
]
```

---

###### Пример 22
**Сериализация Dto в массив с использованием onlyFilled.**\
Преобразование свойств объекта Dto к массиву с включением опции вывода только заполненных значений.

[Открыть тест](tests/Examples/Example22/Example22Test.php)

```php
class CarDto extends \Expo\Dto\DefaultDto
{
    public string $markName = 'Lexus';
    public ?string $modelName = null;
}

$carDto = CarDto::create();

/* Вывод результата */
print_r($carDto->onlyFilled()->toArray());
```

Результат:

```text
[
    'markName' => 'Lexus',
]
```

---

###### Пример 23
**Сериализация Dto в массив с использованием onlyKeys.**\
Преобразование свойств объекта Dto к массиву с включением опции вывода только указанных свойств.

[Открыть тест](tests/Examples/Example23/Example23Test.php)

```php
class CarDto extends \Expo\Dto\DefaultDto
{
    public string $markName = 'Lexus';
    public string $modelName = 'RX500';
}

$carDto = CarDto::create();

/* Вывод результата */
print_r($carDto->onlyKeys(['markName'])->toArray());
```

Результат:

```text
[
    'markName' => 'Lexus',
]
```

---

###### Пример 24
**Сериализация Dto в массив с использованием includeStyles.**\
Преобразование свойств объекта Dto к массиву с включением опции добавления в вывод стилей свойств в camelCase и snake_case.

[Открыть тест](tests/Examples/Example24/Example24Test.php)

```php
class CarDto extends \Expo\Dto\DefaultDto
{
    public string $markName = 'Lexus';
    public string $modelName = 'RX500';
}

$carDto = CarDto::create();

/* Вывод результата */
print_r($carDto->includeStyles()->toArray());
```

Результат:

```text
[
    'markName' => 'Lexus',
    'modelName' => 'RX500',
    'mark_name' => 'Lexus',
    'model_name' => 'RX500',
]
```

---

###### Пример 25
**Сериализация Dto в массив с использованием includeArray.**\
Преобразование свойств объекта Dto к массиву с включением опции добавления в вывод дополнительного массива.

[Открыть тест](tests/Examples/Example25/Example25Test.php)

```php
class CarDto extends \Expo\Dto\DefaultDto
{
    public string $markName = 'Lexus';
}

$carDto = CarDto::create();

/* Вывод результата */
print_r($carDto->includeArray(['modelName' => 'RX500'])->toArray());
```

Результат:

```text
[
    'markName' => 'Lexus',
    'modelName' => 'RX500',
]
```

---

###### Пример 26
**Сериализация Dto в массив с использованием excludeKeys.**\
Преобразование свойств объекта Dto к массиву с включением опции исключения из вывода указанных свойств.

[Открыть тест](tests/Examples/Example26/Example26Test.php)

```php
class CarDto extends \Expo\Dto\DefaultDto
{
    public string $markName = 'Lexus';
    public string $modelName = 'RX500';
}

$carDto = CarDto::create();

/* Вывод результата */
print_r($carDto->excludeKeys(['modelName'])->toArray());
```

Результат:

```text
[
    'markName' => 'Lexus',
]
```

---

###### Пример 27
**Сериализация Dto в массив с использованием mappingKeys.**\
Преобразование свойств объекта Dto к массиву с включением опции маппинга названий свойств в выводе.

[Открыть тест](tests/Examples/Example27/Example27Test.php)

```php
class CarDto extends \Expo\Dto\DefaultDto
{
    public string $markName = 'Lexus';
    public string $modelName = 'RX500';
}

$carDto = CarDto::create();

/* Вывод результата */
print_r($carDto->mappingKeys(['markName' => 'mark_name'])->toArray());
```

Результат:

```text
[
    'mark_name' => 'Lexus',
    'modelName' => 'RX500',
]
```

---

###### Пример 28
**Сериализация Dto в массив с использованием serializeKeys.**\
Преобразование свойств объекта Dto к массиву с включением опции сериализации всех/указанных вложенных свойств и приведения объектов к скалярному типу.

[Открыть тест](tests/Examples/Example28/Example28Test.php)

```php
class MarkDto extends \Expo\Dto\DefaultDto
{
    public int $id;
    public string $markName;
}

class ModelDto extends \Expo\Dto\DefaultDto
{
    public int $id;
    public string $modelName;
}

class CarDto extends \Expo\Dto\DefaultDto
{
    public MarkDto $markDto;
    public ModelDto $modelDto;
}

$carDto = CarDto::create(
    markDto: MarkDto::create(id: 1, markName: 'Lexus'),
    modelDto: ModelDto::create(id: 2, modelName: 'RX500'),
);

/* Вывод результата */
print_r($carDto->serializeKeys(['markDto'])->toArray());

/* Вывод результата */
print_r($carDto->serializeKeys(true)->toArray());
```

Результат:

```text
[
    'markDto' => [
        'id' => 1,
        'markName' => 'Lexus',
    ],
    'modelDto' => object ModelDto {id: 2, modelName: 'RX500'},
]
[
    'markDto' => [
        'id' => 1,
        'markName' => 'Lexus',
    ],
    'modelDto' => [
        'id' => 2,
        'modelName' => 'RX500',
    ],
]
```

---

###### Пример 29
**Сериализация Dto в массив с использованием toArray.**\
Преобразование свойств объекта Dto к массиву.

[Открыть тест](tests/Examples/Example29/Example29Test.php)

```php
class CarDto extends \Expo\Dto\DefaultDto
{
    public string $markName = 'Lexus';
    public string $modelName = 'RX500';
}

/* Вывод результата */
print_r(CarDto::create()->toArray());
```

Результат:

```text
[
    'markName' => 'Lexus',
    'modelName' => 'RX500',
]
```

---

###### Пример 30
**Сериализация Dto в строку с использованием toJson.**\
Преобразование свойств объекта Dto к строке формата json.

[Открыть тест](tests/Examples/Example30/Example30Test.php)

```php
class CarDto extends \Expo\Dto\DefaultDto
{
    public string $markName = 'Lexus';
    public string $modelName = 'RX500';
}

/* Вывод результата */
echo CarDto::create()->toJson();
```

Результат:

```text
{"markName": "Lexus", "modelName": "RX500"}
```

---

###### Пример 31
**Заполнение Dto с массивами объектов через casts и аттрибут Collection.**\
Создание объекта Dto и заполнение его свойств с массивами объектов указанного типа (коллекции).
Для преобразования массива (коллекции) к массиву объектов необходимо указать в методе casts свойству возвращаемый массив с названием класса объектов или задать свойству аттрибут Collection с названием класса объектов.

[Открыть тест](tests/Examples/Example31/Example31Test.php)

```php
class MarkDto extends \Expo\Dto\DefaultDto
{
    public int $id;
    public string $markName;
}

class ModelDto extends \Expo\Dto\DefaultDto
{
    public int $id;
    public string $modelName;
}

class CarDto extends \Expo\Dto\DefaultDto
{
    /** @var array<MarkDto> */
    public array $markNames;

    /** @var array<ModelDto> */
    #[\Expo\Dto\Attributes\Collection(ModelDto::class)]
    public array $modelNames;

    protected function casts(): array
    {
        return [
            'markNames' => [MarkDto::class],
        ];
    }
}

$array = [
    'markNames' => [
        ['id' => 1, 'markName' => 'Lexus'],
        ['id' => 2, 'markName' => 'Toyota'],
    ],
    'modelNames' => [
        ['id' => 3, 'modelName' => 'RX500'],
        ['id' => 4, 'modelName' => 'RAV4'],
    ],
];

$carDto = CarDto::create($array);

/* Вывод результата */
print_r($carDto->toArray());
```

Результат:

```text
[
    'markNames' => [
        0 => object MarkDto {id: 1, markName: 'Lexus'},
        1 => object MarkDto {id: 1, markName: 'Toyota'},
    ],
    'modelNames' => [
        0 => object ModelDto {id: 1, modelName: 'RX500'},
        1 => object ModelDto {id: 1, modelName: 'RAV4'},
    ],
]
```