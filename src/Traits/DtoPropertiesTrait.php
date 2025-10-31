<?php

declare(strict_types=1);

namespace Atlcom\Traits;

use PhpParser\Node;
use PhpParser\Node\Identifier;
use PhpParser\Node\IntersectionType;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\Use_;
use PhpParser\Node\UnionType;
use PhpParser\ParserFactory;
use ReflectionClass;
use ReflectionIntersectionType;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;
use Throwable;

/**
 * Трейт свойств
 * @mixin \Atlcom\Dto
 */
trait DtoPropertiesTrait
{
    /**
     * Возвращает массив свойств dto
     * @see ../../tests/Examples/Example41/Example41Test.php
     *
     * @return array
     */
    public static function getProperties(): array
    {
        static $cache = [];

        $cacheKey = static::class;

        if (isset($cache[$cacheKey])) {
            return $cache[$cacheKey];
        }

        $array = [];

        foreach (array_keys(get_class_vars(static::class)) as $key) {
            if (
                !str_contains($key, '*')
                && !str_contains($key, chr(0))
                && !(new ReflectionProperty(static::class, $key))->isPrivate()
                && !(new ReflectionProperty(static::class, $key))->isProtected()
            ) {
                $array[] = $key;
            }
        }

        return $cache[$cacheKey] = $array;
    }


    /**
     * Возвращает массив всех свойств dto с его первым типом
     * @see ../../tests/Examples/Example41/Example41Test.php
     *
     * @param bool|array|null $useCasts
     * @param bool|array|null $useMappings
     * @return array
     */
    public static function getPropertiesWithFirstType(
        bool|array|null $useCasts = [],
        bool|array|null $useMappings = false,
    ): array {
        static $cache = [];

        $cacheKey = static::class . ':' . json_encode([
            'casts' => $useCasts,
            'mappings' => $useMappings,
        ]);

        if (isset($cache[$cacheKey])) {
            return $cache[$cacheKey];
        }

        $result = array_map(
            static fn (array $v) => mb_strtolower($v[0]) === 'null' ? ($v[1] ?? $v[0]) : $v[0],
            static::getPropertiesWithAllTypes($useCasts, $useMappings),
        );

        return $cache[$cacheKey] = $result;
    }


    /**
     * Возвращает массив всех свойств dto со всеми его типами
     * @see ../../tests/Examples/Example41/Example41Test.php
     * 
     * @param bool|array|null $useCasts
     * @param bool|array|null $useMappings
     * @return array
     */
    public static function getPropertiesWithAllTypes(
        bool|array|null $useCasts = false,
        bool|array|null $useMappings = false,
    ): array {
        static $cache = [];

        $cacheKey = static::class . ':' . json_encode([
            'casts' => $useCasts,
            'mappings' => $useMappings,
        ]);

        if (isset($cache[$cacheKey])) {
            return $cache[$cacheKey];
        }

        $array = [];
        $dto = null;
        $casts = $useCasts
            ? [...($dto ??= new static())->casts(), ...(is_array($useCasts) ? $useCasts : [])]
            : [];
        $mappings = $useMappings
            ? [...($dto ??= new static())->mappings(), ...(is_array($useMappings) ? $useMappings : [])]
            : [];

        foreach (static::getProperties() as $key) {
            $mapKey = $mappings[$key] ?? $key;
            $array[$mapKey] = match (true) {
                isset($casts[$mapKey]) => (is_array($casts[$mapKey]) ? $casts[$mapKey] : [$casts[$mapKey]]),
                isset($casts[$key]) => (is_array($casts[$key]) ? $casts[$key] : [$casts[$key]]),

                default => static::resolvePropertyTypes($key),
            };
        }

        return $cache[$cacheKey] = $array;
    }


    /**
     * Возвращает массив типов свойства dto
     * @see ../../tests/Examples/Example62/Example62Test.php
     *
     * @param string $name
     * @return array
     */
    public function getPropertyTypes(string $name): array
    {
        $name = $this->resolvePropertyName($name);

        return match (true) {
            property_exists($this, $name) => static::resolvePropertyTypes($name),

            $this->consts('AUTO_DYNAMIC_PROPERTIES_ENABLED') === true
            => match (
                $type = gettype($this->getCustomOption($name))
                ) {
                    'null', 'NULL' => ['null'],
                    'integer', 'int' => ['int'],
                    'double', 'float' => ['float'],
                    'boolean', 'bool' => ['bool'],
                    'string' => ['string'],
                    'array' => ['array'],
                    'object' => ['object'],
                    'mixed' => ['mixed'],

                    default => [$type],
                },


            default => [],
        };
    }


