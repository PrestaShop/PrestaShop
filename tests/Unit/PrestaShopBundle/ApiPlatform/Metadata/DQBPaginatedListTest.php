<?php

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\ApiPlatform\Metadata;

use ApiPlatform\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\ApiPlatform\Metadata\DQBPaginatedList;
use PrestaShopBundle\ApiPlatform\Provider\QueryListProvider;

class DQBPaginatedListTest extends TestCase
{
    public function testDefaultConstructor(): void
    {
        // Without any parameters
        $operation = new DQBPaginatedList();
        $this->assertEquals(QueryListProvider::class, $operation->getProvider());
        $this->assertEquals(DQBPaginatedList::METHOD_GET, $operation->getMethod());
        $this->assertEquals([], $operation->getExtraProperties());
        $this->assertEquals(['json'], $operation->getFormats());

        // With positioned parameters
        $operation = new DQBPaginatedList('/uri');
        $this->assertEquals(QueryListProvider::class, $operation->getProvider());
        $this->assertEquals('/uri', $operation->getUriTemplate());
        $this->assertEquals([], $operation->getExtraProperties());
        $this->assertEquals(['json'], $operation->getFormats());

        // With named parameters
        $operation = new DQBPaginatedList(
            formats: ['json', 'html'],
            extraProperties: ['scopes' => ['test']]
        );
        $this->assertEquals(QueryListProvider::class, $operation->getProvider());
        $this->assertEquals(['scopes' => ['test']], $operation->getExtraProperties());
        $this->assertEquals(['json', 'html'], $operation->getFormats());
    }

