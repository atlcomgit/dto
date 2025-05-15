<?php

declare(strict_types=1);

namespace Atlcom\Tests\Other;

use Atlcom\Dto;
use Atlcom\Exceptions\DtoException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class AiTestDto extends Dto
{
    public string $name = '';
    public int $age = 0;
    public ?string $email = null;

    protected function casts(): array
    {
        return [
            'age' => 'integer',
        ];
    }
}

final class AiDtoTest extends TestCase
{
    #[Test]
    public function testCreateFromArray(): void
    {
        $dto = AiTestDto::create(['name' => 'A', 'age' => 20]);
        $this->assertEquals('A', $dto->name);
        $this->assertEquals(20, $dto->age);
    }

    #[Test]
    public function testCreateFromJson(): void
    {
        $dto = AiTestDto::create('{"name":"B","age":30}');
        $this->assertEquals('B', $dto->name);
        $this->assertEquals(30, $dto->age);
    }

    #[Test]
    public function testToArray(): void
    {
        $dto = AiTestDto::create(['name' => 'C', 'age' => 40]);
        $arr = $dto->toArray();
        $this->assertIsArray($arr);
        $this->assertEquals('C', $arr['name']);
        $this->assertEquals(40, $arr['age']);
    }

    #[Test]
    public function testToJson(): void
    {
        $dto = AiTestDto::create(['name' => 'D', 'age' => 50]);
        $json = $dto->toJson();
        $this->assertJson($json);
        $this->assertStringContainsString('D', $json);
    }

    #[Test]
    public function testToArrayBlank(): void
    {
        $arr = AiTestDto::toArrayBlank();
        $this->assertArrayHasKey('name', $arr);
        $this->assertArrayHasKey('age', $arr);
    }

    #[Test]
    public function testToArrayBlankRecursive(): void
    {
        $arr = AiTestDto::toArrayBlankRecursive();
        $this->assertArrayHasKey('name', $arr);
        $this->assertArrayHasKey('age', $arr);
    }

    #[Test]
    public function testGetHash(): void
    {
        $dto = AiTestDto::create(['name' => 'E', 'age' => 60]);
        $hash = $dto->getHash();
        $this->assertIsString($hash);
        $this->assertNotEmpty($hash);
    }

    #[Test]
    public function testArrayAccess(): void
    {
        $dto = AiTestDto::create(['name' => 'F', 'age' => 70]);//->enableConst(INTERFACE_ARRAY_ACCESS_ENABLED);
        // $this->assertEquals('F', $dto['name']);
        // $dto['name'] = 'G';
        // $this->assertEquals('G', $dto->name);
        unset($dto['name']);
        $this->assertNull($dto->name);
    }

    #[Test]
    public function testCountable(): void
    {
        $dto = AiTestDto::create(['name' => 'H', 'age' => 80]);
        $this->assertEquals(3, count($dto)); // name, age, email
    }

    #[Test]
    public function testIteratorAggregate(): void
    {
        $dto = AiTestDto::create(['name' => 'I', 'age' => 90]);
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
        $dto = AiTestDto::create(['name' => 'J', 'age' => 100]);
        $json = json_encode($dto);
        $this->assertIsString($json);
        $this->assertStringContainsString('J', $json);
    }

    #[Test]
    public function testStringable(): void
    {
        $dto = AiTestDto::create(['name' => 'K', 'age' => 110]);
        $str = (string)$dto;
        $this->assertIsString($str);
        $this->assertStringContainsString('K', $str);
    }

    #[Test]
    public function testOnlyFilled(): void
    {
        $dto = AiTestDto::create(['name' => 'L']);
        $arr = $dto->onlyFilled()->toArray();
        $this->assertArrayHasKey('name', $arr);
        $this->assertArrayNotHasKey('age', $arr);
    }

    #[Test]
    public function testOnlyNotNull(): void
    {
        $dto = AiTestDto::create(['name' => 'M', 'email' => null]);
        $arr = $dto->onlyNotNull()->toArray();
        $this->assertArrayHasKey('name', $arr);
        $this->assertArrayNotHasKey('email', $arr);
    }

    #[Test]
    public function testOnlyKeys(): void
    {
        $dto = AiTestDto::create(['name' => 'N', 'age' => 120]);
        $arr = $dto->onlyKeys(['name'])->toArray();
        $this->assertArrayHasKey('name', $arr);
        $this->assertArrayNotHasKey('age', $arr);
    }

