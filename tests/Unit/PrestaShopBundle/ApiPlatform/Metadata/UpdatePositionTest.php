<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\Metadata;

use ApiPlatform\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\ApiPlatform\Processor\UpdatePositionProcessor;

class UpdatePositionTest extends TestCase
{
    public function testDefaultConstructor(): void
    {
        // Without any parameters
        $operation = new UpdatePosition();
        $this->assertEquals(UpdatePositionProcessor::class, $operation->getProcessor());
        $this->assertNull($operation->getProvider());
        $this->assertEquals(UpdatePosition::METHOD_PATCH, $operation->getMethod());
        $this->assertFalse($operation->canRead());
        $this->assertFalse($operation->getOutput());
        $this->assertEquals([], $operation->getExtraProperties());
        $this->assertEquals(['json'], $operation->getFormats());

        // With positioned parameters
        $operation = new UpdatePosition('/uri');
        $this->assertEquals(UpdatePositionProcessor::class, $operation->getProcessor());
        $this->assertNull($operation->getProvider());
        $this->assertEquals(UpdatePosition::METHOD_PATCH, $operation->getMethod());
        $this->assertFalse($operation->canRead());
        $this->assertFalse($operation->getOutput());
        $this->assertEquals([], $operation->getExtraProperties());
        $this->assertEquals(['json'], $operation->getFormats());
        $this->assertEquals('/uri', $operation->getUriTemplate());

        // With named parameters
        $operation = new UpdatePosition(
            formats: ['json', 'html'],
            extraProperties: ['scopes' => ['test']],
            parentIdField: 'attributeGroupId',
        );
        $this->assertEquals(UpdatePositionProcessor::class, $operation->getProcessor());
        $this->assertNull($operation->getProvider());
        $this->assertEquals(UpdatePosition::METHOD_PATCH, $operation->getMethod());
        $this->assertFalse($operation->canRead());
        $this->assertFalse($operation->getOutput());
        $this->assertEquals(['scopes' => ['test'], 'parentIdField' => 'attributeGroupId'], $operation->getExtraProperties());
        $this->assertEquals(['json', 'html'], $operation->getFormats());
        $this->assertEquals('attributeGroupId', $operation->getParentIdField());
    }

    public function testParentIdField(): void
    {
        // ParentIdField parameters in constructor
        $operation = new UpdatePosition(
            parentIdField: 'attributeGroupId',
        );
        $this->assertEquals(['parentIdField' => 'attributeGroupId'], $operation->getExtraProperties());
        $this->assertEquals('attributeGroupId', $operation->getParentIdField());

        // Extra properties parameters in constructor
        $operation = new UpdatePosition(
            extraProperties: ['parentIdField' => 'attributeGroupId']
        );
        $this->assertEquals(['parentIdField' => 'attributeGroupId'], $operation->getExtraProperties());
        $this->assertEquals('attributeGroupId', $operation->getParentIdField());

        // Extra properties AND parentIdField parameters in constructor, both values are equal no problem
        $operation = new UpdatePosition(
            extraProperties: ['parentIdField' => 'attributeGroupId'],
            parentIdField: 'attributeGroupId',
        );
        $this->assertEquals(['parentIdField' => 'attributeGroupId'], $operation->getExtraProperties());
        $this->assertEquals('attributeGroupId', $operation->getParentIdField());

        // Use with method, returned object is a clone All values are replaced
        $operation2 = $operation->withParentIdField('attributeId');
        $this->assertNotEquals($operation2, $operation);
        $this->assertEquals(['parentIdField' => 'attributeId'], $operation2->getExtraProperties());
        $this->assertEquals('attributeId', $operation2->getParentIdField());
        // Initial operation not modified of course
        $this->assertEquals(['parentIdField' => 'attributeGroupId'], $operation->getExtraProperties());
        $this->assertEquals('attributeGroupId', $operation->getParentIdField());

        // When both values are specified, but they are different trigger an exception
        $caughtException = null;
        try {
            new UpdatePosition(
                extraProperties: ['parentIdField' => 'attributeGroupId'],
                parentIdField: 'attributeId',
            );
        } catch (InvalidArgumentException $e) {
            $caughtException = $e;
        }

        $this->assertNotNull($caughtException);
        $this->assertInstanceOf(InvalidArgumentException::class, $caughtException);
        $this->assertEquals('Specifying an extra property parentIdField and a parentIdField argument that are different is invalid', $caughtException->getMessage());
    }

