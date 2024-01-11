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
use PrestaShopBundle\ApiPlatform\Metadata\CQRSDelete;
use PrestaShopBundle\ApiPlatform\Provider\QueryProvider;

class CQRSDeleteTest extends TestCase
{
    public function testDefaultConstructor(): void
    {
        // Without any parameters
        $operation = new CQRSDelete();
        $this->assertEquals(QueryProvider::class, $operation->getProvider());
        $this->assertNull($operation->getProcessor());
        $this->assertEquals(CQRSDelete::METHOD_DELETE, $operation->getMethod());
        $this->assertEquals([], $operation->getExtraProperties());
        $this->assertFalse($operation->getOutput());

        // With positioned parameters
        $operation = new CQRSDelete('/uri');
        $this->assertEquals(QueryProvider::class, $operation->getProvider());
        $this->assertNull($operation->getProcessor());
        $this->assertEquals(CQRSDelete::METHOD_DELETE, $operation->getMethod());
        $this->assertEquals('/uri', $operation->getUriTemplate());
        $this->assertEquals([], $operation->getExtraProperties());
        $this->assertFalse($operation->getOutput());

        // With named parameters
        $operation = new CQRSDelete(
            output: true,
            extraProperties: ['scopes' => ['test']],
        );
        $this->assertEquals(QueryProvider::class, $operation->getProvider());
        $this->assertNull($operation->getProcessor());
        $this->assertEquals(['scopes' => ['test']], $operation->getExtraProperties());
        $this->assertTrue($operation->getOutput());
    }

    public function testScopes(): void
    {
        // Scopes parameters in constructor
        $operation = new CQRSDelete(
            scopes: ['test', 'test2']
        );
        $this->assertEquals(['scopes' => ['test', 'test2']], $operation->getExtraProperties());
        $this->assertEquals(['test', 'test2'], $operation->getScopes());

        // Extra properties parameters in constructor
        $operation = new CQRSDelete(
            extraProperties: ['scopes' => ['test']]
        );
        $this->assertEquals(['scopes' => ['test']], $operation->getExtraProperties());
        $this->assertEquals(['test'], $operation->getScopes());

        // Extra properties AND scopes parameters in constructor, both values get merged but remain unique
        $operation = new CQRSDelete(
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

    public function testCQRSQuery(): void
    {
        // CQRS query parameters in constructor
        $operation = new CQRSDelete(
            CQRSQuery: 'My\\Namespace\\MyQuery',
        );
        $this->assertEquals(QueryProvider::class, $operation->getProvider());
        $this->assertNull($operation->getProcessor());
        $this->assertEquals(['CQRSQuery' => 'My\\Namespace\\MyQuery'], $operation->getExtraProperties());
        $this->assertEquals('My\\Namespace\\MyQuery', $operation->getCQRSQuery());

        // Extra properties parameters in constructor
        $operation = new CQRSDelete(
            extraProperties: ['CQRSQuery' => 'My\\Namespace\\MyQuery'],
        );
        $this->assertEquals(QueryProvider::class, $operation->getProvider());
        $this->assertNull($operation->getProcessor());
        $this->assertEquals(['CQRSQuery' => 'My\\Namespace\\MyQuery'], $operation->getExtraProperties());
        $this->assertEquals('My\\Namespace\\MyQuery', $operation->getCQRSQuery());

        // Extra properties AND CQRS query parameters in constructor, both values are equals no problem
        $operation = new CQRSDelete(
            extraProperties: ['CQRSQuery' => 'My\\Namespace\\MyQuery'],
            CQRSQuery: 'My\\Namespace\\MyQuery',
        );
        $this->assertEquals(QueryProvider::class, $operation->getProvider());
        $this->assertNull($operation->getProcessor());
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

        // New operation without query, the provider is forced when it is set
        $operation = new CQRSDelete();
        $this->assertEquals(QueryProvider::class, $operation->getProvider());
        $this->assertNull($operation->getProcessor());
        $this->assertArrayNotHasKey('CQRSQuery', $operation->getExtraProperties());
        $this->assertNull($operation->getCQRSQuery());

        $operation3 = $operation->withCQRSQuery('My\\Namespace\\MyQuery');
        $this->assertEquals(QueryProvider::class, $operation3->getProvider());
        $this->assertNull($operation3->getProcessor());
        $this->assertEquals(['CQRSQuery' => 'My\\Namespace\\MyQuery'], $operation3->getExtraProperties());
        $this->assertEquals('My\\Namespace\\MyQuery', $operation3->getCQRSQuery());
        // And initial operation as not modified of course
        $this->assertEquals(QueryProvider::class, $operation->getProvider());
        $this->assertNull($operation->getProcessor());
        $this->assertArrayNotHasKey('CQRSQuery', $operation->getExtraProperties());
        $this->assertNull($operation->getCQRSQuery());

        // When both values are specified, but they are different trigger an exception
        $caughtException = null;
        try {
            new CQRSDelete(
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
        $operation = new CQRSDelete(
            CQRSQueryMapping: $queryMapping,
        );

        $this->assertEquals(['CQRSQueryMapping' => $queryMapping], $operation->getExtraProperties());
        $this->assertEquals($queryMapping, $operation->getCQRSQueryMapping());

        // Extra properties parameters in constructor
        $operation = new CQRSDelete(
            extraProperties: ['CQRSQueryMapping' => $queryMapping],
        );
        $this->assertEquals(['CQRSQueryMapping' => $queryMapping], $operation->getExtraProperties());
        $this->assertEquals($queryMapping, $operation->getCQRSQueryMapping());

        // Extra properties AND CQRS query mapping parameters in constructor, both values are equals no problem
        $operation = new CQRSDelete(
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
            new CQRSDelete(
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

    public function testApiResourceMapping(): void
    {
        // Api resource mapping parameters in constructor
        $resourceMapping = ['[id]' => '[queryId]'];
        $operation = new CQRSDelete(
            ApiResourceMapping: $resourceMapping,
        );

        $this->assertEquals(['ApiResourceMapping' => $resourceMapping], $operation->getExtraProperties());
        $this->assertEquals($resourceMapping, $operation->getApiResourceMapping());

        // Extra properties parameters in constructor
        $operation = new CQRSDelete(
            extraProperties: ['ApiResourceMapping' => $resourceMapping],
        );
        $this->assertEquals(['ApiResourceMapping' => $resourceMapping], $operation->getExtraProperties());
        $this->assertEquals($resourceMapping, $operation->getApiResourceMapping());

        // Extra properties AND Api resource mapping parameters in constructor, both values are equals no problem
        $operation = new CQRSDelete(
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
            new CQRSDelete(
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
}
