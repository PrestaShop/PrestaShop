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
use PrestaShop\PrestaShop\Core\Search\Filters\LanguageFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\ProductFilters;
use PrestaShopBundle\ApiPlatform\Metadata\PaginatedList;
use PrestaShopBundle\ApiPlatform\Provider\QueryListProvider;

class PaginatedListTest extends TestCase
{
    public function testDefaultConstructor(): void
    {
        // Without any parameters
        $operation = new PaginatedList();
        $this->assertEquals(QueryListProvider::class, $operation->getProvider());
        $this->assertEquals(PaginatedList::METHOD_GET, $operation->getMethod());
        $this->assertEquals([], $operation->getExtraProperties());
        $this->assertEquals(['json'], $operation->getFormats());

        // With positioned parameters
        $operation = new PaginatedList('/uri');
        $this->assertEquals(QueryListProvider::class, $operation->getProvider());
        $this->assertEquals('/uri', $operation->getUriTemplate());
        $this->assertEquals([], $operation->getExtraProperties());
        $this->assertEquals(['json'], $operation->getFormats());

        // With named parameters
        $operation = new PaginatedList(
            formats: ['json', 'html'],
            extraProperties: ['scopes' => ['test']]
        );
        $this->assertEquals(QueryListProvider::class, $operation->getProvider());
        $this->assertEquals(['scopes' => ['test']], $operation->getExtraProperties());
        $this->assertEquals(['json', 'html'], $operation->getFormats());
    }

    public function testFiltersClass(): void
    {
        // Filters mapping parameters in constructor
        $filtersClass = ProductFilters::class;
        $operation = new PaginatedList(
            filtersClass: $filtersClass,
        );

        $this->assertEquals(['filtersClass' => $filtersClass], $operation->getExtraProperties());
        $this->assertEquals($filtersClass, $operation->getFiltersClass());

        // Extra properties parameters in constructor
        $operation = new PaginatedList(
            extraProperties: ['filtersClass' => $filtersClass],
        );
        $this->assertEquals(['filtersClass' => $filtersClass], $operation->getExtraProperties());
        $this->assertEquals($filtersClass, $operation->getFiltersClass());

        // Extra properties AND CQRS query mapping parameters in constructor, both values are equals no problem
        $operation = new PaginatedList(
            extraProperties: ['filtersClass' => $filtersClass],
            filtersClass: $filtersClass,
        );
        $this->assertEquals(['filtersClass' => $filtersClass], $operation->getExtraProperties());
        $this->assertEquals($filtersClass, $operation->getFiltersClass());

        // Use with method, returned object is a clone All values are replaced
        $newMapping = LanguageFilters::class;
        $operation2 = $operation->withFiltersClass($newMapping);
        $this->assertNotEquals($operation2, $operation);
        $this->assertEquals(['filtersClass' => $newMapping], $operation2->getExtraProperties());
        $this->assertEquals($newMapping, $operation2->getFiltersClass());
        // Initial operation not modified of course
        $this->assertEquals(['filtersClass' => $filtersClass], $operation->getExtraProperties());
        $this->assertEquals($filtersClass, $operation->getFiltersClass());

        // When both values are specified, but they are different trigger an exception
        $caughtException = null;
        try {
            new PaginatedList(
                extraProperties: ['filtersClass' => $filtersClass],
                filtersClass: $newMapping,
            );
        } catch (InvalidArgumentException $e) {
            $caughtException = $e;
        }

        $this->assertNotNull($caughtException);
        $this->assertInstanceOf(InvalidArgumentException::class, $caughtException);
        $this->assertEquals('Specifying an extra property filtersClass and a filtersClass argument that are different is invalid', $caughtException->getMessage());
    }

