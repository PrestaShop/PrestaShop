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

namespace PrestaShopBundle\ApiPlatform\Metadata;

use ApiPlatform\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Search\Filters\LanguageFilters;
use PrestaShop\PrestaShop\Core\Search\Filters\ProductFilters;
use PrestaShopBundle\ApiPlatform\Provider\QueryListProvider;

class CQRSPaginateTest extends TestCase
{
    public function testDefaultConstructor(): void
    {
        // Without any parameters
        $operation = new CQRSPaginate();
        $this->assertEquals(QueryListProvider::class, $operation->getProvider());
        $this->assertEquals(CQRSPaginate::METHOD_GET, $operation->getMethod());
        $this->assertEquals([], $operation->getExtraProperties());
        $this->assertEquals(['json'], $operation->getFormats());

        // With positioned parameters
        $operation = new CQRSPaginate('/uri');
        $this->assertEquals(QueryListProvider::class, $operation->getProvider());
        $this->assertEquals('/uri', $operation->getUriTemplate());
        $this->assertEquals([], $operation->getExtraProperties());
        $this->assertEquals(['json'], $operation->getFormats());

        // With named parameters
        $operation = new CQRSPaginate(
            formats: ['json', 'html'],
            extraProperties: ['scopes' => ['test']]
        );
        $this->assertEquals(QueryListProvider::class, $operation->getProvider());
        $this->assertEquals(['scopes' => ['test']], $operation->getExtraProperties());
        $this->assertEquals(['json', 'html'], $operation->getFormats());
    }

    public function testCQRSQuery(): void
    {
        // CQRS query parameters in constructor
        $operation = new CQRSPaginate(
            CQRSQuery: 'My\\Namespace\\MyQuery',
        );
        $this->assertEquals(['CQRSQuery' => 'My\\Namespace\\MyQuery'], $operation->getExtraProperties());
        $this->assertEquals('My\\Namespace\\MyQuery', $operation->getCQRSQuery());

        // Extra properties parameters in constructor
        $operation = new CQRSPaginate(
            extraProperties: ['CQRSQuery' => 'My\\Namespace\\MyQuery'],
        );
        $this->assertEquals(['CQRSQuery' => 'My\\Namespace\\MyQuery'], $operation->getExtraProperties());
        $this->assertEquals('My\\Namespace\\MyQuery', $operation->getCQRSQuery());

        // Extra properties AND CQRS query parameters in constructor, both values are equals no problem
        $operation = new CQRSPaginate(
            extraProperties: ['CQRSQuery' => 'My\\Namespace\\MyQuery'],
            CQRSQuery: 'My\\Namespace\\MyQuery',
        );
        $this->assertEquals(['CQRSQuery' => 'My\\Namespace\\MyQuery'], $operation->getExtraProperties());
        $this->assertEquals('My\\Namespace\\MyQuery', $operation->getCQRSQuery());

        // Use with method, returned object is a clone All values are replaced
        $operation2 = $operation->withCQRSQuery('My\\Namespace\\MyOtherQuery');
        $this->assertNotEquals($operation2, $operation);
        $this->assertEquals(['CQRSQuery' => 'My\\Namespace\\MyOtherQuery'], $operation2->getExtraProperties());
        $this->assertEquals('My\\Namespace\\MyOtherQuery', $operation2->getCQRSQuery());
        // Initial operation not modified of course
        $this->assertEquals(['CQRSQuery' => 'My\\Namespace\\MyQuery'], $operation->getExtraProperties());
        $this->assertEquals('My\\Namespace\\MyQuery', $operation->getCQRSQuery());

        // When both values are specified, but they are different trigger an exception
        $caughtException = null;
        try {
            new CQRSPaginate(
                extraProperties: ['CQRSQuery' => 'My\\Namespace\\MyQuery'],
                CQRSQuery: 'My\\Namespace\\MyOtherQuery',
            );
        } catch (InvalidArgumentException $e) {
            $caughtException = $e;
        }

        $this->assertNotNull($caughtException);
        $this->assertInstanceOf(InvalidArgumentException::class, $caughtException);
        $this->assertEquals('Specifying an extra property CQRSQuery and a CQRSQuery argument that are different is invalid', $caughtException->getMessage());
    }

