<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Security\Attribute;

use PHPUnit\Framework\TestCase;
use RuntimeException;

class AdminSecurityTest extends TestCase
{
    public function testConstructorWithAnnotationStyle(): void
    {
        $adminSecurity = new AdminSecurity(
            [
                'value' => 'testValue',
                'message' => 'testMessage',
                'domain' => 'testDomain',
                'url' => 'testUrl',
                'redirectQueryParamsToKeep' => ['testRedirectQueryParamsToKeep'],
                'statusCode' => 1,
                'exceptionCode' => 2,
                'redirectRoute' => 'testRedirectRoute',
            ]
        );

        self::assertEquals('testValue', $adminSecurity->getAttribute());
        self::assertEquals('testMessage', $adminSecurity->getMessage());
        self::assertEquals('testDomain', $adminSecurity->getDomain());
        self::assertEquals('testUrl', $adminSecurity->getUrl());
        self::assertEquals(['testRedirectQueryParamsToKeep'], $adminSecurity->getRedirectQueryParamsToKeep());
        self::assertEquals(1, $adminSecurity->getStatusCode());
        self::assertEquals(2, $adminSecurity->getExceptionCode());
        self::assertEquals('testRedirectRoute', $adminSecurity->getRedirectRoute());
    }

    public function testConstructorWithAttributeStyle(): void
    {
        $adminSecurity = new AdminSecurity(
            'testValue',
            message: 'testMessage',
            domain: 'testDomain',
            url: 'testUrl',
            redirectQueryParamsToKeep: ['testRedirectQueryParamsToKeep'],
            statusCode: 1,
            exceptionCode: 2,
            redirectRoute: 'testRedirectRoute',
        );

        self::assertEquals('testValue', $adminSecurity->getAttribute());
        self::assertEquals('testMessage', $adminSecurity->getMessage());
        self::assertEquals('testDomain', $adminSecurity->getDomain());
        self::assertEquals('testUrl', $adminSecurity->getUrl());
        self::assertEquals(['testRedirectQueryParamsToKeep'], $adminSecurity->getRedirectQueryParamsToKeep());
        self::assertEquals(1, $adminSecurity->getStatusCode());
        self::assertEquals(2, $adminSecurity->getExceptionCode());
        self::assertEquals('testRedirectRoute', $adminSecurity->getRedirectRoute());
    }

    public function testInvalidData(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unknown key "fake" for annotation "@PrestaShopBundle\Security\Attribute\AdminSecurity"');

        new AdminSecurity(
            [
                'value' => 'testValue',
                'message' => 'testMessage',
                'domain' => 'testDomain',
                'url' => 'testUrl',
                'redirectQueryParamsToKeep' => ['testRedirectQueryParamsToKeep'],
                'statusCode' => 1,
                'exceptionCode' => 2,
                'redirectRoute' => 'testRedirectRoute',
                'fake' => 'testFake',
            ]
        );
    }
}
