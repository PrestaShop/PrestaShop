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
use PrestaShopBundle\ApiPlatform\Metadata\CQRSCommand;
use PrestaShopBundle\ApiPlatform\Processor\CommandProcessor;
use PrestaShopBundle\ApiPlatform\Provider\QueryProvider;

class CQRSCommandTest extends TestCase
{
    public function testDefaultConstructor(): void
    {
        // Without any parameters
        $operation = new CQRSCommand();
        $this->assertEquals(CommandProcessor::class, $operation->getProcessor());
        $this->assertNull($operation->getProvider());
        $this->assertEquals(CQRSCommand::METHOD_POST, $operation->getMethod());
        $this->assertEquals([], $operation->getExtraProperties());
        $this->assertEquals(['json'], $operation->getFormats());

        // With positioned parameters
        $operation = new CQRSCommand(CQRSCommand::METHOD_PUT, '/uri');
        $this->assertEquals(CommandProcessor::class, $operation->getProcessor());
        $this->assertNull($operation->getProvider());
        $this->assertEquals(CQRSCommand::METHOD_PUT, $operation->getMethod());
        $this->assertEquals('/uri', $operation->getUriTemplate());
        $this->assertEquals([], $operation->getExtraProperties());
        $this->assertEquals(['json'], $operation->getFormats());

        // With named parameters
        $operation = new CQRSCommand(
            formats: ['json', 'html'],
            extraProperties: ['scopes' => ['test']]
        );
        $this->assertEquals(CommandProcessor::class, $operation->getProcessor());
        $this->assertNull($operation->getProvider());
        $this->assertEquals(['scopes' => ['test']], $operation->getExtraProperties());
        $this->assertEquals(['json', 'html'], $operation->getFormats());
    }