    public function testCQRSQueryMapping(): void
    {
        // CQRS query mapping parameters in constructor
        $queryMapping = ['[id]' => '[queryId]'];
        $operation = new CQRSPaginate(
            CQRSQueryMapping: $queryMapping,
        );

        $this->assertEquals(['CQRSQueryMapping' => $queryMapping], $operation->getExtraProperties());
        $this->assertEquals($queryMapping, $operation->getCQRSQueryMapping());

        // Extra properties parameters in constructor
        $operation = new CQRSPaginate(
            extraProperties: ['CQRSQueryMapping' => $queryMapping],
        );
        $this->assertEquals(['CQRSQueryMapping' => $queryMapping], $operation->getExtraProperties());
        $this->assertEquals($queryMapping, $operation->getCQRSQueryMapping());

        // Extra properties AND CQRS query mapping parameters in constructor, both values are equals no problem
        $operation = new CQRSPaginate(
            extraProperties: ['CQRSQueryMapping' => $queryMapping],
            CQRSQueryMapping: $queryMapping,
        );
        $this->assertEquals(['CQRSQueryMapping' => $queryMapping], $operation->getExtraProperties());
        $this->assertEquals($queryMapping, $operation->getCQRSQueryMapping());

        // Use with method, returned object is a clone All values are replaced
        $newMapping = ['[queryId' => '[valueObjectId]'];
        $operation2 = $operation->withCQRSQueryMapping($newMapping);
        $this->assertNotEquals($operation2, $operation);
        $this->assertEquals(['CQRSQueryMapping' => $newMapping], $operation2->getExtraProperties());
        $this->assertEquals($newMapping, $operation2->getCQRSQueryMapping());
        // Initial operation not modified of course
        $this->assertEquals(['CQRSQueryMapping' => $queryMapping], $operation->getExtraProperties());
        $this->assertEquals($queryMapping, $operation->getCQRSQueryMapping());

        // When both values are specified, but they are different trigger an exception
        $caughtException = null;
        try {
            new CQRSPaginate(
                extraProperties: ['CQRSQueryMapping' => $queryMapping],
                CQRSQueryMapping: $newMapping,
            );
        } catch (InvalidArgumentException $e) {
            $caughtException = $e;
        }

        $this->assertNotNull($caughtException);
        $this->assertInstanceOf(InvalidArgumentException::class, $caughtException);
        $this->assertEquals('Specifying an extra property CQRSQueryMapping and a CQRSQueryMapping argument that are different is invalid', $caughtException->getMessage());
    }

    public function testItemsField(): void
    {
        // itemsField parameters in constructor
        $operation = new CQRSPaginate(
            itemsField: 'items',
        );
        $this->assertEquals(['itemsField' => 'items'], $operation->getExtraProperties());
        $this->assertEquals('items', $operation->getItemsField());

        // Extra properties parameters in constructor
        $operation = new CQRSPaginate(
            extraProperties: ['itemsField' => 'items'],
        );
        $this->assertEquals(['itemsField' => 'items'], $operation->getExtraProperties());
        $this->assertEquals('items', $operation->getItemsField());

        // Extra properties AND CQRS query parameters in constructor, both values are equals no problem
        $operation = new CQRSPaginate(
            extraProperties: ['itemsField' => 'items'],
            itemsField: 'items',
        );
        $this->assertEquals(['itemsField' => 'items'], $operation->getExtraProperties());
        $this->assertEquals('items', $operation->getItemsField());

        // Use with method, returned object is a clone All values are replaced
        $operation2 = $operation->withItemsField('combinations');
        $this->assertNotEquals($operation2, $operation);
        $this->assertEquals(['itemsField' => 'combinations'], $operation2->getExtraProperties());
        $this->assertEquals('combinations', $operation2->getItemsField());
        // Initial operation not modified of course
        $this->assertEquals(['itemsField' => 'items'], $operation->getExtraProperties());
        $this->assertEquals('items', $operation->getItemsField());

        // When both values are specified, but they are different trigger an exception
        $caughtException = null;
        try {
            new CQRSPaginate(
                extraProperties: ['itemsField' => 'items'],
                itemsField: 'combinations',
            );
        } catch (InvalidArgumentException $e) {
            $caughtException = $e;
        }

        $this->assertNotNull($caughtException);
        $this->assertInstanceOf(InvalidArgumentException::class, $caughtException);
        $this->assertEquals('Specifying an extra property itemsField and a itemsField argument that are different is invalid', $caughtException->getMessage());
    }

