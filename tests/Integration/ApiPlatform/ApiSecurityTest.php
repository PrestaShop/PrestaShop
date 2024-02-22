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

namespace Tests\Integration\ApiPlatform;

use Tests\Integration\ApiPlatform\EndPoint\ApiTestCase;
use Tests\Resources\Resetter\ApiClientResetter;

class ApiSecurityTest extends ApiTestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        ApiClientResetter::resetApiClient();
    }

    public function testAuthenticationWithoutScopeNeededSuccess()
    {
        self::createApiClient();
        $bearerToken = $this->getBearerToken();
        $client = static::createClient();
        $response = $client->request('GET', '/api/test/unscoped/product/1', [
            'headers' => [
                'Authorization' => 'Bearer ' . $bearerToken,
            ],
        ]);

        self::assertResponseStatusCodeSame(200);
        $this->assertNotEmpty($response->getContent());
    }

    public function testAuthenticationWithoutTokenFailed()
    {
        self::createApiClient();
        $client = static::createClient();
        $response = $client->request('GET', '/api/test/unscoped/product/1');

        self::assertResponseStatusCodeSame(401);
        $this->assertEquals('No Authorization header provided', $response->getContent(false));
    }

    public function testAuthenticationWithoutBearerFailed()
    {
        self::createApiClient();
        $bearerToken = $this->getBearerToken();
        $client = static::createClient();
        $response = $client->request('GET', '/api/test/unscoped/product/1', [
            'headers' => [
                'Authorization' => $bearerToken,
            ],
        ]);

        self::assertResponseStatusCodeSame(401);
        $this->assertEquals('Bearer token missing', $response->getContent(false));
    }

    public function testAuthenticationWithInvalidCredential()
    {
        self::createApiClient();
        $client = static::createClient();
        $response = $client->request('GET', '/api/test/unscoped/product/1', [
            'headers' => [
                'Authorization' => 'Bearer toto',
            ],
        ]);

        self::assertResponseStatusCodeSame(401);
        $this->assertEquals('Invalid credentials', $response->getContent(false));
    }

    public function testAuthenticationWithLowerCaseBearerFailed()
    {
        self::createApiClient();
        $bearerToken = $this->getBearerToken();
        $client = static::createClient();
        $response = $client->request('GET', '/api/test/unscoped/product/1', [
            'headers' => [
                'Authorization' => 'bearer ' . $bearerToken,
            ],
        ]);

        self::assertResponseStatusCodeSame(401);
        $this->assertEquals('Bearer token missing', $response->getContent(false));
    }

    public function testAuthenticationWithScopeSuccess()
    {
        self::createApiClient(['product_read']);
        $bearerToken = $this->getBearerToken(['product_read']);
        $client = static::createClient();
        $response = $client->request('GET', '/api/test/scoped/product/1', [
            'auth_bearer' => $bearerToken,
        ]);

        self::assertResponseStatusCodeSame(200);
        $this->assertNotEmpty($response->getContent());
    }

    public function testAuthenticationWithoutScopeInJWTTokenFailed()
    {
        // API Client does have the scope associated
        self::createApiClient(['product_read']);
        // But the token is generated without containing the required scope
        $bearerToken = $this->getBearerToken();
        $client = static::createClient();
        $client->request('GET', '/api/test/scoped/product/1', [
            'auth_bearer' => $bearerToken,
        ]);

        self::assertResponseStatusCodeSame(403);
    }
}
