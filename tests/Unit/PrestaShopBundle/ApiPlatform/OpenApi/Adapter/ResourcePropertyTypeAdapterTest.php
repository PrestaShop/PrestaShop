<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\ApiPlatform\OpenApi\Adapter;

use ApiPlatform\Metadata\Post;
use ArrayObject;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\ApiPlatform\OpenApi\Adapter\ResourcePropertyTypeAdapter;
use Symfony\Component\PropertyInfo\Extractor\PhpStanExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;

class ResourcePropertyTypeAdapterTest extends TestCase
{
    public function testRequestBodyTypedFromTheResource(): void
    {
        $adapter = new ResourcePropertyTypeAdapter(
            new PropertyInfoExtractor([], [new PhpStanExtractor(), new ReflectionExtractor()]),
        );
        /** @var ArrayObject<string, mixed> $definition */
        $definition = new ArrayObject(['type' => 'object', 'properties' => [
            // The command declares plain arrays or lists of value objects: the generator documents strings
            'countryIds' => ['type' => 'array', 'items' => ['type' => 'string'], 'example' => [1, 3]],
            'codes' => ['type' => 'array'],
            'ratios' => ['type' => ['array', 'null'], 'items' => ['type' => 'string']],
            'flags' => ['type' => 'array', 'items' => ['type' => 'string']],
            'ranges' => ['type' => 'array', 'items' => ['$ref' => '#/components/schemas/Range']],
            'untyped' => ['type' => 'array', 'items' => ['type' => 'string']],
            'unknown' => ['type' => 'array', 'items' => ['type' => 'string']],
            // A legacy constructor parameter without type declaration
            'callPrefix' => [],
        ]]);

        $adapter->adapt(
            ResourceFixture::class,
            $definition,
            new Post(class: ResourceFixture::class, input: ['class' => CommandFixture::class])
        );

        $properties = $definition['properties'];
        $this->assertEquals(['type' => 'array', 'items' => ['type' => 'integer'], 'example' => [1, 3]], $properties['countryIds']);
        $this->assertEquals(['type' => 'array', 'items' => ['type' => 'string']], $properties['codes']);
        $this->assertEquals(['type' => ['array', 'null'], 'items' => ['type' => 'number']], $properties['ratios']);
        $this->assertEquals(['type' => 'array', 'items' => ['type' => 'boolean']], $properties['flags']);
        // A list of objects keeps its reference, a list without docblock and a property the resource lacks are left alone
        $this->assertEquals(['type' => 'array', 'items' => ['$ref' => '#/components/schemas/Range']], $properties['ranges']);
        $this->assertEquals(['type' => 'array', 'items' => ['type' => 'string']], $properties['untyped']);
        $this->assertEquals(['type' => 'array', 'items' => ['type' => 'string']], $properties['unknown']);
        $this->assertEquals(['type' => ['integer', 'null']], $properties['callPrefix']);
    }

    public function testResourceSchemaIsLeftAlone(): void
    {
        $adapter = new ResourcePropertyTypeAdapter(
            new PropertyInfoExtractor([], [new PhpStanExtractor(), new ReflectionExtractor()]),
        );
        /** @var ArrayObject<string, mixed> $definition */
        $definition = new ArrayObject(['type' => 'object', 'properties' => [
            'countryIds' => ['type' => 'array', 'items' => ['type' => 'string']],
        ]]);

        // No command input: the schema of the resource itself is typed by the resource extractors
        $adapter->adapt(ResourceFixture::class, $definition, new Post(class: ResourceFixture::class));

        $this->assertEquals(['type' => 'array', 'items' => ['type' => 'string']], $definition['properties']['countryIds']);
    }
}

class ResourceFixture
{
    /**
     * @var int[]
     */
    public array $countryIds;

    /**
     * @var string[]
     */
    public array $codes;

    /**
     * @var float[]|null
     */
    public ?array $ratios;

    /**
     * @var bool[]
     */
    public array $flags;

    /**
     * @var RangeFixture[]
     */
    public array $ranges;

    public array $untyped;

    public ?int $callPrefix;
}

class RangeFixture
{
}

class CommandFixture
{
}