    public function testCountFields(): void
    {
        // CQRS query mapping parameters in constructor
        $operation = new CQRSPaginate(
            countField: 'count',
        );

        $this->assertEquals(['countField' => 'count'], $operation->getExtraProperties());
        $this->assertEquals('count', $operation->getCountField());

        // Extra properties parameters in constructor
        $operation = new CQRSPaginate(
            extraProperties: ['countField' => 'count'],
        );
        $this->assertEquals(['countField' => 'count'], $operation->getExtraProperties());
        $this->assertEquals('count', $operation->getCountField());

        // Extra properties AND CQRS query mapping parameters in constructor, both values are equals no problem
        $operation = new CQRSPaginate(
            extraProperties: ['countField' => 'count'],
            countField: 'count',
        );
        $this->assertEquals(['countField' => 'count'], $operation->getExtraProperties());
        $this->assertEquals('count', $operation->getCountField());

        // Use with method, returned object is a clone All values are replaced
        $operation2 = $operation->withCountField('totalCombinations');
        $this->assertNotEquals($operation2, $operation);
        $this->assertEquals(['countField' => 'totalCombinations'], $operation2->getExtraProperties());
        $this->assertEquals('totalCombinations', $operation2->getCountField());
        // Initial operation not modified of course
        $this->assertEquals(['countField' => 'count'], $operation->getExtraProperties());
        $this->assertEquals('count', $operation->getCountField());

        // When both values are specified, but they are different trigger an exception
        $caughtException = null;
        try {
            new CQRSPaginate(
                extraProperties: ['countField' => 'count'],
                countField: 'totalCombinations',
            );
        } catch (InvalidArgumentException $e) {
            $caughtException = $e;
        }

        $this->assertNotNull($caughtException);
        $this->assertInstanceOf(InvalidArgumentException::class, $caughtException);
        $this->assertEquals('Specifying an extra property countField and a countField argument that are different is invalid', $caughtException->getMessage());
    }

