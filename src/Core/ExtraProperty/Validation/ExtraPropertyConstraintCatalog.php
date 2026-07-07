<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ExtraProperty\Validation;

use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\Composite;

/**
 * Machine-readable description of the constraints the BO "Validation" textarea accepts
 * (ExtraPropertyConstraintMapper's whitelist), meant to be serialized into the definition
 * page so a builder UI can offer each constraint with its configurable options.
 *
 * Shape (JSON-ready):
 *   name => {
 *     defaultOption: ?string,     // option filled by the positional shape, e.g. Choice([...])
 *     composite: bool,            // accepts nested constraints via the bracket shape, e.g. All[...]
 *     required: list<string>,     // options that must be provided
 *     options: { optionName: { type: 'string'|'int'|'number'|'bool'|'list'|'mixed' } }
 *   }
 *
 * Options are reflected from the constraint's public properties; a curated override fixes the
 * ordering and typing of the constraints the builder UI features prominently (reflection cannot
 * tell a UI-relevant option from an edge-case one, nor order them meaningfully). Message-template
 * options are left out — they are parseable but noise for a builder UI.
 */
class ExtraPropertyConstraintCatalog
{
    /**
     * Curated option lists (ordered, typed) taking precedence over reflection.
     */
    private const OPTION_OVERRIDES = [
        'Length' => ['min' => 'int', 'max' => 'int', 'charset' => 'string'],
        'Range' => ['min' => 'number', 'max' => 'number'],
        'Regex' => ['pattern' => 'string', 'match' => 'bool'],
        'Choice' => ['choices' => 'list', 'multiple' => 'bool', 'min' => 'int', 'max' => 'int'],
        'Count' => ['min' => 'int', 'max' => 'int', 'divisibleBy' => 'int'],
        'Type' => ['type' => 'string'],
        'TypedRegex' => ['type' => 'string'],
        'Email' => ['mode' => 'string'],
        'Url' => ['protocols' => 'list', 'requireTld' => 'bool'],
        'DateTime' => ['format' => 'string'],
    ];

    /**
     * @var array<string, array{defaultOption: ?string, composite: bool, required: list<string>, options: array<string, array{type: string}>}>|null
     */
    private ?array $catalog = null;

    /**
     * @return array<string, array{defaultOption: ?string, composite: bool, required: list<string>, options: array<string, array{type: string}>}>
     */
    public function getCatalog(): array
    {
        if (null !== $this->catalog) {
            return $this->catalog;
        }

        $catalog = [];
        foreach (ExtraPropertyConstraintMapper::getAllowedConstraints() as $name => $fqcn) {
            $catalog[$name] = $this->describe($name, $fqcn);
        }

        return $this->catalog = $catalog;
    }

    /**
     * @param class-string<Constraint> $fqcn
     *
     * @return array{defaultOption: ?string, composite: bool, required: list<string>, options: array<string, array{type: string}>}
     */
    private function describe(string $name, string $fqcn): array
    {
        $reflection = new ReflectionClass($fqcn);
        /** @var Constraint $prototype */
        $prototype = $reflection->newInstanceWithoutConstructor();

        $options = isset(self::OPTION_OVERRIDES[$name])
            ? array_map(static fn (string $type): array => ['type' => $type], self::OPTION_OVERRIDES[$name])
            : $this->reflectOptions($reflection);

        return [
            'defaultOption' => $prototype->getDefaultOption(),
            'composite' => is_subclass_of($fqcn, Composite::class),
            'required' => array_values(array_map(strval(...), $prototype->getRequiredOptions())),
            'options' => $options,
        ];
    }

    /**
     * @param ReflectionClass<Constraint> $reflection
     *
     * @return array<string, array{type: string}>
     */
    private function reflectOptions(ReflectionClass $reflection): array
    {
        $options = [];
        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            $optionName = $property->getName();
            if ($property->isStatic()
                || in_array($optionName, ['groups', 'payload'], true)
                || 1 === preg_match('/message$/i', $optionName)
            ) {
                continue;
            }
            $options[$optionName] = ['type' => $this->describeType($property)];
        }

        return $options;
    }

    private function describeType(ReflectionProperty $property): string
    {
        $type = $property->getType();

        if ($type instanceof ReflectionUnionType) {
            $names = array_map(
                static fn ($inner): string => $inner instanceof ReflectionNamedType ? $inner->getName() : 'mixed',
                $type->getTypes()
            );
            $scalars = array_diff($names, ['null']);
            if ([] !== $scalars && [] === array_diff($scalars, ['int', 'float'])) {
                return 'number';
            }

            return 'mixed';
        }

        if (!$type instanceof ReflectionNamedType) {
            return 'mixed';
        }

        return match ($type->getName()) {
            'int' => 'int',
            'float' => 'number',
            'bool' => 'bool',
            'string' => 'string',
            'array' => 'list',
            default => 'mixed',
        };
    }
}
