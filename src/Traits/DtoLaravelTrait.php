<?php

declare(strict_types=1);

namespace Atlcom\Traits;

use Atlcom\Exceptions\DtoException;
use Atlcom\Exceptions\DtoValidateException;
use BackedEnum;
use Carbon\Carbon;
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
     * @see ../../tests/Other/RulesDtoTest.php
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

            if (in_array($key, ['id', 'uuid']) && function_exists('request')) {
                $request = request();

                $type = match (true) {
                    in_array('mixed', $types) => 'mixed',
                    in_array('int', $types) => 'integer',
                    in_array('integer', $types) => 'integer',
                    in_array('string', $types) => 'string',
                    in_array('any', $types) => 'mixed',
                    in_array('true', $types) => 'boolean',
                    in_array('false', $types) => 'boolean',

                    default => null,
                };

                !$type ?: match (true) {
                    $request->isMethod('put'), $request->isMethod('patch') => $rules = [...$rules, 'required', $type],
                    $request->isMethod('post') => $rules[] = 'prohibited',

                    default => $rules = [...$rules, 'nullable', $type],
                };
            }


            foreach ($types as $type) {
                $rules[] = match ($type) {
                    'null' => 'nullable',
                    'int' => 'numeric',
                    'string' => 'string',
                    'bool' => 'boolean',
                    'true' => 'boolean',
                    'false' => 'boolean',
                    'array' => 'array',
                    'mixed' => 'nullable',

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

            (in_array('null', $types) || in_array('mixed', $types) || !empty($defaults[$key])) ?: $rules[] = 'required';
            !(in_array('required', $rules) && in_array('nullable', $rules))
                ?: $rules = array_filter($rules, static fn ($v) => $v !== 'nullable');

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

        $this->onValidating($rules, $data);

        if ($rules && class_exists($validator)) {
            try {
                !$this->mappings() ?: $this->prepareMappings($data);
                !$this->consts('AUTO_MAPPINGS_ENABLED') ?: $this->prepareStyles($data);

                $dataNew = $validator::make($data, $rules, $this->validatorMessages())
                    ->setAttributeNames($this->attributes())
                    ->validate();
            } catch (Throwable $exception) {
                $this->onException(
                    new DtoValidateException($exception->getMessage(), $exception->getCode() ?: 422, $exception),
                );
            }
        } else {
            $dataNew = $data;
        }

        return $this->fillFromArray($dataNew ?? []);
    }


    /**
     * @override
     * Метод вызывается перед валидацией запроса
     *
     * @param array $rules
     * @param array $array
     * @return void
     */
    // #[Override()]
    protected function onValidating(array &$rules, array &$array): void
    {
        if (function_exists('request')) {
            $request = request();
            $routeId = $request->route()?->parameter('id');

            if (
                $request
                && $routeId
                && !$request->input('id')
                && ($request->isMethod('put') || $request->isMethod('patch'))
            ) {
                $array['id'] = (int)$routeId;
            }
        }
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
            'required_if' => "{$class}: [:attribute] является обязательным при :other = :value",
            'required_unless' => "{$class}: [:attribute] является обязательным, если :other не равно :values",
            'required_with' => "{$class}: [:attribute] является обязательным, когда присутствует :values",
            'required_with_all' => "{$class}: [:attribute] является обязательным, когда присутствуют :values",
            'required_without' => "{$class}: [:attribute] является обязательным, когда отсутствует :values",
            'required_without_all' => "{$class}: [:attribute] является обязательным, когда отсутствуют все :values",

            'integer' => "{$class}: [:attribute] должно быть целым числом",
            'numeric' => "{$class}: [:attribute] должно быть числом",
            'string' => "{$class}: [:attribute] должно быть строкой",
            'boolean' => "{$class}: [:attribute] должно быть булевым значением",
            'array' => "{$class}: [:attribute] должно быть массивом",
            'json' => "{$class}: [:attribute] должно быть корректной JSON-строкой",

            'min' => [
                'numeric' => "{$class}: [:attribute] должно быть не меньше :min",
                'string' => "{$class}: [:attribute] должно содержать минимум :min символов",
                'array' => "{$class}: [:attribute] должно содержать минимум :min элементов",
                'file' => "{$class}: [:attribute] должно быть не меньше :min килобайт",
            ],
            'max' => [
                'numeric' => "{$class}: [:attribute] должно быть не больше :max",
                'string' => "{$class}: [:attribute] должно содержать не более :max символов",
                'array' => "{$class}: [:attribute] должно содержать не более :max элементов",
                'file' => "{$class}: [:attribute] должно быть не больше :max килобайт",
            ],
            'between' => [
                'numeric' => "{$class}: [:attribute] должно быть между :min и :max",
                'string' => "{$class}: [:attribute] должно содержать от :min до :max символов",
                'array' => "{$class}: [:attribute] должно содержать от :min до :max элементов",
                'file' => "{$class}: [:attribute] должно быть от :min до :max килобайт",
            ],

            'in' => "{$class}: [:attribute] должно быть одним из [:values]",
            'not_in' => "{$class}: [:attribute] не должно быть из списка [:values]",
            'enum' => "{$class}: [:attribute] должно быть допустимым ENUM-значением",

            'exists' => "{$class}: [:attribute] запись не найдена",
            'unique' => "{$class}: [:attribute] должно быть уникальным",
            'same' => "{$class}: [:attribute] должно совпадать с :other",
            'different' => "{$class}: [:attribute] должно отличаться от :other",

            'email' => "{$class}: [:attribute] невалидный email",
            'url' => "{$class}: [:attribute] невалидный URL",
            'ip' => "{$class}: [:attribute] невалидный IP-адрес",
            'ipv4' => "{$class}: [:attribute] невалидный IPv4-адрес",
            'ipv6' => "{$class}: [:attribute] невалидный IPv6-адрес",
            'mac_address' => "{$class}: [:attribute] невалидный MAC-адрес",
            'uuid' => "{$class}: [:attribute] невалидный UUID",

            'date' => "{$class}: [:attribute] должно быть корректной датой",
            'date_format' => "{$class}: [:attribute] не соответствует формату :format",
            'before' => "{$class}: [:attribute] должно быть до :date",
            'before_or_equal' => "{$class}: [:attribute] должно быть до или равно :date",
            'after' => "{$class}: [:attribute] должно быть после :date",
            'after_or_equal' => "{$class}: [:attribute] должно быть после или равно :date",

            'confirmed' => "{$class}: [:attribute] не совпадает с подтверждением",
            'nullable' => "{$class}: [:attribute] может быть пустым",
            'regex' => "{$class}: [:attribute] имеет неверный формат",
            'digits' => "{$class}: [:attribute] должно состоять из :digits цифр",
            'digits_between' => "{$class}: [:attribute] должно содержать от :min до :max цифр",

            'file' => "{$class}: [:attribute] должно быть файлом",
            'mimes' => "{$class}: [:attribute] должно быть файлом одного из типов: :values",
            'mimetypes' => "{$class}: [:attribute] имеет неверный MIME-тип",
            'image' => "{$class}: [:attribute] должно быть изображением",

            'distinct' => "{$class}: [:attribute] содержит дублирующиеся значения",
            'present' => "{$class}: [:attribute] должно присутствовать в запросе",
            'accepted' => "{$class}: [:attribute] должно быть принято",
            'declined' => "{$class}: [:attribute] должно быть отклонено",

            'custom' => "{$class}: [:attribute] ошибка",
        ];
    }
}
