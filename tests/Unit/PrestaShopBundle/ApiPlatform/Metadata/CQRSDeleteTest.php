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
use PrestaShopBundle\ApiPlatform\Processor\CommandProcessor;

class CQRSDeleteTest extends TestCase
{
    public function testDefaultConstructor(): void
    {
        // Without any parameters
        $operation = new CQRSDelete();
        $this->assertEquals(CommandProcessor::class, $operation->getProcessor());
        $this->assertNull($operation->getProvider());
        $this->assertEquals(CQRSDelete::METHOD_DELETE, $operation->getMethod());
        $this->assertEquals(['allowEmptyBody' => true], $operation->getExtraProperties());
        $this->assertFalse($operation->getOutput());
        $this->assertEquals(['json'], $operation->getFormats());
        // ReadListener disabled to avoid 404
        $this->assertFalse($operation->canRead());
        // DeserializeListener forced to tru (unlike POST, PUT and PATCH that are automatically deserialized)
        $this->assertTrue($operation->canDeserialize());
        // By default empty body is allowed
        $this->assertTrue($operation->getAllowEmptyBody());

        // With positioned parameters
        $operation = new CQRSDelete('/uri');
        $this->assertEquals(CommandProcessor::class, $operation->getProcessor());
        $this->assertNull($operation->getProvider());
        $this->assertEquals(CQRSDelete::METHOD_DELETE, $operation->getMethod());
        $this->assertEquals('/uri', $operation->getUriTemplate());
        $this->assertEquals(['allowEmptyBody' => true], $operation->getExtraProperties());
        $this->assertFalse($operation->getOutput());
        $this->assertEquals(['json'], $operation->getFormats());
        $this->assertFalse($operation->canRead());
        $this->assertTrue($operation->canDeserialize());
        $this->assertTrue($operation->getAllowEmptyBody());

        // With named parameters
        $operation = new CQRSDelete(
            formats: ['json', 'html'],
            output: 'test',
            extraProperties: ['scopes' => ['test']],
        );
        $this->assertEquals(CommandProcessor::class, $operation->getProcessor());
        $this->assertNull($operation->getProvider());
        $this->assertEquals(['scopes' => ['test'], 'allowEmptyBody' => true], $operation->getExtraProperties());
        $this->assertEquals('test', $operation->getOutput());
        $this->assertEquals(['json', 'html'], $operation->getFormats());
        $this->assertFalse($operation->canRead());
        $this->assertTrue($operation->canDeserialize());
        $this->assertTrue($operation->getAllowEmptyBody());
    }

    public function testScopes(): void
    {
        // Scopes parameters in constructor
        $operation = new CQRSDelete(
            scopes: ['test', 'test2']
        );
        $this->assertEquals(['scopes' => ['test', 'test2'], 'allowEmptyBody' => true], $operation->getExtraProperties());
        $this->assertEquals(['test', 'test2'], $operation->getScopes());

        // Extra properties parameters in constructor
        $operation = new CQRSDelete(
            extraProperties: ['scopes' => ['test']]
        );
        $this->assertEquals(['scopes' => ['test'], 'allowEmptyBody' => true], $operation->getExtraProperties());
        $this->assertEquals(['test'], $operation->getScopes());

        // Extra properties AND scopes parameters in constructor, both values get merged but remain unique
        $operation = new CQRSDelete(
            extraProperties: ['scopes' => ['test', 'test1']],
            scopes: ['test', 'test2'],
        );
        $this->assertEquals(['scopes' => ['test', 'test1', 'test2'], 'allowEmptyBody' => true], $operation->getExtraProperties());
        $this->assertEquals(['test', 'test1', 'test2'], $operation->getScopes());

        // Use with method, returned object is a clone All values are replaced
        $operation2 = $operation->withScopes(['test3']);
        $this->assertNotEquals($operation2, $operation);
        $this->assertEquals(['scopes' => ['test3'], 'allowEmptyBody' => true], $operation2->getExtraProperties());
        $this->assertEquals(['test3'], $operation2->getScopes());
        // Initial operation not modified of course
        $this->assertEquals(['scopes' => ['test', 'test1', 'test2'], 'allowEmptyBody' => true], $operation->getExtraProperties());
        $this->assertEquals(['test', 'test1', 'test2'], $operation->getScopes());
    }