    /**
     * Возвращает имя свойства после резолвинга
     *
     * @param string $name
     * @return string
     */
    public function resolvePropertyName(string $name): string
    {
        static $cache = [];

        $cacheKey = static::class . ':' . $name;

        if (isset($cache[$cacheKey])) {
            return $cache[$cacheKey];
        }

        $resolved = (property_exists($this, $name) ? $name : null)
            ?? ($this->getFlipArray($this->mappings())[$name] ?? null)
            ?? (
                $this->consts('AUTO_MAPPINGS_ENABLED')
                ? ((property_exists($this, $name = $this->toCamelCase($name)) ? $name : null)
                    ?? (property_exists($this, $name = $this->toSnakeCase($name)) ? $name : null)
                )
                : null
            )
            ?? $name;

        return $cache[$cacheKey] = $resolved;
    }


    /**
     * Возвращает массив типов свойства через Reflection API с сохранением порядка из AST
     *
     * @param string $name
     * @return array
     */
    public static function resolvePropertyTypes(string $name): array
    {
        static $cache = [];

        $cacheKey = static::class;

        // Если весь класс уже распарсен - возвращаем из кеша
        if (isset($cache[$cacheKey])) {
            return $cache[$cacheKey][$name] ?? ['mixed'];
        }

        // Парсим все свойства класса за один проход
        $cache[$cacheKey] = static::parseAllClassProperties();

        return $cache[$cacheKey][$name] ?? ['mixed'];
    }


    /**
     * Парсит файл класса и возвращает типы всех свойств с сохранением порядка
     *
     * @return array<string, array>
     */
    private static function parseAllClassProperties(): array
    {
        $result = [];
        $ref = new ReflectionClass(static::class);
        $file = $ref->getFileName();

        // Для анонимных классов или eval-кода используем только Reflection API
        if (!$file || !file_exists($file)) {
            return static::parseAllPropertiesViaReflection();
        }

        try {
            $code = file_get_contents($file);
            $parser = (new ParserFactory())->createForNewestSupportedVersion();
            $ast = $parser->parse($code);

            if (!$ast) {
                return static::parseAllPropertiesViaReflection();
            }

            // Сбор use-выражений и namespace
            $useStatements = [];
            $currentNamespace = '';

            foreach ($ast as $node) {
                if ($node instanceof Namespace_) {
                    $currentNamespace = $node->name ? $node->name->toString() : '';

                    foreach ($node->stmts as $stmt) {
                        if ($stmt instanceof Use_) {
                            foreach ($stmt->uses as $use) {
                                $alias = $use->alias ? $use->alias->name : $use->name->getLast();
                                $useStatements[$alias] = $use->name->toString();
                            }
                        }
                    }
                }
            }

            // Ищем все свойства класса
            $findAllProperties = static function (array $stmts, array $uses, string $namespace) use (&$findAllProperties): array {
                $properties = [];

                foreach ($stmts as $stmt) {
                    // Обработка namespace
                    if ($stmt instanceof Namespace_) {
                        $props = $findAllProperties($stmt->stmts, $uses, $namespace);
                        $properties = array_merge($properties, $props);
                        continue;
                    }

                    // Обработка класса
                    if ($stmt instanceof Class_) {
                        foreach ($stmt->stmts as $classStmt) {
                            if (!($classStmt instanceof Property)) {
                                continue;
                            }

                            foreach ($classStmt->props as $prop) {
                                $propertyName = $prop->name->name;
                                $type = $classStmt->type;

                                if (!$type) {
                                    $properties[$propertyName] = ['mixed'];
                                    continue;
                                }

                                $types = [];

                                // UnionType (int|string|null)
                                if ($type instanceof UnionType) {
                                    foreach ($type->types as $t) {
                                        $types[] = static::resolveTypeNode($t, $uses, $namespace);
                                    }
                                } // IntersectionType (A&B)
                                elseif ($type instanceof IntersectionType) {
                                    foreach ($type->types as $t) {
                                        $types[] = static::resolveTypeNode($t, $uses, $namespace);
                                    }
                                } // NullableType (?int)
                                elseif ($type instanceof NullableType) {
                                    $types = [
                                        'null',
                                        static::resolveTypeNode($type->type, $uses, $namespace),
                                    ];
                                } // Простой тип
                                else {
                                    $types = [static::resolveTypeNode($type, $uses, $namespace)];
                                }

                                $properties[$propertyName] = $types;
                            }
                        }
                    }
                }

                return $properties;
            };

            $result = $findAllProperties($ast, $useStatements, $currentNamespace);

            // Если парсер не нашел свойства, используем Reflection
            if (empty($result)) {
                return static::parseAllPropertiesViaReflection();
            }

            return $result;

        } catch (Throwable $e) {
            // В случае ошибки парсинга используем Reflection
            return static::parseAllPropertiesViaReflection();
        }
    }