    public function testScopes(): void
    {
        // Scopes parameters in constructor
        $operation = new DQBPaginatedList(
            scopes: ['test', 'test2']
        );
        $this->assertEquals(['scopes' => ['test', 'test2']], $operation->getExtraProperties());
        $this->assertEquals(['test', 'test2'], $operation->getScopes());

        // Extra properties parameters in constructor
        $operation = new DQBPaginatedList(
            extraProperties: ['scopes' => ['test']]
        );
        $this->assertEquals(['scopes' => ['test']], $operation->getExtraProperties());
        $this->assertEquals(['test'], $operation->getScopes());

        // Extra properties AND scopes parameters in constructor, both values get merged but remain unique
        $operation = new DQBPaginatedList(
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
        $operation = new DQBPaginatedList(
            ApiResourceMapping: $resourceMapping,
        );

        $this->assertEquals(['ApiResourceMapping' => $resourceMapping], $operation->getExtraProperties());
        $this->assertEquals($resourceMapping, $operation->getApiResourceMapping());

        // Extra properties parameters in constructor
        $operation = new DQBPaginatedList(
            extraProperties: ['ApiResourceMapping' => $resourceMapping],
        );
        $this->assertEquals(['ApiResourceMapping' => $resourceMapping], $operation->getExtraProperties());
        $this->assertEquals($resourceMapping, $operation->getApiResourceMapping());

        // Extra properties AND Api resource mapping parameters in constructor, both values are equals no problem
        $operation = new DQBPaginatedList(
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
            new DQBPaginatedList(
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

    public function testQueryBuilder(): void
    {
        // QueryBuilder parameters in constructor
        $operation = new DQBPaginatedList(
            queryBuilder: 'prestashop.core.api.query_builder.hook'
        );
        $this->assertEquals(['queryBuilder' => 'prestashop.core.api.query_builder.hook'], $operation->getExtraProperties());
        $this->assertEquals('prestashop.core.api.query_builder.hook', $operation->getQueryBuilder());

        // Extra properties parameters in constructor
        $operation = new DQBPaginatedList(
            extraProperties: ['queryBuilder' => 'prestashop.core.api.query_builder.hook']
        );
        $this->assertEquals(['queryBuilder' => 'prestashop.core.api.query_builder.hook'], $operation->getExtraProperties());
        $this->assertEquals('prestashop.core.api.query_builder.hook', $operation->getQueryBuilder());

        // Use with method, returned object is a clone All values are replaced
        $operation2 = $operation->withQueryBuilder('prestashop.core.api.query_builder.hook2');
        $this->assertNotEquals($operation2, $operation);
        $this->assertEquals(['queryBuilder' => 'prestashop.core.api.query_builder.hook2'], $operation2->getExtraProperties());
        $this->assertEquals('prestashop.core.api.query_builder.hook2', $operation2->getQueryBuilder());
        // Initial operation not modified of course
        $this->assertEquals(['queryBuilder' => 'prestashop.core.api.query_builder.hook'], $operation->getExtraProperties());
        $this->assertEquals('prestashop.core.api.query_builder.hook', $operation->getQueryBuilder());
    }

    public function testMultipleArguments(): void
    {
        $resourceMapping = ['[id]' => '[queryId]'];
        $operation = new DQBPaginatedList(
            extraProperties: [
                'scopes' => ['master_scope'],
            ],
            scopes: ['scope1', 'scope2'],
            ApiResourceMapping: $resourceMapping,
            queryBuilder: 'prestashop.core.api.query_builder.hook',
        );

        $this->assertEquals(QueryListProvider::class, $operation->getProvider());
        $this->assertEquals($resourceMapping, $operation->getApiResourceMapping());
        $this->assertEquals(['master_scope', 'scope1', 'scope2'], $operation->getScopes());
        $this->assertEquals('prestashop.core.api.query_builder.hook', $operation->getQueryBuilder());
        $this->assertEquals([
            'scopes' => ['master_scope', 'scope1', 'scope2'],
            'ApiResourceMapping' => $resourceMapping,
            'queryBuilder' => 'prestashop.core.api.query_builder.hook',
        ], $operation->getExtraProperties());

        // Using with clones the object, only one extra parameter is modified
        $operation2 = $operation->withScopes(['scope3']);
        $operation3 = $operation2->withQueryBuilder('prestashop.core.api.query_builder.hook2');
        $this->assertNotEquals($operation2, $operation);
        $this->assertNotEquals($operation2, $operation3);
        $this->assertNotEquals($operation3, $operation);

        // Check first clone operation2
        $this->assertEquals(QueryListProvider::class, $operation2->getProvider());
        $this->assertEquals($resourceMapping, $operation2->getApiResourceMapping());
        $this->assertEquals('prestashop.core.api.query_builder.hook', $operation2->getQueryBuilder());
        $this->assertEquals(['scope3'], $operation2->getScopes());
        $this->assertEquals([
            'scopes' => ['scope3'],
            'ApiResourceMapping' => $resourceMapping,
            'queryBuilder' => 'prestashop.core.api.query_builder.hook',
        ], $operation2->getExtraProperties());

        // Check second clone operation3
        $this->assertEquals(QueryListProvider::class, $operation3->getProvider());
        $this->assertEquals($resourceMapping, $operation3->getApiResourceMapping());
        $this->assertEquals('prestashop.core.api.query_builder.hook2', $operation3->getQueryBuilder());
        $this->assertEquals(['scope3'], $operation3->getScopes());
        $this->assertEquals([
            'scopes' => ['scope3'],
            'ApiResourceMapping' => $resourceMapping,
            'queryBuilder' => 'prestashop.core.api.query_builder.hook2',
        ], $operation3->getExtraProperties());

        // The original object has not been modified
        $this->assertEquals(QueryListProvider::class, $operation->getProvider());
        $this->assertEquals($resourceMapping, $operation->getApiResourceMapping());
        $this->assertEquals(['master_scope', 'scope1', 'scope2'], $operation->getScopes());
        $this->assertEquals([
            'scopes' => ['master_scope', 'scope1', 'scope2'],
            'ApiResourceMapping' => $resourceMapping,
            'queryBuilder' => 'prestashop.core.api.query_builder.hook',
        ], $operation->getExtraProperties());
    }
}
