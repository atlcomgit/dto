# Laravel

## Пример Dependency Injection Dto вместо Request в Laravel

##### Файл app/Providers/DtoServiceProvider.php
Добавляем сервис провайдер для обработки Dto (dependency injection)

```php
namespace App\Providers;

use Atlcom\Dto;
use Atlcom\Exceptions\DtoException;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

use Throwable;

class DtoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->resolving(Dto::class, function (Dto $dto, Application $app) {
            try {
                return $dto->fillFromRequest(request()->toArray());
            } catch (Throwable $exception) {
                throw new DtoException($exception->getMessage());
            }
        });
    }
}
```

##### Файл config/app.php
Подключаем провайдер DtoServiceProvider в проект

```php
return [
    // ...

    'providers' => [
        // ...
        App\Providers\DtoServiceProvider::class,
    ],
];
```

##### Файл app/Dto/ExampleDto.php
Создаём своё dto

```php
namespace App\Dto;

use Atlcom\Dto;
use Illuminate\Contracts\Support\Arrayable;

class ExampleDto extends Dto implements Arrayable
{
    public string $name;

    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
        ];
    }
}
```

##### Файл app/Http/Controllers/ExampleController.php
Инъектим dto в метод контроллера

```php
namespace App\Http\Controllers;

use App\Dto\ExampleDto;
use App\Http\Controllers\Controller;

class ExampleController extends Controller
{
    public function index(ExampleDto $dto): array
    {
        // $dto->name
    }
}
```

##### Файл app/Models/Example.php
Используем типизацию свойств Dto для кастов в модели

```php
namespace App\Models;

use App\Dto\ExampleDto;
use Illuminate\Database\Eloquent\Model;

class Example extends Model
{
    //...

    protected function casts(): array
    {
        return Arr::map(
            ExampleDto::getPropertiesWithFirstType(useMappings: true),
            static fn (string $item): string => Str::swap([
                'int' => 'integer',
                'bool' => 'boolean',
                Carbon::class => 'datetime',
            ], $item),
        );
    }
}
```
