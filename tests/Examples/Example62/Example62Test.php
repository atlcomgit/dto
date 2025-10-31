<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example62;

use Carbon\Carbon;
use Closure;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Примеры классов dto для теста
 */

enum CarEnum: string
{
    case Name1 = 'name1';
}

class CarDto extends \Atlcom\Dto
{
    public $none;
    public mixed $mixed;
    public object|array|int|string|bool|null $any;
    public null $null;
    public ?false $false;
    public ?true $true;
    public ?int $int;
    public ?string $string;
    public ?bool $bool;
    public ?array $array;
    public ?object $object;
    public ?\Atlcom\Dto $dto;

    public ?stdClass $stdObject;
    public ?Carbon $carbon;
    public ?Closure $closure;
    public ?CarEnum $enum;

    public mixed $carMapping;
}

/**
 * Тест 62
 * Преобразование dto в массив его свойств с типами
 */

final class Example62Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        $carDto = CarDto::create();

        $this->assertSame(['mixed'], $carDto->getPropertyTypes('none'));
        $this->assertSame(['mixed'], $carDto->getPropertyTypes('mixed'));
        $this->assertSame(['object', 'array', 'int', 'string', 'bool', 'null'], $carDto->getPropertyTypes('any'));
        $this->assertSame(['null'], $carDto->getPropertyTypes('null'));
        $this->assertSame(['null', 'false'], $carDto->getPropertyTypes('false'));
        $this->assertSame(['null', 'true'], $carDto->getPropertyTypes('true'));
        $this->assertSame(['null', 'int'], $carDto->getPropertyTypes('int'));
        $this->assertSame(['null', 'string'], $carDto->getPropertyTypes('string'));
        $this->assertSame(['null', 'bool'], $carDto->getPropertyTypes('bool'));
        $this->assertSame(['null', 'array'], $carDto->getPropertyTypes('array'));
        $this->assertSame(['null', 'object'], $carDto->getPropertyTypes('object'));
        $this->assertSame(['null', \Atlcom\Dto::class], $carDto->getPropertyTypes('dto'));
        $this->assertSame(['null', 'stdClass'], $carDto->getPropertyTypes('stdObject'));
        $this->assertSame(['null', Carbon::class], $carDto->getPropertyTypes('carbon'));
        $this->assertSame(['null', 'Closure'], $carDto->getPropertyTypes('closure'));
        $this->assertSame(['null', CarEnum::class], $carDto->getPropertyTypes('enum'));
        $this->assertSame(['mixed'], $carDto->getPropertyTypes('carMapping'));

        $carDto->consts('AUTO_DYNAMIC_PROPERTIES_ENABLED', true)
            ->custom1(123);
        $carDto->custom2 = '123';

        $this->assertSame(['int'], $carDto->getPropertyTypes('custom1'));
        $this->assertSame(['string'], $carDto->getPropertyTypes('custom2'));
    }
}
