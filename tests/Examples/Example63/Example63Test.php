<?php

declare(strict_types=1);

namespace Atlcom\Tests\Examples\Example63;

use Carbon\Carbon;
use Exception;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stdClass;

/**
 * Примеры классов dto для теста
 */

class CarDto extends \Atlcom\Dto
{
    public const AUTO_EMPTY_STRING_TO_NULL_ENABLED = true;

    public int $int;
    public ?int $intNull;
    public float $float;
    public ?float $floatNull;
    public string $string;
    public ?string $stringNull;
    public bool $bool;
    public ?bool $boolNull;
    public array $array;
    public ?array $arrayNull;
    public object $object;
    public ?object $objectNull;
    public Carbon $carbon;
    public ?Carbon $carbonNull;
    public Exception $exception;
    public ?Exception $exceptionNull;

    protected function casts(): array
    {
        return [...parent::getCasts(), ...parent::castDefault()];
    }
}

/**
 * Тест 63
 * Преобразование dto в массив его свойств с типами
 */

final class Example63Test extends TestCase
{
    #[Test]
    public function example1(): void
    {
        $carDto = CarDto::create(
            int: '',
            intNull: '',
            float: '',
            floatNull: '',
            string: '',
            stringNull: '',
            bool: '',
            boolNull: '',
            array: '',
            arrayNull: '',
            object: '',
            objectNull: '',
            carbon: '',
            carbonNull: '',
            exception: '',
            exceptionNull: '',
        );

        $this->assertSame(0, $carDto->int);
        $this->assertSame(null, $carDto->intNull);
        $this->assertSame(0.0, $carDto->float);
        $this->assertSame(null, $carDto->floatNull);
        $this->assertSame('', $carDto->string);
        $this->assertSame(null, $carDto->stringNull);
        $this->assertSame(false, $carDto->bool);
        $this->assertSame(null, $carDto->boolNull);
        $this->assertSame([], $carDto->array);
        $this->assertSame(null, $carDto->arrayNull);
        $this->assertInstanceOf(stdClass::class, $carDto->object);
        $this->assertSame(null, $carDto->objectNull);
        $this->assertInstanceOf(Carbon::class, $carDto->carbon);
        $this->assertSame(null, $carDto->carbonNull);
        $this->assertInstanceOf(Exception::class, $carDto->exception);
        $this->assertSame(null, $carDto->exceptionNull);
    }


    #[Test]
    public function example2(): void
    {
        $carDto = CarDto::create(
            int: null,
            intNull: null,
            float: null,
            floatNull: null,
            string: null,
            stringNull: null,
            bool: null,
            boolNull: null,
            array: null,
            arrayNull: null,
            object: null,
            objectNull: null,
            carbon: null,
            carbonNull: null,
            exception: null,
            exceptionNull: null,
        );

        $this->assertSame(0, $carDto->int);
        $this->assertSame(null, $carDto->intNull);
        $this->assertSame(0.0, $carDto->float);
        $this->assertSame(null, $carDto->floatNull);
        $this->assertSame('', $carDto->string);
        $this->assertSame(null, $carDto->stringNull);
        $this->assertSame(false, $carDto->bool);
        $this->assertSame(null, $carDto->boolNull);
        $this->assertSame([], $carDto->array);
        $this->assertSame(null, $carDto->arrayNull);
        $this->assertInstanceOf(stdClass::class, $carDto->object);
        $this->assertSame(null, $carDto->objectNull);
        $this->assertInstanceOf(Carbon::class, $carDto->carbon);
        $this->assertSame(null, $carDto->carbonNull);
        $this->assertInstanceOf(Exception::class, $carDto->exception);
        $this->assertSame(null, $carDto->exceptionNull);
    }


    #[Test]
    public function example3(): void
    {
        $carDto = (new CarDto())
            ->consts('AUTO_EMPTY_STRING_TO_NULL_ENABLED', false)
            ->fillFromArray([
                'int' => '',
                'intNull' => '',
                'float' => '',
                'floatNull' => '',
                'string' => '',
                'stringNull' => '',
                'bool' => '',
                'boolNull' => '',
                'array' => '',
                'arrayNull' => '',
                'object' => '',
                'objectNull' => '',
                'carbon' => '',
                'carbonNull' => '',
                'exception' => '',
                'exceptionNull' => '',
            ]);

        $this->assertSame(0, $carDto->int);
        $this->assertSame(0, $carDto->intNull);
        $this->assertSame(0.0, $carDto->float);
        $this->assertSame(0.0, $carDto->floatNull);
        $this->assertSame('', $carDto->string);
        $this->assertSame('', $carDto->stringNull);
        $this->assertSame(false, $carDto->bool);
        $this->assertSame(false, $carDto->boolNull);
        $this->assertSame([], $carDto->array);
        $this->assertSame([], $carDto->arrayNull);
        $this->assertInstanceOf(stdClass::class, $carDto->object);
        $this->assertInstanceOf(stdClass::class, $carDto->objectNull);
        $this->assertInstanceOf(Carbon::class, $carDto->carbon);
        $this->assertInstanceOf(Carbon::class, $carDto->carbonNull);
        $this->assertInstanceOf(Exception::class, $carDto->exception);
        $this->assertInstanceOf(Exception::class, $carDto->exceptionNull);
    }
}
