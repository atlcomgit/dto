<?php

declare(strict_types=1);

namespace Atlcom\Traits;

use Atlcom\Exceptions\DtoException;
use Atlcom\Exceptions\DtoRequestException;
use Atlcom\Exceptions\DtoValidateException;
use BackedEnum;
use Carbon\Carbon;
use Closure;
use DateTime;
use Throwable;
use UnitEnum;

/**
 * Трейт для Laravel Framework
 * @mixin \Atlcom\Dto
 */
trait DtoLaravelTrait
{
    /**
     * @override
     * Возвращает массив правил валидации при использовании Dto вместо FormRequest в Laravel
     * @see ../../docs/LARAVEL.md
     *
     * @return array
     */
    // #[Override()]
    public function rules(): array
    {
        $array = [];
        $defaults = $this->defaults();

        foreach (static::getPropertiesWithAllTypes() as $key => $types) {
            $rules = [];

            foreach ($types as $type) {
                $rules[] = match ($type) {
                    'null' => 'nullable',
                    'int' => 'numeric',
                    'string' => 'string',
                    'bool' => 'boolean',
                    'array' => 'array',

                    default =>
                        match (true) {
                            class_exists($type) && (
                                is_subclass_of($type, UnitEnum::class)
                                || is_subclass_of($type, BackedEnum::class)
                            ) => 'in:' . implode(',', array_column($type::cases(), 'value')),
                            class_exists($type) && (
                                $type === DateTime::class
                                || $type === Carbon::class
                                || is_subclass_of($type, DateTime::class)
                            ) => 'date',

                            default => null,
                        },
                };
            }

            (in_array('null', $types) || !empty($defaults[$key])) ?: $rules[] = 'required';

            // Свое правило валидации
            // $rules[] = function (string $attribute, mixed $value, Closure $fail) {
            //     $value ?: $fail($this->validatorMessages()['custom'] ?? '');
            // };

            $array[$key] = array_filter(array_unique($rules));
        }

        return $array;
    }


    /**
     * @override
     * Заполнение Dto из запроса Request в Laravel
     *
     * @param object|array $data
     * @return static
     * @throws DtoException
     */
    // #[Override()]
    public function fillFromRequest(object|array $data): static
    {
        $data = static::convertDataToArray($data);
        $validator = 'Illuminate\Support\Facades\Validator';
        $rules = $this->rules();
        $dataNew = [];

        if ($rules && class_exists($validator)) {
            try {
                !$this->mappings() ?: $this->prepareMappings($data);
                !static::AUTO_MAPPINGS_ENABLED ?: $this->prepareStyles($data);

                $dataNew = $validator::make($data, $rules, $this->validatorMessages())
                    ->setAttributeNames($this->attributes())
                    ->validate();
            } catch (Throwable $exception) {
                $this->onException(
                    new DtoValidateException($exception->getMessage(), $exception->getCode() ?: 422, $exception)
                );
            }
        } else {
            $dataNew = $data;
        }

        return $this->fillFromArray($dataNew ?? []);
    }


    /**
     * @override
     * Названия отображения имен свойств при ошибке валидации в Laravel
     *
     * @return array
     */
    // #[Override()]
    protected function attributes(): array
    {
        return array_combine(static::getProperties(), static::getProperties());
    }


    /**
     * @override
     * Сообщения валидации в Laravel
     *
     * @return array
     */
    // #[Override()]
    protected function validatorMessages(): array
    {
        $class = $this->toBasename($this::class);

        return [
            'required' => "{$class}: [:attribute] является обязательным",
            'requiredIf' => "{$class}: [:attribute] является обязательным в данном случае",
            'integer' => "{$class}: [:attribute] должно быть типа INTEGER",
            'string' => "{$class}: [:attribute] должно быть типа STRING",
            'boolean' => "{$class}: [:attribute] должно быть типа BOOLEAN",
            'array' => "{$class}: [:attribute] должно быть типа ARRAY",
            'enum' => "{$class}: [:attribute] должно быть типа ENUM",
            'exists' => "{$class}: [:attribute] запись не найдена",
            'unique' => "{$class}: [:attribute] должно быть уникальным",
            'in' => "{$class}: [:attribute] должно быть из списка [:values]",
            'notIn' => "{$class}: [:attribute] не должно быть из списка [:values]",
            'min' => [
                'numeric' => "{$class}: [:attribute] не соответствует минимальному значению :min",
                'string' => "{$class}: [:attribute] не соответствует минимальной длине :min",
                'array' => "{$class}: [:attribute] не соответствует минимальным элементам :min",
                'file' => "{$class}: [:attribute] не соответствует минимальному размеру :min",
            ],
            'max' => [
                'numeric' => "{$class}: [:attribute] не соответствует максимальному значению :max",
                'string' => "{$class}: [:attribute] не соответствует максимальной длине :max",
                'array' => "{$class}: [:attribute] не соответствует максимальным элементам :max",
                'file' => "{$class}: [:attribute] не соответствует максимальному размеру :max",
            ],
            'same' => "{$class}: [:attribute] должно совпадать с :other",
            'between' => "{$class}: [:attribute] должно быть между :min - :max",
            'email' => "{$class}: [:attribute] невалидный адрес",
            'phone' => "{$class}: [:attribute] невалидный номер телефона",
            'custom' => "{$class}: [:attribute] ошибка",
        ];
    }
}
