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
    ): ExtraPropertyDefinition {
        return new ExtraPropertyDefinition(
            entityName: 'product',
            propertyName: $propertyName,
            type: ExtraPropertyType::STRING,
            scope: $scope,
            moduleName: $moduleName,
            required: $required,
        );
    }
}
