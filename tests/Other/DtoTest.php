<?php

namespace Atlcom\Tests\Other;

use Atlcom\Dto;
use Atlcom\Exceptions\DtoException;
use Faker\Factory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stdClass;

class SimpleDto extends Dto
{
    public string $name;
    public int $age;
    public ?string $email = null;

    protected function defaults(): array
    {
        return [
            'email' => 'default@example.com',
        ];
    }
}

class DtoTest extends TestCase
{
    private $faker;

    protected function setUp(): void
    {
        $this->faker = Factory::create();
    }


    #[Test]
    public function testCreateAndFill(): void
    {
        $name = $this->faker->name;
        $age = $this->faker->numberBetween(18, 99);
        $dto = SimpleDto::create(['name' => $name, 'age' => $age]);

        $this->assertInstanceOf(SimpleDto::class, $dto);
        $this->assertEquals($name, $dto->name);
        $this->assertEquals($age, $dto->age);
        $this->assertEquals('default@example.com', $dto->email);
    }


    #[Test]
    public function testFillFromObject(): void
    {
        $obj = new stdClass();
        $obj->name = $this->faker->name;
        $obj->age = $this->faker->numberBetween(18, 99);

        $dto = (new SimpleDto())->fillFromObject($obj);

        $this->assertEquals($obj->name, $dto->name);
        $this->assertEquals($obj->age, $dto->age);
    }


    #[Test]
    public function testFillFromJson(): void
    {
        $data = [
            'name' => $this->faker->name,
            'age' => $this->faker->numberBetween(18, 99),
        ];
        $json = json_encode($data);

        $dto = (new SimpleDto())->fillFromJson($json);

        $this->assertEquals($data['name'], $dto->name);
        $this->assertEquals($data['age'], $dto->age);
    }


    #[Test]
    public function testFillFromDto(): void
    {
        $dto1 = SimpleDto::create(['name' => $this->faker->name, 'age' => 42]);
        $dto2 = (new SimpleDto())->fillFromDto($dto1);

        $this->assertEquals($dto1->name, $dto2->name);
        $this->assertEquals($dto1->age, $dto2->age);
    }


    #[Test]
    public function testMerge(): void
    {
        $dto = SimpleDto::create(['name' => 'A', 'age' => 20]);
        $dto->merge(['name' => 'B', 'email' => 'b@example.com']);

        $this->assertEquals('B', $dto->name);
        $this->assertEquals(20, $dto->age);
        $this->assertEquals('b@example.com', $dto->email);
    }


    #[Test]
    public function testClear(): void
    {
        $dto = SimpleDto::create(['name' => 'A', 'age' => 20, 'email' => 'a@example.com']);
        $dto->clear();

        $this->assertSame('', $dto->name);
        $this->assertSame(0, $dto->age);
        $this->assertSame('default@example.com', $dto->email);
    }


    #[Test]
    public function testToArrayAndToJson(): void
    {
        $dto = SimpleDto::create(['name' => 'A', 'age' => 20]);
        $arr = $dto->toArray();
        $json = $dto->toJson();

        $this->assertIsArray($arr);
        $this->assertArrayHasKey('name', $arr);
        $this->assertArrayHasKey('age', $arr);
        $this->assertIsString($json);
        $this->assertStringContainsString('A', $json);
    }


    #[Test]
    public function testToArrayBlankAndRecursive(): void
    {
        $arr = SimpleDto::toArrayBlank();
        $arrRec = SimpleDto::toArrayBlankRecursive();

        $this->assertArrayHasKey('name', $arr);
        $this->assertArrayHasKey('age', $arr);
        $this->assertArrayHasKey('email', $arr);
        $this->assertArrayHasKey('name', $arrRec);
    }


    #[Test]
    public function testGetHash(): void
    {
        $dto = SimpleDto::create(['name' => 'A', 'age' => 20]);
        $hash = $dto->getHash();

        $this->assertIsString($hash);
        $this->assertNotEmpty($hash);
    }


    #[Test]
    public function testArrayAccess(): void
    {
        $this->expectException(DtoException::class);

        $dto = SimpleDto::create(['name' => 'A', 'age' => 20]);
        $this->assertTrue(isset($dto['name']));
        $this->assertEquals('A', $dto['name']);

        $dto['name'] = 'B';
        $this->assertEquals('B', $dto['name']);

        unset($dto['name']);
        $this->assertNull($dto->name);
    }


    #[Test]
    public function testCountable(): void
    {
        $dto = SimpleDto::create(['name' => 'A', 'age' => 20]);
        $this->assertEquals(3, count($dto)); // name, age, email
    }


    #[Test]
    public function testIteratorAggregate(): void
    {
        $dto = SimpleDto::create(['name' => 'A', 'age' => 20]);
        $data = [];
        foreach ($dto as $key => $value) {
            $data[$key] = $value;
        }
        $this->assertArrayHasKey('name', $data);
        $this->assertArrayHasKey('age', $data);
    }


    #[Test]
    public function testJsonSerializable(): void
    {
        $dto = SimpleDto::create(['name' => 'A', 'age' => 20]);
        $json = json_encode($dto);
        $this->assertIsString($json);
        $this->assertStringContainsString('A', $json);
    }


    #[Test]
    public function testStringable(): void
    {
        $dto = SimpleDto::create(['name' => 'A', 'age' => 20]);
        $str = (string)$dto;
        $this->assertIsString($str);
        $this->assertStringContainsString('A', $str);
    }


    #[Test]
    public function testSetAndGetCustomOption(): void
    {
        $dto = SimpleDto::create(['name' => 'A', 'age' => 20]);
        $dto->setCustomOption('foo', 'bar');
        $this->assertEquals('bar', $dto->getCustomOption('foo'));
    }


    #[Test]
    public function testOptionsMethods(): void
    {
        $dto = SimpleDto::create(['name' => 'A', 'age' => 20]);
        $dto->onlyFilled()
            ->onlyNotNull()
            ->onlyKeys('name')
            ->excludeKeys('age')
            ->includeStyles()
            ->includeArray(['extra' => 1])
            ->mappingKeys(['name' => 'n'])
            ->serializeKeys('name')
            ->withProtectedKeys('name')
            ->withPrivateKeys('age')
            ->withCustomOptions('foo')
            ->withoutOptions()
            ->for(SimpleDto::class);

        $this->assertInstanceOf(SimpleDto::class, $dto);
    }


    #[Test]
    public function testTransformToDto(): void
    {
        $dto = SimpleDto::create(['name' => 'A', 'age' => 20]);
        $dto2 = $dto->transformToDto(SimpleDto::class, ['name' => 'B']);
        $this->assertInstanceOf(SimpleDto::class, $dto2);
        $this->assertEquals('B', $dto2->name);
    }


    #[Test]
    public function testCollect(): void
    {
        $arr = [
            ['name' => 'A', 'age' => 20],
            ['name' => 'B', 'age' => 30],
        ];
        $dtos = SimpleDto::collect($arr);
        $this->assertIsArray($dtos);
        $this->assertCount(2, $dtos);
        $this->assertInstanceOf(SimpleDto::class, $dtos[0]);
    }


    #[Test]
    public function testErrorOnInvalidType(): void
    {
        $this->expectException(DtoException::class);
        SimpleDto::create(['name' => 'A', 'age' => 'not-an-int']);
    }


    #[Test]
    public function testErrorOnInvalidJson(): void
    {
        $this->expectException(\Throwable::class);
        (new SimpleDto())->fillFromJson('{invalid json}');
    }
}
