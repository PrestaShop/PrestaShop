<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Service\Routing;

use Configuration;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Routing\AnonymousRouteProvider;
use PrestaShopBundle\Security\Admin\UserTokenManager;
use PrestaShopBundle\Service\Routing\Router;
use ReflectionClass;

class RouterTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->resetConfigurationCache();
    }

    public function testGenerateTokenizedUrlWithFragments(): void
    {
        $url = 'my-shop.com/product#routing-in-prestashop';
        $route = Router::generateTokenizedUrl($url, 'token');
        static::assertEquals('my-shop.com/product?_token=token#routing-in-prestashop', $route);

        $url = 'my-shop.com/product?delete=1#routing-in-prestashop';
        $route = Router::generateTokenizedUrl($url, 'token');
        static::assertEquals('my-shop.com/product?delete=1&_token=token#routing-in-prestashop', $route);

        $url = 'localhost/shopp/product?delete=1&confirm=1#routing-in-prestashop/tokens?route';
        $route = Router::generateTokenizedUrl($url, 'token');
        static::assertEquals('localhost/shopp/product?delete=1&confirm=1&_token=token#routing-in-prestashop/tokens?route', $route);
    }

    public function testGenerateDoesNotAddTokenForApiPlatformRoutes(): void
    {
        $this->enableTokensInUrls();

        $router = $this->buildTestableRouter('/admin-api/products/1');

        // API Platform routes (prefixed with _api) must NOT get _token in the URL
        static::assertEquals('/admin-api/products/1', $router->generate('_api_products_get_item'));
        static::assertEquals('/admin-api/products/1', $router->generate('_api_products_post_collection'));
        static::assertEquals('/admin-api/products/1', $router->generate('_api_languages_get_collection'));
    }

    public function testGenerateAddsTokenForRegularAdminRoutes(): void
    {
        $this->enableTokensInUrls();

        $router = $this->buildTestableRouter('/admin/products/edit');

        $url = $router->generate('admin_products_edit');
        static::assertStringContainsString('_token=test-token', $url);
    }

    public function testGenerateDoesNotAddTokenForAnonymousRoutes(): void
    {
        $this->enableTokensInUrls();

        $userTokenManagerMock = $this->createMock(UserTokenManager::class);
        $userTokenManagerMock->method('getSymfonyToken')->willReturn('test-token');

        $anonymousRouteProviderMock = $this->createMock(AnonymousRouteProvider::class);
        $anonymousRouteProviderMock->method('isRouteAnonymous')->willReturn(true);

        $router = $this->getMockBuilder(Router::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['generateFromParent'])
            ->getMock();
        $router->method('generateFromParent')->willReturn('/public/page');
        $router->setUserTokenManager($userTokenManagerMock);
        $router->setAnonymousRouteProvider($anonymousRouteProviderMock);

        $url = $router->generate('some_public_route');
        static::assertStringNotContainsString('_token', $url);
    }

    private function buildTestableRouter(string $fixedUrl): Router
    {
        $userTokenManagerMock = $this->createMock(UserTokenManager::class);
        $userTokenManagerMock->method('getSymfonyToken')->willReturn('test-token');

        $anonymousRouteProviderMock = $this->createMock(AnonymousRouteProvider::class);
        $anonymousRouteProviderMock->method('isRouteAnonymous')->willReturn(false);

        $router = $this->getMockBuilder(Router::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['generateFromParent'])
            ->getMock();
        $router->method('generateFromParent')->willReturn($fixedUrl);
        $router->setUserTokenManager($userTokenManagerMock);
        $router->setAnonymousRouteProvider($anonymousRouteProviderMock);

        return $router;
    }

    /**
     * Forces TokenInUrls::isDisabled() to return false by injecting a truthy
     * PS_SECURITY_TOKEN value directly into the Configuration static cache,
     * bypassing any database dependency.
     */
    private function enableTokensInUrls(): void
    {
        $reflection = new ReflectionClass(Configuration::class);

        $initializedProp = $reflection->getProperty('_initialized');
        $initializedProp->setAccessible(true);
        $initializedProp->setValue(null, true);

        $cacheProp = $reflection->getProperty('_new_cache_global');
        $cacheProp->setAccessible(true);
        $cache = $cacheProp->getValue() ?? [];
        $cache['PS_SECURITY_TOKEN'][0] = true;
        $cacheProp->setValue(null, $cache);
    }

    private function resetConfigurationCache(): void
    {
        $reflection = new ReflectionClass(Configuration::class);

        $initializedProp = $reflection->getProperty('_initialized');
        $initializedProp->setAccessible(true);
        $initializedProp->setValue(null, false);

        $cacheProp = $reflection->getProperty('_new_cache_global');
        $cacheProp->setAccessible(true);
        $cacheProp->setValue(null, null);
    }
}