    public function testFiltersMapping(): void
    {
        // Filters mapping parameters in constructor
        $filtersMapping = ['[langId]' => '[id_lang]'];
        $operation = new PaginatedList(
            filtersMapping: $filtersMapping,
        );

        $this->assertEquals(['filtersMapping' => $filtersMapping], $operation->getExtraProperties());
        $this->assertEquals($filtersMapping, $operation->getFiltersMapping());

        // Extra properties parameters in constructor
        $operation = new PaginatedList(
            extraProperties: ['filtersMapping' => $filtersMapping],
        );
        $this->assertEquals(['filtersMapping' => $filtersMapping], $operation->getExtraProperties());
        $this->assertEquals($filtersMapping, $operation->getFiltersMapping());

        // Extra properties AND CQRS query mapping parameters in constructor, both values are equals no problem
        $operation = new PaginatedList(
            extraProperties: ['filtersMapping' => $filtersMapping],
            filtersMapping: $filtersMapping,
        );
        $this->assertEquals(['filtersMapping' => $filtersMapping], $operation->getExtraProperties());
        $this->assertEquals($filtersMapping, $operation->getFiltersMapping());

        // Use with method, returned object is a clone All values are replaced
        $newMapping = ['[queryId' => '[valueObjectId]'];
        $operation2 = $operation->withFiltersMapping($newMapping);
        $this->assertNotEquals($operation2, $operation);
        $this->assertEquals(['filtersMapping' => $newMapping], $operation2->getExtraProperties());
        $this->assertEquals($newMapping, $operation2->getFiltersMapping());
        // Initial operation not modified of course
        $this->assertEquals(['filtersMapping' => $filtersMapping], $operation->getExtraProperties());
        $this->assertEquals($filtersMapping, $operation->getFiltersMapping());

        // When both values are specified, but they are different trigger an exception
        $caughtException = null;
        try {
            new PaginatedList(
                extraProperties: ['filtersMapping' => $filtersMapping],
                filtersMapping: $newMapping,
            );
        } catch (InvalidArgumentException $e) {
            $caughtException = $e;
        }

        $this->assertNotNull($caughtException);
        $this->assertInstanceOf(InvalidArgumentException::class, $caughtException);
        $this->assertEquals('Specifying an extra property filtersMapping and a filtersMapping argument that are different is invalid', $caughtException->getMessage());
    }

