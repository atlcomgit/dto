# Laravel

## Пример Dependency Injection Dto вместо Request в Laravel

##### Файл app/Defaults/DefaultDto.php
Добавляем абстрактное Dto и расширяем его от Dto

```php
declare(strict_types=1);

namespace App\Defaults;

use Atlcom\Dto;
use App\Exceptions\ValidationException;
use Illuminate\Support\Facades\Validator;
use Exception;
use Throwable;

abstract class DefaultDto extends Dto
{
    final public function fillFromRequest(): void
    {
        $data = request()->toArray();
        $dataKeys = null;

        if (method_exists($this, 'rules')) {
            try {
                !$this->mappings() ?: $this->prepareMappings($data);
                !static::AUTO_MAPPINGS_ENABLED ?: $this->prepareStyles($data);
              
                $dataKeys = Validator::make($data, $this->rules(), $this->validatorMessages())
                    ->validate();
            } catch (Throwable $exception) {
                throw new Exception($exception->getMessage(), $exception->getCode());
            }
        }

        $this->fillFromArray($dataKeys ?? []);
    }
}
```

##### Файл app/Providers/DtoServiceProvider.php
Добавляем сервис провайдер для обработки Dto (dependency injection)

```php
namespace App\Providers;

use App\Defaults\DefaultDto;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Exception;
use Throwable;

class DtoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->resolving(DefaultDto::class, function (DefaultDto $dto, Application $app) {
            try {
                return !method_exists($dto, 'fillFromRequest') ?: $dto->fillFromRequest();
            } catch (Throwable $exception) {
                throw new Exception($exception->getMessage());
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

use App\Defaults\DefaultDto;

class ExampleDto extends DefaultDto
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
