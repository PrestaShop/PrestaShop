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