    /**
     * Извлекает типы всех свойств через Reflection API
     *
     * @return array<string, array>
     */
    private static function parseAllPropertiesViaReflection(): array
    {
        $result = [];
        $properties = static::getProperties();

        foreach ($properties as $name) {
            if (!property_exists(static::class, $name)) {
                continue;
            }

            $property = new ReflectionProperty(static::class, $name);
            $type = $property->getType();

            if (!$type) {
                $result[$name] = ['mixed'];
                continue;
            }

            $types = [];

            // UnionType
            if ($type instanceof ReflectionUnionType) {
                foreach ($type->getTypes() as $unionType) {
                    if ($unionType instanceof ReflectionNamedType) {
                        $types[] = $unionType->getName();
                    }
                }
            } // IntersectionType
            elseif ($type instanceof ReflectionIntersectionType) {
                foreach ($type->getTypes() as $intersectionType) {
                    if ($intersectionType instanceof ReflectionNamedType) {
                        $types[] = $intersectionType->getName();
                    }
                }
            } // NamedType
            elseif ($type instanceof ReflectionNamedType) {
                if ($type->allowsNull() && $type->getName() !== 'mixed') {
                    $types[] = 'null';
                }
                $types[] = $type->getName();
            }

            $result[$name] = $types ?: ['mixed'];
        }

        return $result;
    }


    /**
     * Преобразует узел типа AST в строку с резолвингом полного имени класса
     *
     * @param Node $node
     * @param array $useStatements
     * @param string $currentNamespace
     * @return string
     */
    private static function resolveTypeNode(
        Node $node,
        array $useStatements = [],
        string $currentNamespace = '',
    ): string {
        // Identifier (int, string, bool и т.д.)
        if ($node instanceof Identifier) {
            return $node->name;
        }

        // Name (имя класса)
        if ($node instanceof Name) {
            $name = $node->toString();

            // Если это полное имя (начинается с \)
            if ($node->isFullyQualified()) {
                return ltrim($name, '\\');
            }

            // Если это относительное имя с namespace
            if ($node->isQualified()) {
                $parts = $node->getParts();
                $firstPart = $parts[0];

                // Проверяем use-выражения
                if (isset($useStatements[$firstPart])) {
                    $parts[0] = $useStatements[$firstPart];

                    return implode('\\', $parts);
                }

                // Добавляем текущий namespace
                return $currentNamespace ? $currentNamespace . '\\' . $name : $name;
            }

            // Короткое имя - проверяем use-выражения
            if (isset($useStatements[$name])) {
                return $useStatements[$name];
            }

            // Добавляем текущий namespace
            return $currentNamespace ? $currentNamespace . '\\' . $name : $name;
        }

        return 'mixed';
    }


    /**
     * Проверяет dto на заполнение хотя бы одного свойства
     * @see ../../tests/Examples/Example39/Example39Test.php
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        foreach (static::getPropertiesWithAllTypes() as $key => $types) {
            $value = $this->$key ?? null;
            $isEmpty = match (true) {
                is_callable($value) => false,
                $value instanceof self => $value->isEmpty(),
                is_object($value) && method_exists($value, 'isEmpty') => $value->isEmpty(),
                is_object($value) && method_exists($value, 'count') => $value->count() === 0,
                is_object($value) => empty((array)$value),
                is_array($value) => empty($value),
                is_scalar($value) =>
                    match (true) {
                        in_array('null', $types) => $value === null,

                        default => empty($value),
                    },

                default => true,
            };

            if (!$isEmpty) {
                return false;
            }
        }

        return true;
    }


    /**
     * Удаляет свойства из dto
     * @see ../../tests/Examples/Example59/Example59Test.php
     *
     * @param string|array ...$data
     * @return static
     */
    public function removeProperties(string|array ...$data): static
    {
        $removeKeys = [];

        foreach ($data as $key) {
            $removeKeys = [
                ...$removeKeys,
                ...(is_string($key)
                    ? [$key]
                    : (is_string(key($key)) ? [key($key)] : $key)
                ),
            ];
        }

        $customOptions = $this->options()['customOptions'] ?? [];

        foreach ($removeKeys as $key) {
            if (property_exists($this, $key)) {
                unset($this->$key);
            }

            if (isset($customOptions[$key])) {
                unset($customOptions[$key]);
            }
        }

        $this->options(customOptions: $customOptions);

        return $this;
    }


    /**
     * Скрывает свойства из dto
     * @see ../../tests/Examples/Example61/Example61Test.php
     *
     * @param string|array ...$data
     * @return static
     */
    public function hideProperties(string|array ...$data): static
    {
        $hideKeys = [];

        foreach ($data as $key) {
            $hideKeys = [
                ...$hideKeys,
                ...(is_string($key)
                    ? [$key]
                    : (is_string(key($key)) ? [key($key)] : $key)
                ),
            ];
        }

        $customOptions = $this->options()['customOptions'] ?? [];

        foreach ($hideKeys as $key) {
            if (property_exists($this, $key)) {
                $customOptions[$key] = $this->{$key};
                unset($this->$key);
            }
        }

        $this->options(customOptions: $customOptions);

        return $this;
    }
}
