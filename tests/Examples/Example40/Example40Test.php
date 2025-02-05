<?php

namespace Atlcom\Tests\Examples\Example40;

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

class CarDto extends \Atlcom\Dto
{
    private mixed $private;
    protected mixed $protected;
    public mixed $public;

    public int $id1;
    public ?int $id2;
    public string $name1;
    public ?string $name2;
    public bool $active1;
    public ?bool $active2;
    public array $array1;
    public ?array $array2;
    public object $object1;
    public ?object $object2;
    public ModelDto $modelDto1;
    public ?ModelDto $modelDto2;
}

class ModelDto extends \Atlcom\Dto
{
    public stdClass $stdObject1;
    public ?stdClass $stdObject2;
    public Carbon $carbon1;
    public ?Carbon $carbon2;
    public Closure $closure1;
    public ?Closure $closure2;
    public CarEnum $carEnum1;
    public ?CarEnum $carEnum2;
}

/**
 * Тест 40
 * Преобразование dto в пустой массив
 */

final class Example40Test extends TestCase
{
    #[Test]
    public function example(): void
    {
        // только свойства родительской dto

        $carArrayBlank = CarDto::toArrayBlank(true);
        // var_dump($carArrayBlank);

        $this->assertArrayNotHasKey('private', $carArrayBlank);
        $this->assertArrayNotHasKey('protected', $carArrayBlank);
        $this->assertArrayHasKey('public', $carArrayBlank);
        $this->assertTrue($carArrayBlank['public'] === null);

        $this->assertArrayHasKey('id1', $carArrayBlank);
        $this->assertTrue($carArrayBlank['id1'] === null);
        $this->assertArrayHasKey('id2', $carArrayBlank);
        $this->assertTrue($carArrayBlank['id2'] === null);

        $this->assertArrayHasKey('name1', $carArrayBlank);
        $this->assertTrue($carArrayBlank['name1'] === null);
        $this->assertArrayHasKey('name2', $carArrayBlank);
        $this->assertTrue($carArrayBlank['name2'] === null);

        $this->assertArrayHasKey('active1', $carArrayBlank);
        $this->assertTrue($carArrayBlank['active1'] === null);
        $this->assertArrayHasKey('active2', $carArrayBlank);
        $this->assertTrue($carArrayBlank['active2'] === null);

        $this->assertArrayHasKey('array1', $carArrayBlank);
        $this->assertTrue($carArrayBlank['array1'] === null);
        $this->assertArrayHasKey('array2', $carArrayBlank);
        $this->assertTrue($carArrayBlank['array2'] === null);

        $this->assertArrayHasKey('object1', $carArrayBlank);
        $this->assertTrue($carArrayBlank['object1'] === null);
        $this->assertArrayHasKey('object2', $carArrayBlank);
        $this->assertTrue($carArrayBlank['object2'] === null);

        $this->assertArrayHasKey('modelDto1', $carArrayBlank);
        $this->assertTrue($carArrayBlank['modelDto1'] === null);
        $this->assertArrayHasKey('modelDto2', $carArrayBlank);
        $this->assertTrue($carArrayBlank['modelDto2'] === null);

        $carArrayBlank = CarDto::toArrayBlank(false);
        // var_dump($carArrayBlank);

        $this->assertArrayNotHasKey('private', $carArrayBlank);
        $this->assertArrayNotHasKey('protected', $carArrayBlank);
        $this->assertArrayHasKey('public', $carArrayBlank);
        $this->assertTrue($carArrayBlank['public'] === null);

        $this->assertArrayHasKey('id1', $carArrayBlank);
        $this->assertTrue($carArrayBlank['id1'] === 0);
        $this->assertArrayHasKey('id2', $carArrayBlank);
        $this->assertTrue($carArrayBlank['id2'] === null);

        $this->assertArrayHasKey('name1', $carArrayBlank);
        $this->assertTrue($carArrayBlank['name1'] === '');
        $this->assertArrayHasKey('name2', $carArrayBlank);
        $this->assertTrue($carArrayBlank['name2'] === null);

        $this->assertArrayHasKey('active1', $carArrayBlank);
        $this->assertTrue($carArrayBlank['active1'] === false);
        $this->assertArrayHasKey('active2', $carArrayBlank);
        $this->assertTrue($carArrayBlank['active2'] === null);

        $this->assertArrayHasKey('array1', $carArrayBlank);
        $this->assertTrue($carArrayBlank['array1'] === []);
        $this->assertArrayHasKey('array2', $carArrayBlank);
        $this->assertTrue($carArrayBlank['array2'] === []);

        $this->assertArrayHasKey('object1', $carArrayBlank);
        $this->assertTrue($carArrayBlank['object1']::class === ((object)[])::class);
        $this->assertArrayHasKey('object2', $carArrayBlank);
        $this->assertTrue($carArrayBlank['object2']::class === ((object)[])::class);

        $this->assertArrayHasKey('modelDto1', $carArrayBlank);
        $this->assertTrue($carArrayBlank['modelDto1'] === []);
        $this->assertArrayHasKey('modelDto2', $carArrayBlank);
        $this->assertTrue($carArrayBlank['modelDto2'] === []);

        // с рекурсией по всем вложенным свойствам

        $carArrayBlankRecursive = CarDto::toArrayBlankRecursive(true);
        // var_dump($carArrayBlankRecursive);

        $this->assertArrayNotHasKey('private', $carArrayBlankRecursive);
        $this->assertArrayNotHasKey('protected', $carArrayBlankRecursive);
        $this->assertArrayHasKey('public', $carArrayBlankRecursive);
        $this->assertTrue($carArrayBlankRecursive['public'] === null);

        $this->assertArrayHasKey('id1', $carArrayBlankRecursive);
        $this->assertTrue($carArrayBlankRecursive['id1'] === null);
        $this->assertArrayHasKey('id2', $carArrayBlankRecursive);
        $this->assertTrue($carArrayBlankRecursive['id2'] === null);

        $this->assertArrayHasKey('name1', $carArrayBlankRecursive);
        $this->assertTrue($carArrayBlankRecursive['name1'] === null);
        $this->assertArrayHasKey('name2', $carArrayBlankRecursive);
        $this->assertTrue($carArrayBlankRecursive['name2'] === null);

        $this->assertArrayHasKey('active1', $carArrayBlankRecursive);
        $this->assertTrue($carArrayBlankRecursive['active1'] === null);
        $this->assertArrayHasKey('active2', $carArrayBlankRecursive);
        $this->assertTrue($carArrayBlankRecursive['active2'] === null);

        $this->assertArrayHasKey('array1', $carArrayBlankRecursive);
        $this->assertTrue($carArrayBlankRecursive['array1'] === null);
        $this->assertArrayHasKey('array2', $carArrayBlankRecursive);
        $this->assertTrue($carArrayBlankRecursive['array2'] === null);

        $this->assertArrayHasKey('object1', $carArrayBlankRecursive);
        $this->assertTrue($carArrayBlankRecursive['object1'] === null);
        $this->assertArrayHasKey('object2', $carArrayBlankRecursive);
        $this->assertTrue($carArrayBlankRecursive['object2'] === null);

        $this->assertArrayHasKey('modelDto1', $carArrayBlankRecursive);
        $this->assertIsArray($carArrayBlankRecursive['modelDto1']);

        $this->assertArrayHasKey('stdObject1', $carArrayBlankRecursive['modelDto1']);
        $this->assertTrue($carArrayBlankRecursive['modelDto1']['stdObject1'] === null);
        $this->assertArrayHasKey('stdObject2', $carArrayBlankRecursive['modelDto1']);
        $this->assertTrue($carArrayBlankRecursive['modelDto1']['stdObject2'] === null);

        $this->assertArrayHasKey('carbon1', $carArrayBlankRecursive['modelDto1']);
        $this->assertTrue($carArrayBlankRecursive['modelDto1']['carbon1'] === null);
        $this->assertArrayHasKey('carbon2', $carArrayBlankRecursive['modelDto1']);
        $this->assertTrue($carArrayBlankRecursive['modelDto1']['carbon2'] === null);

        $this->assertArrayHasKey('closure1', $carArrayBlankRecursive['modelDto1']);
        $this->assertTrue($carArrayBlankRecursive['modelDto1']['closure1'] === null);
        $this->assertArrayHasKey('closure2', $carArrayBlankRecursive['modelDto1']);
        $this->assertTrue($carArrayBlankRecursive['modelDto1']['closure2'] === null);

        $this->assertArrayHasKey('carEnum1', $carArrayBlankRecursive['modelDto1']);
        $this->assertTrue($carArrayBlankRecursive['modelDto1']['carEnum1'] === null);
        $this->assertArrayHasKey('carEnum2', $carArrayBlankRecursive['modelDto1']);
        $this->assertTrue($carArrayBlankRecursive['modelDto1']['carEnum2'] === null);

        $this->assertArrayHasKey('modelDto2', $carArrayBlankRecursive);
        $this->assertIsArray($carArrayBlankRecursive['modelDto2']);

        $this->assertArrayHasKey('stdObject1', $carArrayBlankRecursive['modelDto2']);
        $this->assertTrue($carArrayBlankRecursive['modelDto2']['stdObject1'] === null);
        $this->assertArrayHasKey('stdObject2', $carArrayBlankRecursive['modelDto2']);
        $this->assertTrue($carArrayBlankRecursive['modelDto2']['stdObject2'] === null);

        $this->assertArrayHasKey('carbon1', $carArrayBlankRecursive['modelDto1']);
        $this->assertTrue($carArrayBlankRecursive['modelDto1']['carbon1'] === null);
        $this->assertArrayHasKey('carbon2', $carArrayBlankRecursive['modelDto1']);
        $this->assertTrue($carArrayBlankRecursive['modelDto1']['carbon2'] === null);

        $this->assertArrayHasKey('closure1', $carArrayBlankRecursive['modelDto1']);
        $this->assertTrue($carArrayBlankRecursive['modelDto1']['closure1'] === null);
        $this->assertArrayHasKey('closure2', $carArrayBlankRecursive['modelDto1']);
        $this->assertTrue($carArrayBlankRecursive['modelDto1']['closure2'] === null);

        $this->assertArrayHasKey('carEnum1', $carArrayBlankRecursive['modelDto1']);
        $this->assertTrue($carArrayBlankRecursive['modelDto1']['carEnum1'] === null);
        $this->assertArrayHasKey('carEnum2', $carArrayBlankRecursive['modelDto1']);
        $this->assertTrue($carArrayBlankRecursive['modelDto1']['carEnum2'] === null);

        $carArrayBlankRecursive = CarDto::toArrayBlankRecursive(false);
        // var_dump($carArrayBlankRecursive);

        $this->assertArrayNotHasKey('private', $carArrayBlankRecursive);
        $this->assertArrayNotHasKey('protected', $carArrayBlankRecursive);
        $this->assertArrayHasKey('public', $carArrayBlankRecursive);
        $this->assertTrue($carArrayBlankRecursive['public'] === null);

        $this->assertArrayHasKey('id1', $carArrayBlankRecursive);
        $this->assertTrue($carArrayBlankRecursive['id1'] === 0);
        $this->assertArrayHasKey('id2', $carArrayBlankRecursive);
        $this->assertTrue($carArrayBlankRecursive['id2'] === null);

        $this->assertArrayHasKey('name1', $carArrayBlankRecursive);
        $this->assertTrue($carArrayBlankRecursive['name1'] === '');
        $this->assertArrayHasKey('name2', $carArrayBlankRecursive);
        $this->assertTrue($carArrayBlankRecursive['name2'] === null);

        $this->assertArrayHasKey('active1', $carArrayBlankRecursive);
        $this->assertTrue($carArrayBlankRecursive['active1'] === false);
        $this->assertArrayHasKey('active2', $carArrayBlankRecursive);
        $this->assertTrue($carArrayBlankRecursive['active2'] === null);

        $this->assertArrayHasKey('array1', $carArrayBlankRecursive);
        $this->assertTrue($carArrayBlankRecursive['array1'] === []);
        $this->assertArrayHasKey('array2', $carArrayBlankRecursive);
        $this->assertTrue($carArrayBlankRecursive['array2'] === []);

        $this->assertArrayHasKey('object1', $carArrayBlankRecursive);
        $this->assertTrue($carArrayBlankRecursive['object1']::class === ((object)[])::class);
        $this->assertArrayHasKey('object2', $carArrayBlankRecursive);
        $this->assertTrue($carArrayBlankRecursive['object2']::class === ((object)[])::class);

        $this->assertArrayHasKey('modelDto1', $carArrayBlankRecursive);
        $this->assertIsArray($carArrayBlankRecursive['modelDto1']);

        $this->assertArrayHasKey('stdObject1', $carArrayBlankRecursive['modelDto1']);
        $this->assertTrue($carArrayBlankRecursive['modelDto1']['stdObject1']::class === ((object)[])::class);
        $this->assertArrayHasKey('stdObject2', $carArrayBlankRecursive['modelDto1']);
        $this->assertTrue($carArrayBlankRecursive['modelDto1']['stdObject2']::class === ((object)[])::class);

        $this->assertArrayHasKey('carbon1', $carArrayBlankRecursive['modelDto1']);
        $this->assertTrue($carArrayBlankRecursive['modelDto1']['carbon1'] === '');
        $this->assertArrayHasKey('carbon2', $carArrayBlankRecursive['modelDto1']);
        $this->assertTrue($carArrayBlankRecursive['modelDto1']['carbon2'] === '');

        $this->assertArrayHasKey('closure1', $carArrayBlankRecursive['modelDto1']);
        $this->assertTrue($carArrayBlankRecursive['modelDto1']['closure1'] === null);
        $this->assertArrayHasKey('closure2', $carArrayBlankRecursive['modelDto1']);
        $this->assertTrue($carArrayBlankRecursive['modelDto1']['closure2'] === null);

        $this->assertArrayHasKey('carEnum1', $carArrayBlankRecursive['modelDto1']);
        $this->assertTrue($carArrayBlankRecursive['modelDto1']['carEnum1'] === null);
        $this->assertArrayHasKey('carEnum2', $carArrayBlankRecursive['modelDto1']);
        $this->assertTrue($carArrayBlankRecursive['modelDto1']['carEnum2'] === null);

        $this->assertArrayHasKey('modelDto2', $carArrayBlankRecursive);
        $this->assertIsArray($carArrayBlankRecursive['modelDto2']);

        $this->assertArrayHasKey('stdObject1', $carArrayBlankRecursive['modelDto2']);
        $this->assertTrue($carArrayBlankRecursive['modelDto2']['stdObject1']::class === ((object)[])::class);
        $this->assertArrayHasKey('stdObject2', $carArrayBlankRecursive['modelDto2']);
        $this->assertTrue($carArrayBlankRecursive['modelDto2']['stdObject2']::class === ((object)[])::class);

        $this->assertArrayHasKey('carbon1', $carArrayBlankRecursive['modelDto2']);
        $this->assertTrue($carArrayBlankRecursive['modelDto2']['carbon1'] === '');
        $this->assertArrayHasKey('carbon2', $carArrayBlankRecursive['modelDto2']);
        $this->assertTrue($carArrayBlankRecursive['modelDto2']['carbon2'] === '');

        $this->assertArrayHasKey('closure1', $carArrayBlankRecursive['modelDto2']);
        $this->assertTrue($carArrayBlankRecursive['modelDto2']['closure1'] === null);
        $this->assertArrayHasKey('closure2', $carArrayBlankRecursive['modelDto2']);
        $this->assertTrue($carArrayBlankRecursive['modelDto2']['closure2'] === null);

        $this->assertArrayHasKey('carEnum1', $carArrayBlankRecursive['modelDto2']);
        $this->assertTrue($carArrayBlankRecursive['modelDto2']['carEnum1'] === null);
        $this->assertArrayHasKey('carEnum2', $carArrayBlankRecursive['modelDto2']);
        $this->assertTrue($carArrayBlankRecursive['modelDto2']['carEnum2'] === null);
    }
}