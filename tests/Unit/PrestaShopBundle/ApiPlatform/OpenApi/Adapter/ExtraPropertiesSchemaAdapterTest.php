<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\ApiPlatform\OpenApi\Adapter;

use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ArrayObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinition;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionCollection;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyType;
use PrestaShopBundle\ApiPlatform\OpenApi\Adapter\ExtraPropertiesSchemaAdapter;

class ExtraPropertiesSchemaAdapterTest extends TestCase
{
    /**
     * A definition flagged required (the same isRequired() flag that drives the BO form) must appear in its
     * module object's OpenAPI "required" list; a module with no required field carries no "required" key at all
     * (an empty required array is invalid in OpenAPI/JSON Schema).
     */
    public function testRequiredDefinitionsAreListedPerModuleObject(): void
    {
        $required = $this->definition('demoextrafield', 'theme_color', required: true);
        $optional = $this->definition('demoextrafield', 'video_link', scope: ExtraPropertyScope::LANG);
        $otherModule = $this->definition('othermodule', 'note');

        $schema = $this->buildSchema(new ExtraPropertyDefinitionCollection([$required, $optional, $otherModule]));
        $modules = $schema['properties'];

        $demoKey = $required->getNormalizedModuleKey();
        $otherKey = $otherModule->getNormalizedModuleKey();

        // Only the required field is listed, while both fields stay documented under "properties".
        self::assertSame(['theme_color'], $modules[$demoKey]['required']);
        self::assertArrayHasKey('theme_color', $modules[$demoKey]['properties']);
        self::assertArrayHasKey('video_link', $modules[$demoKey]['properties']);

        // A module without any required field must not emit a "required" key.
        self::assertArrayNotHasKey('required', $modules[$otherKey]);
    }

    /**
     * A declared defaultValue is part of the contract (a missing value row reads back as
     * that value): it must be emitted as the field's OpenAPI "default", with its scalar
     * type — and decoded for JSON, whose runtime read shape is the decoded structure.
     * A definition without a default must not emit the key.
     */
    public function testDeclaredDefaultsAreEmittedWithTheirType(): void
    {
        $definitions = new ExtraPropertyDefinitionCollection([
            $this->definition('demoextrafield', 'flag', type: ExtraPropertyType::BOOL, defaultValue: false),
            $this->definition('demoextrafield', 'score', type: ExtraPropertyType::INT, defaultValue: 0),
            $this->definition('demoextrafield', 'ratio', type: ExtraPropertyType::FLOAT, defaultValue: 1.5),
            $this->definition('demoextrafield', 'label', defaultValue: 'fallback'),
            $this->definition('demoextrafield', 'meta', type: ExtraPropertyType::JSON, defaultValue: '{"tier":"bronze"}'),
            $this->definition('demoextrafield', 'no_default'),
        ]);

        $properties = $this->buildSchema($definitions)['properties'][$definitions->first()->getNormalizedModuleKey()]['properties'];

        self::assertFalse($properties['flag']['default']);
        self::assertSame(0, $properties['score']['default']);
        self::assertSame(1.5, $properties['ratio']['default']);
        self::assertSame('fallback', $properties['label']['default']);
        // JSON defaults are stored as strings but the API returns decoded structures:
        // the documented default matches the runtime shape.
        self::assertSame(['tier' => 'bronze'], $properties['meta']['default']);
        self::assertArrayNotHasKey('default', $properties['no_default']);
    }

    /**
     * @return array<string, mixed>
     */
    private function buildSchema(ExtraPropertyDefinitionCollection $definitions): array
    {
        $adapter = new class($this->createMock(ExtraPropertyDefinitionRepositoryInterface::class), $this->createMock(ResourceMetadataCollectionFactoryInterface::class)) extends ExtraPropertiesSchemaAdapter {
            public function expose(ExtraPropertyDefinitionCollection $definitions): ArrayObject
            {
                return $this->buildExtraPropertiesSchema($definitions);
            }
        };

        return $adapter->expose($definitions)->getArrayCopy();
    }

    private function definition(
        string $moduleName,
        string $propertyName,
        ExtraPropertyScope $scope = ExtraPropertyScope::COMMON,
        bool $required = false,
        ExtraPropertyType $type = ExtraPropertyType::STRING,
        int|float|string|bool|null $defaultValue = null,
    ): ExtraPropertyDefinition {
        return new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: $propertyName,
            type: $type,
            scope: $scope,
            moduleName: $moduleName,
            required: $required,
            defaultValue: $defaultValue,
        );
    }
}