    public function testFiltersClass(): void
    {
        // Filters mapping parameters in constructor
        $filtersClass = ProductFilters::class;
        $operation = new CQRSPaginate(
            filtersClass: $filtersClass,
        );

        $this->assertEquals(['filtersClass' => $filtersClass], $operation->getExtraProperties());
        $this->assertEquals($filtersClass, $operation->getFiltersClass());

        // Extra properties parameters in constructor
        $operation = new CQRSPaginate(
            extraProperties: ['filtersClass' => $filtersClass],
        );
        $this->assertEquals(['filtersClass' => $filtersClass], $operation->getExtraProperties());
        $this->assertEquals($filtersClass, $operation->getFiltersClass());

        // Extra properties AND CQRS query mapping parameters in constructor, both values are equals no problem
        $operation = new CQRSPaginate(
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
            new CQRSPaginate(
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
        $operation = new CQRSPaginate(
            filtersMapping: $filtersMapping,
        );

        $this->assertEquals(['filtersMapping' => $filtersMapping], $operation->getExtraProperties());
        $this->assertEquals($filtersMapping, $operation->getFiltersMapping());

        // Extra properties parameters in constructor
        $operation = new CQRSPaginate(
            extraProperties: ['filtersMapping' => $filtersMapping],
        );
        $this->assertEquals(['filtersMapping' => $filtersMapping], $operation->getExtraProperties());
        $this->assertEquals($filtersMapping, $operation->getFiltersMapping());

        // Extra properties AND CQRS query mapping parameters in constructor, both values are equals no problem
        $operation = new CQRSPaginate(
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
            new CQRSPaginate(
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
        $operation = new CQRSPaginate(
            scopes: ['test', 'test2']
        );
        $this->assertEquals(['scopes' => ['test', 'test2']], $operation->getExtraProperties());
        $this->assertEquals(['test', 'test2'], $operation->getScopes());

        // Extra properties parameters in constructor
        $operation = new CQRSPaginate(
            extraProperties: ['scopes' => ['test']]
        );
        $this->assertEquals(['scopes' => ['test']], $operation->getExtraProperties());
        $this->assertEquals(['test'], $operation->getScopes());

        // Extra properties AND scopes parameters in constructor, both values get merged but remain unique
        $operation = new CQRSPaginate(
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
        $operation = new CQRSPaginate(
            ApiResourceMapping: $resourceMapping,
        );

        $this->assertEquals(['ApiResourceMapping' => $resourceMapping], $operation->getExtraProperties());
        $this->assertEquals($resourceMapping, $operation->getApiResourceMapping());

        // Extra properties parameters in constructor
        $operation = new CQRSPaginate(
            extraProperties: ['ApiResourceMapping' => $resourceMapping],
        );
        $this->assertEquals(['ApiResourceMapping' => $resourceMapping], $operation->getExtraProperties());
        $this->assertEquals($resourceMapping, $operation->getApiResourceMapping());

        // Extra properties AND Api resource mapping parameters in constructor, both values are equals no problem
        $operation = new CQRSPaginate(
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
            new CQRSPaginate(
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

    public function testMultipleArguments(): void
    {
        $resourceMapping = ['[id]' => '[queryId]'];
        $queryMapping = ['[id]' => '[queryId]'];
        $operation = new CQRSPaginate(
            extraProperties: [
                'CQRSQuery' => 'My\\Namespace\\MyQuery',
                'scopes' => ['master_scope'],
                'countField' => 'count',
            ],
            scopes: ['scope1', 'scope2'],
            CQRSQueryMapping: $queryMapping,
            ApiResourceMapping: $resourceMapping,
            itemsField: 'items',
        );

        $this->assertEquals(QueryListProvider::class, $operation->getProvider());
        $this->assertEquals($resourceMapping, $operation->getApiResourceMapping());
        $this->assertEquals(['master_scope', 'scope1', 'scope2'], $operation->getScopes());
        $this->assertEquals('My\\Namespace\\MyQuery', $operation->getCQRSQuery());
        $this->assertEquals($queryMapping, $operation->getCQRSQueryMapping());
        $this->assertEquals('items', $operation->getItemsField());
        $this->assertEquals('count', $operation->getCountField());
        $this->assertEquals([
            'CQRSQuery' => 'My\\Namespace\\MyQuery',
            'scopes' => ['master_scope', 'scope1', 'scope2'],
            'CQRSQueryMapping' => $queryMapping,
            'ApiResourceMapping' => $resourceMapping,
            'itemsField' => 'items',
            'countField' => 'count',
        ], $operation->getExtraProperties());

        // Using with clones the object, only one extra parameter is modified
        $operation2 = $operation->withScopes(['scope3']);
        $operation3 = $operation2->withItemsField('combinations');
        $this->assertNotEquals($operation2, $operation);
        $this->assertNotEquals($operation2, $operation3);
        $this->assertNotEquals($operation3, $operation);

        // Check first clone operation2
        $this->assertEquals(QueryListProvider::class, $operation2->getProvider());
        $this->assertEquals($resourceMapping, $operation2->getApiResourceMapping());
        $this->assertEquals('My\\Namespace\\MyQuery', $operation->getCQRSQuery());
        $this->assertEquals($queryMapping, $operation2->getCQRSQueryMapping());
        $this->assertEquals('items', $operation2->getItemsField());
        $this->assertEquals('count', $operation2->getCountField());
        $this->assertEquals(['scope3'], $operation2->getScopes());
        $this->assertEquals([
            'CQRSQuery' => 'My\\Namespace\\MyQuery',
            'scopes' => ['scope3'],
            'CQRSQueryMapping' => $queryMapping,
            'ApiResourceMapping' => $resourceMapping,
            'itemsField' => 'items',
            'countField' => 'count',
        ], $operation2->getExtraProperties());

        // Check second clone operation3
        $this->assertEquals(QueryListProvider::class, $operation3->getProvider());
        $this->assertEquals($resourceMapping, $operation3->getApiResourceMapping());
        $this->assertEquals($queryMapping, $operation3->getCQRSQueryMapping());
        $this->assertEquals('combinations', $operation3->getItemsField());
        $this->assertEquals('count', $operation3->getCountField());
        $this->assertEquals(['scope3'], $operation3->getScopes());
        $this->assertEquals([
            'CQRSQuery' => 'My\\Namespace\\MyQuery',
            'scopes' => ['scope3'],
            'CQRSQueryMapping' => $queryMapping,
            'ApiResourceMapping' => $resourceMapping,
            'itemsField' => 'combinations',
            'countField' => 'count',
        ], $operation3->getExtraProperties());

        // The original object has not been modified
        $this->assertEquals(QueryListProvider::class, $operation->getProvider());
        $this->assertEquals($resourceMapping, $operation->getApiResourceMapping());
        $this->assertEquals(['master_scope', 'scope1', 'scope2'], $operation->getScopes());
        $this->assertEquals('My\\Namespace\\MyQuery', $operation->getCQRSQuery());
        $this->assertEquals($queryMapping, $operation->getCQRSQueryMapping());
        $this->assertEquals('items', $operation->getItemsField());
        $this->assertEquals('count', $operation->getCountField());
        $this->assertEquals([
            'CQRSQuery' => 'My\\Namespace\\MyQuery',
            'scopes' => ['master_scope', 'scope1', 'scope2'],
            'CQRSQueryMapping' => $queryMapping,
            'ApiResourceMapping' => $resourceMapping,
            'itemsField' => 'items',
            'countField' => 'count',
        ], $operation->getExtraProperties());
    }

    public function testExperimentalOperation(): void
    {
        // Default value is false (no extra property added)
        $operation = new CQRSPaginate();
        $this->assertEquals([], $operation->getExtraProperties());
        $this->assertEquals(false, $operation->getExperimentalOperation());

        // Scopes parameters in constructor
        $operation = new CQRSPaginate(
            experimentalOperation: true,
        );
        $this->assertEquals(['experimentalOperation' => true], $operation->getExtraProperties());
        $this->assertEquals(true, $operation->getExperimentalOperation());

        // Extra properties parameters in constructor
        $operation = new CQRSPaginate(
            extraProperties: ['experimentalOperation' => false]
        );
        $this->assertEquals(['experimentalOperation' => false], $operation->getExtraProperties());
        $this->assertEquals(false, $operation->getExperimentalOperation());

        // Extra properties AND scopes parameters in constructor, both values get merged but remain unique
        $operation = new CQRSPaginate(
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
            new CQRSPaginate(
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
