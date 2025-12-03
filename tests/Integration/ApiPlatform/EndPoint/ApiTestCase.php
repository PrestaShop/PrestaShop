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

namespace Tests\Integration\ApiPlatform\EndPoint;

use AdminAPIKernel;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase as ApiPlatformTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use Configuration;
use PrestaShop\PrestaShop\Core\Domain\ApiClient\Command\AddApiClientCommand;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use RuntimeException;
use Shop;
use ShopGroup;
use Tests\Integration\Utility\LanguageTrait;
use Tests\Resources\Resetter\ApiClientResetter;

abstract class ApiTestCase extends ApiPlatformTestCase
{
    use LanguageTrait;

    protected const CLIENT_ID = 'test_client_id';
    protected const CLIENT_NAME = 'test_client_name';

    protected static ?string $clientSecret = null;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::updateConfiguration('PS_ADMIN_API_FORCE_DEBUG_SECURED', 0);
        ApiClientResetter::resetApiClient();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        ApiClientResetter::resetApiClient();
        self::updateConfiguration('PS_ADMIN_API_FORCE_DEBUG_SECURED', 1);
        self::$clientSecret = null;
    }

    /**
     * @dataProvider getProtectedEndpoints
     *
     * @param string $method
     * @param string $uri
     * @param string $contentType
     * @param bool $scopeNeeded
     */
    public function testProtectedEndpoints(string $method, string $uri, string $contentType = 'application/json', bool $scopeNeeded = true): void
    {
        $options['headers']['content-type'] = $contentType;
        // Check that endpoints are not accessible without a proper Bearer token
        $response = static::createClient([], $options)->request($method, $uri);
        self::assertResponseStatusCodeSame(401);

        $content = $response->getContent(false);
        $this->assertNotEmpty($content);
        $this->assertEquals('"No Authorization header provided"', $content);

        // Test same endpoint with a token but without scopes
        if ($scopeNeeded) {
            $emptyBearerToken = $this->getBearerToken();
            static::createClient([], $options)->request($method, $uri, ['auth_bearer' => $emptyBearerToken]);
            self::assertResponseStatusCodeSame(403);
        }
    }

    /**
     * You must provide a list of protected endpoints that will be automatically checked,
     * the test will check that the endpoints are not accessible when no token is specified
     * AND that they are not accessible when the no particular scope is specified.
     *
     * You should use yield return like this:
     *
     *  yield 'get endpoint' => [
     *      'GET',
     *      '/product/1',
     *  ];
     *
     * @return iterable
     */
    public function getProtectedEndpoints(): iterable
    {
        // Before we could return a EmptyIterator but now PHPUnit forces at least one element in the iterable
        yield 'infos endpoint' => [
            'GET',
            '/api-clients/infos',
            'application/json',
            // The endpoint is protected when you have no token, however it doesn't require any particular scope
            false,
        ];
    }

    /**
     * API endpoints are only available in the OAuth application so we force using the proper kernel here.
     *
     * @return string
     */
    protected static function getKernelClass(): string
    {
        return AdminAPIKernel::class;
    }

    protected static function createClient(array $kernelOptions = [], array $defaultOptions = []): Client
    {
        if (!isset($defaultOptions['headers']['accept'])) {
            $defaultOptions['headers']['accept'] = ['application/json'];
        }

        if (!isset($defaultOptions['headers']['content-type'])) {
            $defaultOptions['headers']['content-type'] = ['application/json'];
        }

        return parent::createClient($kernelOptions, $defaultOptions);
    }

    protected function getBearerToken(array $scopes = [], array $kernelOptions = [], array $clientOptions = []): string
    {
        if (null === self::$clientSecret) {
            self::createApiClient($scopes);
        }
        $options = [
            'extra' => [
                'parameters' => [
                    'client_id' => static::CLIENT_ID,
                    'client_secret' => static::$clientSecret,
                    'grant_type' => 'client_credentials',
                    'scope' => $scopes,
                ],
            ],
            'headers' => [
                'content-type' => 'application/x-www-form-urlencoded',
            ],
        ];
        $response = static::createClient($kernelOptions, $clientOptions)->request('POST', '/access_token', $options);

        return json_decode($response->getContent())->access_token;
    }

    protected static function createApiClient(array $scopes = [], int $lifetime = 10000): void
    {
        $command = new AddApiClientCommand(
            static::CLIENT_NAME,
            static::CLIENT_ID,
            true,
            '',
            $lifetime,
            $scopes
        );

        $container = static::createClient()->getContainer();
        $commandBus = $container->get('prestashop.core.command_bus');
        $createdApiClient = $commandBus->handle($command);

        self::$clientSecret = $createdApiClient->getSecret();
    }

    protected static function addShopGroup(string $groupName, ?string $color = null): int
    {
        $shopGroup = new ShopGroup();
        $shopGroup->name = $groupName;
        $shopGroup->active = true;

        if ($color !== null) {
            $shopGroup->color = $color;
        }

        if (!$shopGroup->add()) {
            throw new RuntimeException('Could not create shop group');
        }

        return (int) $shopGroup->id;
    }

    protected static function addShop(string $shopName, int $shopGroupId, ?string $color = null): int
    {
        $shop = new Shop();
        $shop->active = true;
        $shop->id_shop_group = $shopGroupId;
        // 2 : ID Category for "Home" in database
        $shop->id_category = 2;
        $shop->theme_name = _THEME_NAME_;
        $shop->name = $shopName;
        if ($color !== null) {
            $shop->color = $color;
        }

        if (!$shop->add()) {
            throw new RuntimeException('Could not create shop');
        }
        $shop->setTheme();
        Shop::resetContext();
        Shop::resetStaticCache();

        return (int) $shop->id;
    }

    protected static function updateConfiguration(string $configurationKey, $value, ?ShopConstraint $shopConstraint = null): void
    {
        self::getContainer()->get(ShopConfigurationInterface::class)->set($configurationKey, $value, $shopConstraint ?: ShopConstraint::allShops());
        Configuration::resetStaticCache();
    }
}
