<?php

namespace Atlcom\Tests\Other;

use Atlcom\Dto;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Carbon\Carbon;

enum TestEnum: string
{
    case A = 'A';
    case B = 'B';
}

/**
 * Тесты метода rules() для различных типов свойств
 */
class RulesDtoTest extends TestCase
{
    /**
     * Временный Dto для базовых типов
     */
    protected static function getBasicTypesDto(): Dto
    {

        return new class extends Dto {
            public int $intField;
            public string $stringField;
            public bool $boolField;
            public array $arrayField;
        };
    }


    /**
     * Временный Dto для свойства с типом mixed
     */
    protected static function getMixedTypeDto(): Dto
    {

        return new class extends Dto {
            public mixed $mixedField;
        };
    }


    /**
     * Временный Dto для nullable и required
     */
    protected static function getNullableDto(): Dto
    {

        return new class extends Dto {
            public ?string $nullableField;
            public int $requiredField;
        };
    }


    /**
     * Временный Dto для enum и даты
     */
    protected static function getEnumDateDto(): Dto
    {

        return new class extends Dto {
            public TestEnum $enumField;
            public Carbon $dateField;
        };
    }


    /**
     * Временный Dto для id/uuid
     */
    protected static function getIdUuidDto(): Dto
    {

        return new class extends Dto {
            public int $id;
            public string $uuid;
        };
    }


    /**
     * Тест генерации правил для базовых типов
     *
     * @see \Atlcom\Traits\DtoLaravelTrait::rules()
     *
     * @return void
     */
    #[Test]
    public function testRulesForBasicTypes(): void
    {
        $dto = self::getBasicTypesDto();
        $rules = $dto->rules();
        $this->assertArrayHasKey('intField', $rules);
        $this->assertContains('numeric', $rules['intField']);
        $this->assertArrayHasKey('stringField', $rules);
        $this->assertContains('string', $rules['stringField']);
        $this->assertArrayHasKey('boolField', $rules);
        $this->assertContains('boolean', $rules['boolField']);
        $this->assertArrayHasKey('arrayField', $rules);
        $this->assertContains('array', $rules['arrayField']);
    }


    /**
     * Тест правила для свойства с типом mixed
     *
     * @see \Atlcom\Traits\DtoLaravelTrait::rules()
     *
     * @return void
     */
    #[Test]
    public function testRulesForMixedType(): void
    {
        $dto = self::getMixedTypeDto();
        $rules = $dto->rules();
        $this->assertArrayHasKey('mixedField', $rules);
        $this->assertContains('nullable', $rules['mixedField']);
    }


    /**
     * Тест обязательных и nullable полей
     *
     * @see \Atlcom\Traits\DtoLaravelTrait::rules()
     *
     * @return void
     */
    #[Test]
    public function testRequiredAndNullableFields(): void
    {
        $dto = self::getNullableDto();
        $rules = $dto->rules();
        $this->assertContains('nullable', $rules['nullableField']);
        $this->assertContains('required', $rules['requiredField']);
    }


    /**
     * Тест правил для enum и даты
     *
     * @see \Atlcom\Traits\DtoLaravelTrait::rules()
     *
     * @return void
     */
    #[Test]
    public function testRulesForEnumAndDate(): void
    {
        $dto = self::getEnumDateDto();
        $rules = $dto->rules();
        $this->assertStringStartsWith('in:', $rules['enumField'][0]);
        $this->assertContains('date', $rules['dateField']);
    }


    /**
     * Тест правил для id/uuid (без запроса)
     *
     * @see \Atlcom\Traits\DtoLaravelTrait::rules()
     *
     * @return void
     */
    #[Test]
    public function testRulesForIdAndUuid(): void
    {
        $dto = self::getIdUuidDto();
        $rules = $dto->rules();
        $this->assertArrayHasKey('id', $rules);
        $this->assertArrayHasKey('uuid', $rules);
    }
}