    #[Test]
    public function testExcludeKeys(): void
    {
        $dto = AiTestDto::create(['name' => 'O', 'age' => 130]);
        $arr = $dto->excludeKeys(['age'])->toArray();
        $this->assertArrayHasKey('name', $arr);
        $this->assertArrayNotHasKey('age', $arr);
    }

    #[Test]
    public function testIncludeStyles(): void
    {
        $dto = AiTestDto::create(['name' => 'P', 'age' => 140]);
        $arr = $dto->includeStyles()->toArray();
        $this->assertArrayHasKey('name', $arr);
        $this->assertArrayHasKey('age', $arr);
        $this->assertArrayHasKey('name', $arr);
    }

    #[Test]
    public function testIncludeArray(): void
    {
        $dto = AiTestDto::create(['name' => 'Q']);
        $arr = $dto->includeArray(['extra' => 1])->toArray();
        $this->assertArrayHasKey('extra', $arr);
    }

    #[Test]
    public function testMappingKeys(): void
    {
        $dto = AiTestDto::create(['name' => 'R']);
        $arr = $dto->mappingKeys(['name' => 'n'])->toArray();
        $this->assertArrayHasKey('n', $arr);
    }

    #[Test]
    public function testSerializeKeys(): void
    {
        $dto = AiTestDto::create(['name' => 'S']);
        $arr = $dto->serializeKeys(['name'])->toArray();
        $this->assertArrayHasKey('name', $arr);
    }

    #[Test]
    public function testWithProtectedKeys(): void
    {
        $dto = AiTestDto::create(['name' => 'T']);
        $arr = $dto->withProtectedKeys(['name'])->toArray();
        $this->assertArrayHasKey('name', $arr);
    }

    #[Test]
    public function testWithPrivateKeys(): void
    {
        $dto = AiTestDto::create(['name' => 'U']);
        $arr = $dto->withPrivateKeys(['name'])->toArray();
        $this->assertArrayHasKey('name', $arr);
    }

    #[Test]
    public function testWithCustomOptions(): void
    {
        $dto = AiTestDto::create(['name' => 'V']);
        $arr = $dto->withCustomOptions(['foo'])->toArray();
        $this->assertIsArray($arr);
    }

    #[Test]
    public function testWithoutOptions(): void
    {
        $dto = AiTestDto::create(['name' => 'W']);
        $arr = $dto->withoutOptions()->toArray();
        $this->assertIsArray($arr);
    }

    #[Test]
    public function testFor(): void
    {
        $dto = AiTestDto::create(['name' => 'X']);
        $result = $dto->for(AiTestDto::class);
        $this->assertInstanceOf(AiTestDto::class, $result);
    }

    #[Test]
    public function testSetAndGetCustomOption(): void
    {
        $dto = AiTestDto::create(['name' => 'Y']);
        $dto->setCustomOption('foo', 'bar');
        $this->assertEquals('bar', $dto->getCustomOption('foo'));
    }

    #[Test]
    public function testCustomOptions(): void
    {
        $dto = AiTestDto::create(['name' => 'Z']);
        $dto->customOptions(['firstOption' => 1]);
        $options = $dto->getOption('customOptions');
        $this->assertArrayHasKey('firstOption', $options);
    }

    #[Test]
    public function testTransformToDto(): void
    {
        $dto = AiTestDto::create(['name' => 'A1', 'age' => 20]);
        $dto2 = $dto->transformToDto(AiTestDto::class, ['name' => 'B1']);
        $this->assertInstanceOf(AiTestDto::class, $dto2);
        $this->assertEquals('B1', $dto2->name);
    }

    #[Test]
    public function testCollect(): void
    {
        $arr = [
            ['name' => 'A2', 'age' => 20],
            ['name' => 'B2', 'age' => 30],
        ];
        $dtos = AiTestDto::collect($arr);
        $this->assertIsArray($dtos);
        $this->assertCount(2, $dtos);
        $this->assertInstanceOf(AiTestDto::class, $dtos[0]);
    }

    #[Test]
    public function testIsEmpty(): void
    {
        $dto = AiTestDto::create();
        $this->assertTrue($dto->isEmpty());
    }

    #[Test]
    public function testExceptionOnInvalidType(): void
    {
        $this->expectException(DtoException::class);
        AiTestDto::create(['age' => 'not-an-integer']);
    }
}