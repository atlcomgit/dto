<?php

namespace Atlcom\Tests\Examples\Example41;

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
    case Name2 = 'name2';
}

class CarDto1 extends \Atlcom\Dto
{
    private mixed $private;
    protected mixed $protected;
    public mixed $public;

    public $none;
    public mixed $mixed;
    public object|array|int|string|bool|null $any;
    public null $null;
    public ?int $id;
    public ?string $name;
    public ?bool $active;
    public ?array $array;
    public ?object $object;
    public ?\Atlcom\Dto $dto;
    public ?CarDto2 $carDto;

    public ?stdClass $stdObject;
    public ?Carbon $carbon;
    public ?Closure $closure;
    public ?CarEnum $carEnum;

    public mixed $carMapping;

    protected function mappings(): array
    {
        return [
            'carMapping' => 'car_mapping',
        ];
    }


    protected function casts(): array
    {
        return [
            'carMapping' => 'string',
        ];
    }
}

class CarDto2 extends \Atlcom\Dto
{
    public ?int $id;
}

/**
 * Тест 41
 * Преобразование dto в массив его свойств с типами
 */

final class Example41Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        // только свойства родительской dto

        $carProperties = CarDto1::getProperties();
        // var_dump($carProperties);

        $this->assertArrayNotHasKey('private', $carProperties);
        $this->assertArrayNotHasKey('protected', $carProperties);
        $this->assertContains('public', $carProperties);
        $this->assertContains('none', $carProperties);
        $this->assertContains('mixed', $carProperties);
        $this->assertContains('any', $carProperties);
        $this->assertContains('null', $carProperties);
        $this->assertContains('id', $carProperties);
        $this->assertContains('name', $carProperties);
        $this->assertContains('active', $carProperties);
        $this->assertContains('array', $carProperties);
        $this->assertContains('object', $carProperties);
        $this->assertContains('dto', $carProperties);
        $this->assertContains('carDto', $carProperties);
        $this->assertContains('stdObject', $carProperties);
        $this->assertContains('carbon', $carProperties);
        $this->assertContains('closure', $carProperties);
        $this->assertContains('carEnum', $carProperties);
        $this->assertContains('carMapping', $carProperties);

        $carPropertiesWithType = CarDto1::getPropertiesWithFirstType();
        // var_dump($carPropertiesWithType);

        $this->assertTrue($carPropertiesWithType['public'] === 'mixed');
        $this->assertTrue($carPropertiesWithType['none'] === 'mixed');
        $this->assertTrue($carPropertiesWithType['mixed'] === 'mixed');
        $this->assertTrue($carPropertiesWithType['any'] === 'object');
        $this->assertTrue($carPropertiesWithType['null'] === 'null');
        $this->assertTrue($carPropertiesWithType['id'] === 'int');
        $this->assertTrue($carPropertiesWithType['name'] === 'string');
        $this->assertTrue($carPropertiesWithType['active'] === 'bool');
        $this->assertTrue($carPropertiesWithType['array'] === 'array');
        $this->assertTrue($carPropertiesWithType['object'] === 'object');
        $this->assertTrue($carPropertiesWithType['dto'] === \Atlcom\Dto::class);
        $this->assertTrue($carPropertiesWithType['carDto'] === CarDto2::class);
        $this->assertTrue($carPropertiesWithType['stdObject'] === stdClass::class);
        $this->assertTrue($carPropertiesWithType['carbon'] === Carbon::class);
        $this->assertTrue($carPropertiesWithType['closure'] === Closure::class);
        $this->assertTrue($carPropertiesWithType['carEnum'] === CarEnum::class);
        $this->assertTrue($carPropertiesWithType['carMapping'] === 'mixed');

        $carPropertiesWithType = CarDto1::getPropertiesWithFirstType(
            useCasts: true,
            useMappings: false,
        );

        $this->assertTrue($carPropertiesWithType['carMapping'] === 'string');

        $carPropertiesWithAllTypes = CarDto1::getPropertiesWithAllTypes(
            useCasts: ['car_mapping' => 'integer'],
            useMappings: true,
        );
        // var_dump($carPropertiesWithAllTypes);

        $this->assertEquals($carPropertiesWithAllTypes['public'], ['mixed']);
        $this->assertEquals($carPropertiesWithAllTypes['none'], ['mixed']);
        $this->assertEquals($carPropertiesWithAllTypes['mixed'], ['mixed']);
        $this->assertEquals($carPropertiesWithAllTypes['any'], ['object', 'array', 'string', 'int', 'bool', 'null']);
        $this->assertEquals($carPropertiesWithAllTypes['null'], ['null']);
        $this->assertEquals($carPropertiesWithAllTypes['id'], ['null', 'int']);
        $this->assertEquals($carPropertiesWithAllTypes['name'], ['null', 'string']);
        $this->assertEquals($carPropertiesWithAllTypes['active'], ['null', 'bool']);
        $this->assertEquals($carPropertiesWithAllTypes['array'], ['null', 'array']);
        $this->assertEquals($carPropertiesWithAllTypes['object'], ['null', 'object']);
        $this->assertEquals($carPropertiesWithAllTypes['dto'], ['null', \Atlcom\Dto::class]);
        $this->assertEquals($carPropertiesWithAllTypes['carDto'], ['null', CarDto2::class]);
        $this->assertEquals($carPropertiesWithAllTypes['stdObject'], ['null', stdClass::class]);
        $this->assertEquals($carPropertiesWithAllTypes['carbon'], ['null', Carbon::class]);
        $this->assertEquals($carPropertiesWithAllTypes['closure'], ['null', Closure::class]);
        $this->assertEquals($carPropertiesWithAllTypes['carEnum'], ['null', CarEnum::class]);
        $this->assertEquals($carPropertiesWithAllTypes['car_mapping'], ['integer']);
    }
}