    public function testScopes(): void
    {
        // Scopes parameters in constructor
        $operation = new CQRSCommand(
            scopes: ['test', 'test2']
        );
        $this->assertEquals(['scopes' => ['test', 'test2']], $operation->getExtraProperties());
        $this->assertEquals(['test', 'test2'], $operation->getScopes());

        // Extra properties parameters in constructor
        $operation = new CQRSCommand(
            extraProperties: ['scopes' => ['test']]
        );
        $this->assertEquals(['scopes' => ['test']], $operation->getExtraProperties());
        $this->assertEquals(['test'], $operation->getScopes());

        // Extra properties AND scopes parameters in constructor, both values get merged but remain unique
        $operation = new CQRSCommand(
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

    public function testCQRSCommand(): void
    {
        // CQRS query parameters in constructor
        $operation = new CQRSCommand(
            CQRSCommand: 'My\\Namespace\\MyCommand',
        );
        $this->assertEquals(CommandProcessor::class, $operation->getProcessor());
        $this->assertNull($operation->getProvider());
        $this->assertEquals(['CQRSCommand' => 'My\\Namespace\\MyCommand'], $operation->getExtraProperties());
        $this->assertEquals('My\\Namespace\\MyCommand', $operation->getCQRSCommand());

        // Extra properties parameters in constructor
        $operation = new CQRSCommand(
            extraProperties: ['CQRSCommand' => 'My\\Namespace\\MyCommand'],
        );
        $this->assertEquals(['CQRSCommand' => 'My\\Namespace\\MyCommand'], $operation->getExtraProperties());
        $this->assertEquals('My\\Namespace\\MyCommand', $operation->getCQRSCommand());

        // Extra properties AND CQRS query parameters in constructor, both values are equals no problem
        $operation = new CQRSCommand(
            extraProperties: ['CQRSCommand' => 'My\\Namespace\\MyCommand'],
            CQRSCommand: 'My\\Namespace\\MyCommand',
        );
        $this->assertEquals(['CQRSCommand' => 'My\\Namespace\\MyCommand'], $operation->getExtraProperties());
        $this->assertEquals('My\\Namespace\\MyCommand', $operation->getCQRSCommand());

        // Use with method, returned object is a clone All values are replaced
        $operation2 = $operation->withCQRSCommand('My\\Namespace\\MyOtherCommand');
        $this->assertNotEquals($operation2, $operation);
        $this->assertEquals(['CQRSCommand' => 'My\\Namespace\\MyOtherCommand'], $operation2->getExtraProperties());
        $this->assertEquals('My\\Namespace\\MyOtherCommand', $operation2->getCQRSCommand());
        // Initial operation not modified of course
        $this->assertEquals(['CQRSCommand' => 'My\\Namespace\\MyCommand'], $operation->getExtraProperties());
        $this->assertEquals('My\\Namespace\\MyCommand', $operation->getCQRSCommand());

        // When both values are specified, but they are different trigger an exception
        $caughtException = null;
        try {
            new CQRSCommand(
                extraProperties: ['CQRSCommand' => 'My\\Namespace\\MyCommand'],
                CQRSCommand: 'My\\Namespace\\MyOtherCommand',
            );
        } catch (InvalidArgumentException $e) {
            $caughtException = $e;
        }

        $this->assertNotNull($caughtException);
        $this->assertInstanceOf(InvalidArgumentException::class, $caughtException);
        $this->assertEquals('Specifying an extra property CQRSCommand and a CQRSCommand argument that are different is invalid', $caughtException->getMessage());
    }

    public function testCQRSCommandMapping(): void
    {
        // CQRS query mapping parameters in constructor
        $commandMapping = ['[id]' => '[queryId]'];
        $operation = new CQRSCommand(
            CQRSCommandMapping: $commandMapping,
        );

        $this->assertEquals(['CQRSCommandMapping' => $commandMapping], $operation->getExtraProperties());
        $this->assertEquals($commandMapping, $operation->getCQRSCommandMapping());

        // Extra properties parameters in constructor
        $operation = new CQRSCommand(
            extraProperties: ['CQRSCommandMapping' => $commandMapping],
        );
        $this->assertEquals(['CQRSCommandMapping' => $commandMapping], $operation->getExtraProperties());
        $this->assertEquals($commandMapping, $operation->getCQRSCommandMapping());

        // Extra properties AND CQRS query mapping parameters in constructor, both values are equals no problem
        $operation = new CQRSCommand(
            extraProperties: ['CQRSCommandMapping' => $commandMapping],
            CQRSCommandMapping: $commandMapping,
        );
        $this->assertEquals(['CQRSCommandMapping' => $commandMapping], $operation->getExtraProperties());
        $this->assertEquals($commandMapping, $operation->getCQRSCommandMapping());

        // Use with method, returned object is a clone All values are replaced
        $newCommandMapping = ['[queryId' => '[valueObjectId]'];
        $operation2 = $operation->withCQRSCommandMapping($newCommandMapping);
        $this->assertNotEquals($operation2, $operation);
        $this->assertEquals(['CQRSCommandMapping' => $newCommandMapping], $operation2->getExtraProperties());
        $this->assertEquals($newCommandMapping, $operation2->getCQRSCommandMapping());
        // Initial operation not modified of course
        $this->assertEquals(['CQRSCommandMapping' => $commandMapping], $operation->getExtraProperties());
        $this->assertEquals($commandMapping, $operation->getCQRSCommandMapping());

        // When both values are specified, but they are different trigger an exception
        $caughtException = null;
        try {
            new CQRSCommand(
                extraProperties: ['CQRSCommandMapping' => $commandMapping],
                CQRSCommandMapping: $newCommandMapping,
            );
        } catch (InvalidArgumentException $e) {
            $caughtException = $e;
        }

        $this->assertNotNull($caughtException);
        $this->assertInstanceOf(InvalidArgumentException::class, $caughtException);
        $this->assertEquals('Specifying an extra property CQRSCommandMapping and a CQRSCommandMapping argument that are different is invalid', $caughtException->getMessage());
    }

    public function testCQRSQuery(): void
    {
        // CQRS query parameters in constructor
        $operation = new CQRSCommand(
            CQRSQuery: 'My\\Namespace\\MyQuery',
        );
        $this->assertEquals(CommandProcessor::class, $operation->getProcessor());
        $this->assertEquals(QueryProvider::class, $operation->getProvider());
        $this->assertEquals(['CQRSQuery' => 'My\\Namespace\\MyQuery'], $operation->getExtraProperties());
        $this->assertEquals('My\\Namespace\\MyQuery', $operation->getCQRSQuery());

        // Extra properties parameters in constructor
        $operation = new CQRSCommand(
            extraProperties: ['CQRSQuery' => 'My\\Namespace\\MyQuery'],
        );
        $this->assertEquals(CommandProcessor::class, $operation->getProcessor());
        $this->assertEquals(QueryProvider::class, $operation->getProvider());
        $this->assertEquals(['CQRSQuery' => 'My\\Namespace\\MyQuery'], $operation->getExtraProperties());
        $this->assertEquals('My\\Namespace\\MyQuery', $operation->getCQRSQuery());

        // Extra properties AND CQRS query parameters in constructor, both values are equals no problem
        $operation = new CQRSCommand(
            extraProperties: ['CQRSQuery' => 'My\\Namespace\\MyQuery'],
            CQRSQuery: 'My\\Namespace\\MyQuery',
        );
        $this->assertEquals(CommandProcessor::class, $operation->getProcessor());
        $this->assertEquals(QueryProvider::class, $operation->getProvider());
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
        $operation = new CQRSCommand();
        $this->assertEquals(CommandProcessor::class, $operation->getProcessor());
        $this->assertNull($operation->getProvider());
        $this->assertArrayNotHasKey('CQRSQuery', $operation->getExtraProperties());
        $this->assertNull($operation->getCQRSQuery());

        $operation3 = $operation->withCQRSQuery('My\\Namespace\\MyQuery');
        $this->assertEquals(CommandProcessor::class, $operation3->getProcessor());
        $this->assertEquals(QueryProvider::class, $operation3->getProvider());
        $this->assertEquals(['CQRSQuery' => 'My\\Namespace\\MyQuery'], $operation3->getExtraProperties());
        $this->assertEquals('My\\Namespace\\MyQuery', $operation3->getCQRSQuery());
        // And initial operation as not modified of course
        $this->assertEquals(CommandProcessor::class, $operation->getProcessor());
        $this->assertNull($operation->getProvider());
        $this->assertArrayNotHasKey('CQRSQuery', $operation->getExtraProperties());
        $this->assertNull($operation->getCQRSQuery());

        // When both values are specified, but they are different trigger an exception
        $caughtException = null;
        try {
            new CQRSCommand(
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
        $operation = new CQRSCommand(
            CQRSQueryMapping: $queryMapping,
        );

        $this->assertEquals(['CQRSQueryMapping' => $queryMapping], $operation->getExtraProperties());
        $this->assertEquals($queryMapping, $operation->getCQRSQueryMapping());

        // Extra properties parameters in constructor
        $operation = new CQRSCommand(
            extraProperties: ['CQRSQueryMapping' => $queryMapping],
        );
        $this->assertEquals(['CQRSQueryMapping' => $queryMapping], $operation->getExtraProperties());
        $this->assertEquals($queryMapping, $operation->getCQRSQueryMapping());

        // Extra properties AND CQRS query mapping parameters in constructor, both values are equals no problem
        $operation = new CQRSCommand(
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
            new CQRSCommand(
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
        $operation = new CQRSCommand(
            ApiResourceMapping: $resourceMapping,
        );

        $this->assertEquals(['ApiResourceMapping' => $resourceMapping], $operation->getExtraProperties());
        $this->assertEquals($resourceMapping, $operation->getApiResourceMapping());

        // Extra properties parameters in constructor
        $operation = new CQRSCommand(
            extraProperties: ['ApiResourceMapping' => $resourceMapping],
        );
        $this->assertEquals(['ApiResourceMapping' => $resourceMapping], $operation->getExtraProperties());
        $this->assertEquals($resourceMapping, $operation->getApiResourceMapping());

        // Extra properties AND Api resource mapping parameters in constructor, both values are equals no problem
        $operation = new CQRSCommand(
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
            new CQRSCommand(
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
        $commandMapping = ['[uriId]' => ['commandId']];
        $operation = new CQRSCommand(
            extraProperties: [
                'CQRSQuery' => 'My\\Namespace\\MyQuery',
                'scopes' => ['master_scope'],
                'CQRSCommandMapping' => $commandMapping,
            ],
            CQRSCommand: 'My\\Namespace\\MyCommand',
            scopes: ['scope1', 'scope2'],
            CQRSQueryMapping: $queryMapping,
            ApiResourceMapping: $resourceMapping,
        );

        $this->assertEquals(CommandProcessor::class, $operation->getProcessor());
        $this->assertEquals(QueryProvider::class, $operation->getProvider());
        $this->assertEquals('My\\Namespace\\MyQuery', $operation->getCQRSQuery());
        $this->assertEquals('My\\Namespace\\MyCommand', $operation->getCQRSCommand());
        $this->assertEquals($commandMapping, $operation->getCQRSCommandMapping());
        $this->assertEquals($queryMapping, $operation->getCQRSQueryMapping());
        $this->assertEquals($resourceMapping, $operation->getApiResourceMapping());
        $this->assertEquals(['master_scope', 'scope1', 'scope2'], $operation->getScopes());
        $this->assertEquals([
            'CQRSQuery' => 'My\\Namespace\\MyQuery',
            'CQRSCommand' => 'My\\Namespace\\MyCommand',
            'scopes' => ['master_scope', 'scope1', 'scope2'],
            'CQRSQueryMapping' => $queryMapping,
            'ApiResourceMapping' => $resourceMapping,
            'CQRSCommandMapping' => $commandMapping,
        ], $operation->getExtraProperties());

        // Using with clones the object, only one extra parameter is modified
        $operation2 = $operation->withCQRSCommand('My\\Namespace\\MyNewCommand');
        $operation3 = $operation2->withScopes(['scope3']);
        $this->assertNotEquals($operation2, $operation);
        $this->assertNotEquals($operation2, $operation3);
        $this->assertNotEquals($operation3, $operation);

        // Check first clone operation2
        $this->assertEquals(CommandProcessor::class, $operation2->getProcessor());
        $this->assertEquals(QueryProvider::class, $operation2->getProvider());
        $this->assertEquals('My\\Namespace\\MyQuery', $operation2->getCQRSQuery());
        $this->assertEquals('My\\Namespace\\MyNewCommand', $operation2->getCQRSCommand());
        $this->assertEquals($commandMapping, $operation2->getCQRSCommandMapping());
        $this->assertEquals($queryMapping, $operation2->getCQRSQueryMapping());
        $this->assertEquals($resourceMapping, $operation2->getApiResourceMapping());
        $this->assertEquals(['master_scope', 'scope1', 'scope2'], $operation2->getScopes());
        $this->assertEquals([
            'CQRSQuery' => 'My\\Namespace\\MyQuery',
            'CQRSCommand' => 'My\\Namespace\\MyNewCommand',
            'scopes' => ['master_scope', 'scope1', 'scope2'],
            'CQRSQueryMapping' => $queryMapping,
            'ApiResourceMapping' => $resourceMapping,
            'CQRSCommandMapping' => $commandMapping,
        ], $operation2->getExtraProperties());

        // Check second clone operation3
        $this->assertEquals(CommandProcessor::class, $operation3->getProcessor());
        $this->assertEquals(QueryProvider::class, $operation3->getProvider());
        $this->assertEquals('My\\Namespace\\MyQuery', $operation3->getCQRSQuery());
        $this->assertEquals('My\\Namespace\\MyNewCommand', $operation3->getCQRSCommand());
        $this->assertEquals($commandMapping, $operation3->getCQRSCommandMapping());
        $this->assertEquals($queryMapping, $operation3->getCQRSQueryMapping());
        $this->assertEquals($resourceMapping, $operation3->getApiResourceMapping());
        $this->assertEquals(['scope3'], $operation3->getScopes());
        $this->assertEquals([
            'CQRSQuery' => 'My\\Namespace\\MyQuery',
            'CQRSCommand' => 'My\\Namespace\\MyNewCommand',
            'scopes' => ['scope3'],
            'CQRSQueryMapping' => $queryMapping,
            'ApiResourceMapping' => $resourceMapping,
            'CQRSCommandMapping' => $commandMapping,
        ], $operation3->getExtraProperties());

        // The original object has not been modified
        $this->assertEquals(QueryProvider::class, $operation->getProvider());
        $this->assertEquals('My\\Namespace\\MyQuery', $operation->getCQRSQuery());
        $this->assertEquals('My\\Namespace\\MyCommand', $operation->getCQRSCommand());
        $this->assertEquals($commandMapping, $operation->getCQRSCommandMapping());
        $this->assertEquals($queryMapping, $operation->getCQRSQueryMapping());
        $this->assertEquals($resourceMapping, $operation->getApiResourceMapping());
        $this->assertEquals(['master_scope', 'scope1', 'scope2'], $operation->getScopes());
        $this->assertEquals([
            'CQRSQuery' => 'My\\Namespace\\MyQuery',
            'CQRSCommand' => 'My\\Namespace\\MyCommand',
            'scopes' => ['master_scope', 'scope1', 'scope2'],
            'CQRSQueryMapping' => $queryMapping,
            'ApiResourceMapping' => $resourceMapping,
            'CQRSCommandMapping' => $commandMapping,
        ], $operation->getExtraProperties());
    }

    public function testExperimentalOperation(): void
    {
        // Default value is false (no extra property added)
        $operation = new CQRSCommand();
        $this->assertEquals([], $operation->getExtraProperties());
        $this->assertEquals(false, $operation->getExperimentalOperation());

        // Scopes parameters in constructor
        $operation = new CQRSCommand(
            experimentalOperation: true,
        );
        $this->assertEquals(['experimentalOperation' => true], $operation->getExtraProperties());
        $this->assertEquals(true, $operation->getExperimentalOperation());

        // Extra properties parameters in constructor
        $operation = new CQRSCommand(
            extraProperties: ['experimentalOperation' => false]
        );
        $this->assertEquals(['experimentalOperation' => false], $operation->getExtraProperties());
        $this->assertEquals(false, $operation->getExperimentalOperation());

        // Extra properties AND scopes parameters in constructor, both values get merged but remain unique
        $operation = new CQRSCommand(
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
            new CQRSCommand(
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