    public function testScopes(): void
    {
        // Scopes parameters in constructor
        $operation = new PaginatedList(
            scopes: ['test', 'test2']
        );
        $this->assertEquals(['scopes' => ['test', 'test2']], $operation->getExtraProperties());
        $this->assertEquals(['test', 'test2'], $operation->getScopes());

        // Extra properties parameters in constructor
        $operation = new PaginatedList(
            extraProperties: ['scopes' => ['test']]
        );
        $this->assertEquals(['scopes' => ['test']], $operation->getExtraProperties());
        $this->assertEquals(['test'], $operation->getScopes());

        // Extra properties AND scopes parameters in constructor, both values get merged but remain unique
        $operation = new PaginatedList(
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
        $operation = new PaginatedList(
            ApiResourceMapping: $resourceMapping,
        );

        $this->assertEquals(['ApiResourceMapping' => $resourceMapping], $operation->getExtraProperties());
        $this->assertEquals($resourceMapping, $operation->getApiResourceMapping());

        // Extra properties parameters in constructor
        $operation = new PaginatedList(
            extraProperties: ['ApiResourceMapping' => $resourceMapping],
        );
        $this->assertEquals(['ApiResourceMapping' => $resourceMapping], $operation->getExtraProperties());
        $this->assertEquals($resourceMapping, $operation->getApiResourceMapping());

        // Extra properties AND Api resource mapping parameters in constructor, both values are equals no problem
        $operation = new PaginatedList(
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
            new PaginatedList(
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

    public function testGridDataFactory(): void
    {
        // GridDataFactory parameters in constructor
        $operation = new PaginatedList(
            gridDataFactory: 'prestashop.core.api.query_builder.hook'
        );
        $this->assertEquals(['gridDataFactory' => 'prestashop.core.api.query_builder.hook'], $operation->getExtraProperties());
        $this->assertEquals('prestashop.core.api.query_builder.hook', $operation->getGridDataFactory());

        // Extra properties parameters in constructor
        $operation = new PaginatedList(
            extraProperties: ['gridDataFactory' => 'prestashop.core.api.query_builder.hook']
        );
        $this->assertEquals(['gridDataFactory' => 'prestashop.core.api.query_builder.hook'], $operation->getExtraProperties());
        $this->assertEquals('prestashop.core.api.query_builder.hook', $operation->getGridDataFactory());

        // Use with method, returned object is a clone All values are replaced
        $operation2 = $operation->withGridDataFactory('prestashop.core.api.query_builder.hook2');
        $this->assertNotEquals($operation2, $operation);
        $this->assertEquals(['gridDataFactory' => 'prestashop.core.api.query_builder.hook2'], $operation2->getExtraProperties());
        $this->assertEquals('prestashop.core.api.query_builder.hook2', $operation2->getGridDataFactory());
        // Initial operation not modified of course
        $this->assertEquals(['gridDataFactory' => 'prestashop.core.api.query_builder.hook'], $operation->getExtraProperties());
        $this->assertEquals('prestashop.core.api.query_builder.hook', $operation->getGridDataFactory());
    }

    public function testMultipleArguments(): void
    {
        $resourceMapping = ['[id]' => '[queryId]'];
        $operation = new PaginatedList(
            extraProperties: [
                'scopes' => ['master_scope'],
            ],
            scopes: ['scope1', 'scope2'],
            ApiResourceMapping: $resourceMapping,
            gridDataFactory: 'prestashop.core.api.query_builder.hook',
        );

        $this->assertEquals(QueryListProvider::class, $operation->getProvider());
        $this->assertEquals($resourceMapping, $operation->getApiResourceMapping());
        $this->assertEquals(['master_scope', 'scope1', 'scope2'], $operation->getScopes());
        $this->assertEquals('prestashop.core.api.query_builder.hook', $operation->getGridDataFactory());
        $this->assertEquals([
            'scopes' => ['master_scope', 'scope1', 'scope2'],
            'ApiResourceMapping' => $resourceMapping,
            'gridDataFactory' => 'prestashop.core.api.query_builder.hook',
        ], $operation->getExtraProperties());

        // Using with clones the object, only one extra parameter is modified
        $operation2 = $operation->withScopes(['scope3']);
        $operation3 = $operation2->withGridDataFactory('prestashop.core.api.query_builder.hook2');
        $this->assertNotEquals($operation2, $operation);
        $this->assertNotEquals($operation2, $operation3);
        $this->assertNotEquals($operation3, $operation);

        // Check first clone operation2
        $this->assertEquals(QueryListProvider::class, $operation2->getProvider());
        $this->assertEquals($resourceMapping, $operation2->getApiResourceMapping());
        $this->assertEquals('prestashop.core.api.query_builder.hook', $operation2->getGridDataFactory());
        $this->assertEquals(['scope3'], $operation2->getScopes());
        $this->assertEquals([
            'scopes' => ['scope3'],
            'ApiResourceMapping' => $resourceMapping,
            'gridDataFactory' => 'prestashop.core.api.query_builder.hook',
        ], $operation2->getExtraProperties());

        // Check second clone operation3
        $this->assertEquals(QueryListProvider::class, $operation3->getProvider());
        $this->assertEquals($resourceMapping, $operation3->getApiResourceMapping());
        $this->assertEquals('prestashop.core.api.query_builder.hook2', $operation3->getGridDataFactory());
        $this->assertEquals(['scope3'], $operation3->getScopes());
        $this->assertEquals([
            'scopes' => ['scope3'],
            'ApiResourceMapping' => $resourceMapping,
            'gridDataFactory' => 'prestashop.core.api.query_builder.hook2',
        ], $operation3->getExtraProperties());

        // The original object has not been modified
        $this->assertEquals(QueryListProvider::class, $operation->getProvider());
        $this->assertEquals($resourceMapping, $operation->getApiResourceMapping());
        $this->assertEquals(['master_scope', 'scope1', 'scope2'], $operation->getScopes());
        $this->assertEquals([
            'scopes' => ['master_scope', 'scope1', 'scope2'],
            'ApiResourceMapping' => $resourceMapping,
            'gridDataFactory' => 'prestashop.core.api.query_builder.hook',
        ], $operation->getExtraProperties());
    }

    public function testExperimentalOperation(): void
    {
        // Default value is false (no extra property added)
        $operation = new PaginatedList();
        $this->assertEquals([], $operation->getExtraProperties());
        $this->assertEquals(false, $operation->getExperimentalOperation());

        // Scopes parameters in constructor
        $operation = new PaginatedList(
            experimentalOperation: true,
        );
        $this->assertEquals(['experimentalOperation' => true], $operation->getExtraProperties());
        $this->assertEquals(true, $operation->getExperimentalOperation());

        // Extra properties parameters in constructor
        $operation = new PaginatedList(
            extraProperties: ['experimentalOperation' => false]
        );
        $this->assertEquals(['experimentalOperation' => false], $operation->getExtraProperties());
        $this->assertEquals(false, $operation->getExperimentalOperation());

        // Extra properties AND scopes parameters in constructor, both values get merged but remain unique
        $operation = new PaginatedList(
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
            new PaginatedList(
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