    public function testScopes(): void
    {
        // Scopes parameters in constructor
        $operation = new UpdatePosition(
            scopes: ['test', 'test2']
        );
        $this->assertEquals(['scopes' => ['test', 'test2']], $operation->getExtraProperties());
        $this->assertEquals(['test', 'test2'], $operation->getScopes());

        // Extra properties parameters in constructor
        $operation = new UpdatePosition(
            extraProperties: ['scopes' => ['test']]
        );
        $this->assertEquals(['scopes' => ['test']], $operation->getExtraProperties());
        $this->assertEquals(['test'], $operation->getScopes());

        // Extra properties AND scopes parameters in constructor, both values get merged but remain unique
        $operation = new UpdatePosition(
            extraProperties: ['scopes' => ['test', 'test1']],
            scopes: ['test', 'test2'],
        );
        $this->assertEquals(['scopes' => ['test', 'test1', 'test2']], $operation->getExtraProperties());
        $this->assertEquals(['test', 'test1', 'test2'], $operation->getScopes());

        // Use with method, returned object is a clone All values are replaced
        $operation2 = $operation->withScopes(['test3']);
        $this->assertNotEquals($operation2, $operation);
        $this->assertEquals(['scopes' => ['test3']], $operation2->getExtraProperties());
        $this->assertEquals(['test3'], $operation2->getScopes());
        // Initial operation not modified of course
        $this->assertEquals(['scopes' => ['test', 'test1', 'test2']], $operation->getExtraProperties());
        $this->assertEquals(['test', 'test1', 'test2'], $operation->getScopes());
    }

    public function testApiResourceMapping(): void
    {
        // Api resource mapping parameters in constructor
        $resourceMapping = ['[id]' => '[queryId]'];
        $operation = new UpdatePosition(
            ApiResourceMapping: $resourceMapping,
        );

        $this->assertEquals(['ApiResourceMapping' => $resourceMapping], $operation->getExtraProperties());
        $this->assertEquals($resourceMapping, $operation->getApiResourceMapping());

        // Extra properties parameters in constructor
        $operation = new UpdatePosition(
            extraProperties: ['ApiResourceMapping' => $resourceMapping],
        );
        $this->assertEquals(['ApiResourceMapping' => $resourceMapping], $operation->getExtraProperties());
        $this->assertEquals($resourceMapping, $operation->getApiResourceMapping());

        // Extra properties AND Api resource mapping parameters in constructor, both values are equals no problem
        $operation = new UpdatePosition(
            extraProperties: ['ApiResourceMapping' => $resourceMapping],
            ApiResourceMapping: $resourceMapping,
        );
        $this->assertEquals(['ApiResourceMapping' => $resourceMapping], $operation->getExtraProperties());
        $this->assertEquals($resourceMapping, $operation->getApiResourceMapping());

        // Use with method, returned object is a clone All values are replaced
        $newMapping = ['[queryId' => '[valueObjectId]'];
        $operation2 = $operation->withApiResourceMapping($newMapping);
        $this->assertNotEquals($operation2, $operation);
        $this->assertEquals(['ApiResourceMapping' => $newMapping], $operation2->getExtraProperties());
        $this->assertEquals($newMapping, $operation2->getApiResourceMapping());
        // Initial operation not modified of course
        $this->assertEquals(['ApiResourceMapping' => $resourceMapping], $operation->getExtraProperties());
        $this->assertEquals($resourceMapping, $operation->getApiResourceMapping());

        // When both values are specified, but they are different trigger an exception
        $caughtException = null;
        try {
            new UpdatePosition(
                extraProperties: ['ApiResourceMapping' => $resourceMapping],
                ApiResourceMapping: $newMapping,
            );
        } catch (InvalidArgumentException $e) {
            $caughtException = $e;
        }

        $this->assertNotNull($caughtException);
        $this->assertInstanceOf(InvalidArgumentException::class, $caughtException);
        $this->assertEquals('Specifying an extra property ApiResourceMapping and a ApiResourceMapping argument that are different is invalid', $caughtException->getMessage());
    }