    public function testCQRSCommand(): void
    {
        // CQRS query parameters in constructor
        $operation = new CQRSDelete(
            CQRSCommand: 'My\\Namespace\\MyQuery',
        );
        $this->assertEquals(CommandProcessor::class, $operation->getProcessor());
        $this->assertEquals(null, $operation->getProvider());
        $this->assertEquals(['CQRSCommand' => 'My\\Namespace\\MyQuery', 'allowEmptyBody' => true], $operation->getExtraProperties());
        $this->assertEquals('My\\Namespace\\MyQuery', $operation->getCQRSCommand());

        // Extra properties parameters in constructor
        $operation = new CQRSDelete(
            extraProperties: ['CQRSCommand' => 'My\\Namespace\\MyQuery'],
        );
        $this->assertEquals(CommandProcessor::class, $operation->getProcessor());
        $this->assertEquals(null, $operation->getProvider());
        $this->assertEquals(['CQRSCommand' => 'My\\Namespace\\MyQuery', 'allowEmptyBody' => true], $operation->getExtraProperties());
        $this->assertEquals('My\\Namespace\\MyQuery', $operation->getCQRSCommand());

        // Extra properties AND CQRS query parameters in constructor, both values are equals no problem
        $operation = new CQRSDelete(
            extraProperties: ['CQRSCommand' => 'My\\Namespace\\MyQuery'],
            CQRSCommand: 'My\\Namespace\\MyQuery',
        );
        $this->assertEquals(CommandProcessor::class, $operation->getProcessor());
        $this->assertEquals(null, $operation->getProvider());
        $this->assertEquals(['CQRSCommand' => 'My\\Namespace\\MyQuery', 'allowEmptyBody' => true], $operation->getExtraProperties());
        $this->assertEquals('My\\Namespace\\MyQuery', $operation->getCQRSCommand());

        // Use with method, returned object is a clone All values are replaced
        $operation2 = $operation->withCQRSCommand('My\\Namespace\\MyOtherQuery');
        $this->assertNotEquals($operation2, $operation);
        $this->assertEquals(['CQRSCommand' => 'My\\Namespace\\MyOtherQuery', 'allowEmptyBody' => true], $operation2->getExtraProperties());
        $this->assertEquals('My\\Namespace\\MyOtherQuery', $operation2->getCQRSCommand());
        // Initial operation not modified of course
        $this->assertEquals(['CQRSCommand' => 'My\\Namespace\\MyQuery', 'allowEmptyBody' => true], $operation->getExtraProperties());
        $this->assertEquals('My\\Namespace\\MyQuery', $operation->getCQRSCommand());

        // New operation without query, the provider is forced when it is set
        $operation = new CQRSDelete();
        $this->assertEquals(CommandProcessor::class, $operation->getProcessor());
        $this->assertEquals(null, $operation->getProvider());
        $this->assertArrayNotHasKey('CQRSCommand', $operation->getExtraProperties());
        $this->assertNull($operation->getCQRSCommand());

        $operation3 = $operation->withCQRSCommand('My\\Namespace\\MyQuery');
        $this->assertEquals(CommandProcessor::class, $operation->getProcessor());
        $this->assertEquals(null, $operation->getProvider());
        $this->assertEquals(['CQRSCommand' => 'My\\Namespace\\MyQuery', 'allowEmptyBody' => true], $operation3->getExtraProperties());
        $this->assertEquals('My\\Namespace\\MyQuery', $operation3->getCQRSCommand());
        // And initial operation as not modified of course
        $this->assertEquals(CommandProcessor::class, $operation->getProcessor());
        $this->assertEquals(null, $operation->getProvider());
        $this->assertArrayNotHasKey('CQRSCommand', $operation->getExtraProperties());
        $this->assertNull($operation->getCQRSCommand());

        // When both values are specified, but they are different trigger an exception
        $caughtException = null;
        try {
            new CQRSDelete(
                extraProperties: ['CQRSCommand' => 'My\\Namespace\\MyQuery'],
                CQRSCommand: 'My\\Namespace\\MyOtherQuery',
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
        $queryMapping = ['[id]' => '[queryId]'];
        $operation = new CQRSDelete(
            CQRSCommandMapping: $queryMapping,
        );

        $this->assertEquals(['CQRSCommandMapping' => $queryMapping, 'allowEmptyBody' => true], $operation->getExtraProperties());
        $this->assertEquals($queryMapping, $operation->getCQRSCommandMapping());

        // Extra properties parameters in constructor
        $operation = new CQRSDelete(
            extraProperties: ['CQRSCommandMapping' => $queryMapping],
        );
        $this->assertEquals(['CQRSCommandMapping' => $queryMapping, 'allowEmptyBody' => true], $operation->getExtraProperties());
        $this->assertEquals($queryMapping, $operation->getCQRSCommandMapping());

        // Extra properties AND CQRS query mapping parameters in constructor, both values are equals no problem
        $operation = new CQRSDelete(
            extraProperties: ['CQRSCommandMapping' => $queryMapping],
            CQRSCommandMapping: $queryMapping,
        );
        $this->assertEquals(['CQRSCommandMapping' => $queryMapping, 'allowEmptyBody' => true], $operation->getExtraProperties());
        $this->assertEquals($queryMapping, $operation->getCQRSCommandMapping());

        // Use with method, returned object is a clone All values are replaced
        $newMapping = ['[queryId' => '[valueObjectId]'];
        $operation2 = $operation->withCQRSCommandMapping($newMapping);
        $this->assertNotEquals($operation2, $operation);
        $this->assertEquals(['CQRSCommandMapping' => $newMapping, 'allowEmptyBody' => true], $operation2->getExtraProperties());
        $this->assertEquals($newMapping, $operation2->getCQRSCommandMapping());
        // Initial operation not modified of course
        $this->assertEquals(['CQRSCommandMapping' => $queryMapping, 'allowEmptyBody' => true], $operation->getExtraProperties());
        $this->assertEquals($queryMapping, $operation->getCQRSCommandMapping());

        // When both values are specified, but they are different trigger an exception
        $caughtException = null;
        try {
            new CQRSDelete(
                extraProperties: ['CQRSCommandMapping' => $queryMapping],
                CQRSCommandMapping: $newMapping,
            );
        } catch (InvalidArgumentException $e) {
            $caughtException = $e;
        }

        $this->assertNotNull($caughtException);
        $this->assertInstanceOf(InvalidArgumentException::class, $caughtException);
        $this->assertEquals('Specifying an extra property CQRSCommandMapping and a CQRSCommandMapping argument that are different is invalid', $caughtException->getMessage());
    }

    public function testApiResourceMapping(): void
    {
        // Api resource mapping parameters in constructor
        $resourceMapping = ['[id]' => '[queryId]'];
        $operation = new CQRSDelete(
            ApiResourceMapping: $resourceMapping,
        );

        $this->assertEquals(['ApiResourceMapping' => $resourceMapping, 'allowEmptyBody' => true], $operation->getExtraProperties());
        $this->assertEquals($resourceMapping, $operation->getApiResourceMapping());

        // Extra properties parameters in constructor
        $operation = new CQRSDelete(
            extraProperties: ['ApiResourceMapping' => $resourceMapping],
        );
        $this->assertEquals(['ApiResourceMapping' => $resourceMapping, 'allowEmptyBody' => true], $operation->getExtraProperties());
        $this->assertEquals($resourceMapping, $operation->getApiResourceMapping());

        // Extra properties AND Api resource mapping parameters in constructor, both values are equals no problem
        $operation = new CQRSDelete(
            extraProperties: ['ApiResourceMapping' => $resourceMapping],
            ApiResourceMapping: $resourceMapping,
        );
        $this->assertEquals(['ApiResourceMapping' => $resourceMapping, 'allowEmptyBody' => true], $operation->getExtraProperties());
        $this->assertEquals($resourceMapping, $operation->getApiResourceMapping());

        // Use with method, returned object is a clone All values are replaced
        $newMapping = ['[queryId' => '[valueObjectId]'];
        $operation2 = $operation->withApiResourceMapping($newMapping);
        $this->assertNotEquals($operation2, $operation);
        $this->assertEquals(['ApiResourceMapping' => $newMapping, 'allowEmptyBody' => true], $operation2->getExtraProperties());
        $this->assertEquals($newMapping, $operation2->getApiResourceMapping());
        // Initial operation not modified of course
        $this->assertEquals(['ApiResourceMapping' => $resourceMapping, 'allowEmptyBody' => true], $operation->getExtraProperties());
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

    public function testExperimentalOperation(): void
    {
        // Default value is false (no extra property added)
        $operation = new CQRSDelete();
        $this->assertEquals(['allowEmptyBody' => true], $operation->getExtraProperties());
        $this->assertEquals(false, $operation->getExperimentalOperation());

        // Scopes parameters in constructor
        $operation = new CQRSDelete(
            experimentalOperation: true,
        );
        $this->assertEquals(['experimentalOperation' => true, 'allowEmptyBody' => true], $operation->getExtraProperties());
        $this->assertEquals(true, $operation->getExperimentalOperation());

        // Extra properties parameters in constructor
        $operation = new CQRSDelete(
            extraProperties: ['experimentalOperation' => false]
        );
        $this->assertEquals(['experimentalOperation' => false, 'allowEmptyBody' => true], $operation->getExtraProperties());
        $this->assertEquals(false, $operation->getExperimentalOperation());

        // Extra properties AND scopes parameters in constructor, both values get merged but remain unique
        $operation = new CQRSDelete(
            extraProperties: ['experimentalOperation' => true],
            experimentalOperation: true,
        );
        $this->assertEquals(['experimentalOperation' => true, 'allowEmptyBody' => true], $operation->getExtraProperties());
        $this->assertEquals(true, $operation->getExperimentalOperation());

        // Use with method, returned object is a clone All values are replaced
        $operation2 = $operation->withExperimentalOperation(false);
        $this->assertNotEquals($operation2, $operation);
        $this->assertEquals(['experimentalOperation' => false, 'allowEmptyBody' => true], $operation2->getExtraProperties());
        $this->assertEquals(false, $operation2->getExperimentalOperation());
        // Initial operation not modified of course
        $this->assertEquals(['experimentalOperation' => true, 'allowEmptyBody' => true], $operation->getExtraProperties());
        $this->assertEquals(true, $operation->getExperimentalOperation());

        // When both values are specified, but they are different trigger an exception
        $caughtException = null;
        try {
            new CQRSDelete(
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

    public function testAllowEmptyBody(): void
    {
        // Default value is true (special case for CQRSDelete)
        $operation = new CQRSDelete();
        $this->assertEquals(['allowEmptyBody' => true], $operation->getExtraProperties());
        $this->assertEquals(true, $operation->getAllowEmptyBody());

        // Scopes parameters in constructor
        $operation = new CQRSDelete(
            allowEmptyBody: true,
        );
        $this->assertEquals(['allowEmptyBody' => true], $operation->getExtraProperties());
        $this->assertEquals(true, $operation->getAllowEmptyBody());

        // Extra properties parameters in constructor
        $operation = new CQRSDelete(
            extraProperties: ['allowEmptyBody' => false]
        );
        $this->assertEquals(['allowEmptyBody' => false], $operation->getExtraProperties());
        $this->assertEquals(false, $operation->getAllowEmptyBody());

        // Extra properties AND scopes parameters in constructor, both values get merged but remain unique
        $operation = new CQRSDelete(
            extraProperties: ['allowEmptyBody' => true],
            allowEmptyBody: true,
        );
        $this->assertEquals(['allowEmptyBody' => true], $operation->getExtraProperties());
        $this->assertEquals(true, $operation->getAllowEmptyBody());

        // Use with method, returned object is a clone All values are replaced
        $operation2 = $operation->withallowEmptyBody(false);
        $this->assertNotEquals($operation2, $operation);
        $this->assertEquals(['allowEmptyBody' => false], $operation2->getExtraProperties());
        $this->assertEquals(false, $operation2->getAllowEmptyBody());
        // Initial operation not modified of course
        $this->assertEquals(['allowEmptyBody' => true], $operation->getExtraProperties());
        $this->assertEquals(true, $operation->getAllowEmptyBody());

        // When both values are specified, but they are different trigger an exception
        $caughtException = null;
        try {
            new CQRSDelete(
                extraProperties: ['allowEmptyBody' => true],
                allowEmptyBody: false,
            );
        } catch (InvalidArgumentException $e) {
            $caughtException = $e;
        }

        $this->assertNotNull($caughtException);
        $this->assertInstanceOf(InvalidArgumentException::class, $caughtException);
        $this->assertEquals('Specifying an extra property allowEmptyBody and a allowEmptyBody argument that are different is invalid', $caughtException->getMessage());
    }
}