    public function testPositionDefinition(): void
    {
        // PositionDefinition parameters in constructor
        $operation = new UpdatePosition(
            positionDefinition: 'prestashop.core.grid.attribute.position_definition'
        );
        $this->assertEquals(['positionDefinition' => 'prestashop.core.grid.attribute.position_definition'], $operation->getExtraProperties());
        $this->assertEquals('prestashop.core.grid.attribute.position_definition', $operation->getPositionDefinition());

        // Extra properties parameters in constructor
        $operation = new UpdatePosition(
            extraProperties: ['positionDefinition' => 'prestashop.core.grid.attribute.position_definition']
        );
        $this->assertEquals(['positionDefinition' => 'prestashop.core.grid.attribute.position_definition'], $operation->getExtraProperties());
        $this->assertEquals('prestashop.core.grid.attribute.position_definition', $operation->getPositionDefinition());

        // Use with method, returned object is a clone All values are replaced
        $operation2 = $operation->withPositionDefinition('prestashop.core.grid.attribute.position_definition2');
        $this->assertNotEquals($operation2, $operation);
        $this->assertEquals(['positionDefinition' => 'prestashop.core.grid.attribute.position_definition2'], $operation2->getExtraProperties());
        $this->assertEquals('prestashop.core.grid.attribute.position_definition2', $operation2->getPositionDefinition());
        // Initial operation not modified of course
        $this->assertEquals(['positionDefinition' => 'prestashop.core.grid.attribute.position_definition'], $operation->getExtraProperties());
        $this->assertEquals('prestashop.core.grid.attribute.position_definition', $operation->getPositionDefinition());
    }

    public function testMultipleArguments(): void
    {
        $resourceMapping = ['[id]' => '[queryId]'];
        $operation = new UpdatePosition(
            extraProperties: [
                'scopes' => ['master_scope'],
            ],
            scopes: ['scope1', 'scope2'],
            ApiResourceMapping: $resourceMapping,
            positionDefinition: 'prestashop.core.grid.attribute.position_definition',
        );

        $this->assertNull($operation->getProvider());
        $this->assertEquals(UpdatePositionProcessor::class, $operation->getProcessor());
        $this->assertEquals($resourceMapping, $operation->getApiResourceMapping());
        $this->assertEquals(['master_scope', 'scope1', 'scope2'], $operation->getScopes());
        $this->assertEquals('prestashop.core.grid.attribute.position_definition', $operation->getPositionDefinition());
        $this->assertEquals([
            'scopes' => ['master_scope', 'scope1', 'scope2'],
            'ApiResourceMapping' => $resourceMapping,
            'positionDefinition' => 'prestashop.core.grid.attribute.position_definition',
        ], $operation->getExtraProperties());

        // Using with clones the object, only one extra parameter is modified
        $operation2 = $operation->withScopes(['scope3']);
        $operation3 = $operation2->withPositionDefinition('prestashop.core.grid.attribute.position_definition2');
        $this->assertNotEquals($operation2, $operation);
        $this->assertNotEquals($operation2, $operation3);
        $this->assertNotEquals($operation3, $operation);

        // Check first clone operation2
        $this->assertNull($operation2->getProvider());
        $this->assertEquals(UpdatePositionProcessor::class, $operation2->getProcessor());
        $this->assertEquals($resourceMapping, $operation2->getApiResourceMapping());
        $this->assertEquals('prestashop.core.grid.attribute.position_definition', $operation2->getPositionDefinition());
        $this->assertEquals(['scope3'], $operation2->getScopes());
        $this->assertEquals([
            'scopes' => ['scope3'],
            'ApiResourceMapping' => $resourceMapping,
            'positionDefinition' => 'prestashop.core.grid.attribute.position_definition',
        ], $operation2->getExtraProperties());

        // Check second clone operation3
        $this->assertNull($operation3->getProvider());
        $this->assertEquals(UpdatePositionProcessor::class, $operation3->getProcessor());
        $this->assertEquals($resourceMapping, $operation3->getApiResourceMapping());
        $this->assertEquals('prestashop.core.grid.attribute.position_definition2', $operation3->getPositionDefinition());
        $this->assertEquals(['scope3'], $operation3->getScopes());
        $this->assertEquals([
            'scopes' => ['scope3'],
            'ApiResourceMapping' => $resourceMapping,
            'positionDefinition' => 'prestashop.core.grid.attribute.position_definition2',
        ], $operation3->getExtraProperties());

        // The original object has not been modified
        $this->assertNull($operation->getProvider());
        $this->assertEquals(UpdatePositionProcessor::class, $operation->getProcessor());
        $this->assertEquals($resourceMapping, $operation->getApiResourceMapping());
        $this->assertEquals(['master_scope', 'scope1', 'scope2'], $operation->getScopes());
        $this->assertEquals([
            'scopes' => ['master_scope', 'scope1', 'scope2'],
            'ApiResourceMapping' => $resourceMapping,
            'positionDefinition' => 'prestashop.core.grid.attribute.position_definition',
        ], $operation->getExtraProperties());
    }

    public function testExperimentalOperation(): void
    {
        // Default value is false (no extra property added)
        $operation = new UpdatePosition();
        $this->assertEquals([], $operation->getExtraProperties());
        $this->assertEquals(false, $operation->getExperimentalOperation());

        // Scopes parameters in constructor
        $operation = new UpdatePosition(
            experimentalOperation: true,
        );
        $this->assertEquals(['experimentalOperation' => true], $operation->getExtraProperties());
        $this->assertEquals(true, $operation->getExperimentalOperation());

        // Extra properties parameters in constructor
        $operation = new UpdatePosition(
            extraProperties: ['experimentalOperation' => false]
        );
        $this->assertEquals(['experimentalOperation' => false], $operation->getExtraProperties());
        $this->assertEquals(false, $operation->getExperimentalOperation());

        // Extra properties AND scopes parameters in constructor, both values get merged but remain unique
        $operation = new UpdatePosition(
            extraProperties: ['experimentalOperation' => true],
            experimentalOperation: true,
        );
        $this->assertEquals(['experimentalOperation' => true], $operation->getExtraProperties());
        $this->assertEquals(true, $operation->getExperimentalOperation());

        // Use with method, returned object is a clone All values are replaced
        $operation2 = $operation->withExperimentalOperation(false);
        $this->assertNotEquals($operation2, $operation);
        $this->assertEquals(['experimentalOperation' => false], $operation2->getExtraProperties());
        $this->assertEquals(false, $operation2->getExperimentalOperation());
        // Initial operation not modified of course
        $this->assertEquals(['experimentalOperation' => true], $operation->getExtraProperties());
        $this->assertEquals(true, $operation->getExperimentalOperation());

        // When both values are specified, but they are different trigger an exception
        $caughtException = null;
        try {
            new UpdatePosition(
                extraProperties: ['experimentalOperation' => true],
                experimentalOperation: false,
            );
        } catch (InvalidArgumentException $e) {
            $caughtException = $e;
        }

        $this->assertNotNull($caughtException);
        $this->assertInstanceOf(InvalidArgumentException::class, $caughtException);
        $this->assertEquals('Specifying an extra property experimentalOperation and a experimentalOperation argument that are different is invalid', $caughtException->getMessage());
    }
}
