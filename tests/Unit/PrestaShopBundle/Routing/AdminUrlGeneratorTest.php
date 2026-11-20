<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Routing;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShopBundle\Entity\Shop;
use PrestaShopBundle\Entity\ShopUrl;
use PrestaShopBundle\Routing\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class AdminUrlGeneratorTest extends TestCase
{
    /**
     * @dataProvider provideAdminUrlCases
     */
    public function testGenerateAdminUrl(
        string $requestUrl,
        string $physicalUri,
        string $adminFolder,
        string $urlPath,
        string $expected
    ): void {
        $requestStack = new RequestStack();
        $requestStack->push(Request::create($requestUrl));

        $generator = $this->createGenerator(
            $requestStack,
            [['domain' => 'example.com', 'domain_ssl' => 'example.com', 'physical_uri' => $physicalUri]],
            $adminFolder,
        );

        self::assertSame($expected, $generator->generateAdminUrl($urlPath));
    }

    public static function provideAdminUrlCases(): iterable
    {
        yield 'reset password route' => [
            'https://example.com/admin123/index.php',
            '/',
            'admin123',
            '/reset-password/some-token',
            'https://example.com/admin123/index.php/reset-password/some-token',
        ];

        yield 'legacy controller query string' => [
            'https://example.com/admin123/index.php',
            '/',
            'admin123',
            '?controller=LegacyAdminController',
            'https://example.com/admin123/index.php?controller=LegacyAdminController',
        ];

        yield 'installation in subdirectory' => [
            'https://example.com/shop/admin123/index.php',
            '/shop/',
            '/admin123/',
            '/reset-password/token',
            'https://example.com/shop/admin123/index.php/reset-password/token',
        ];

        yield 'empty url path' => [
            'https://example.com/admin123/index.php',
            '/',
            'admin123',
            '',
            'https://example.com/admin123/index.php',
        ];
    }

    /**
     * @dataProvider provideConfiguredUrlCases
     */
    public function testConfiguredShopUrls(
        ?string $requestUrl,
        array $shopUrls,
        string $expected,
        string $fallbackBaseUrl = 'https://default.example/fallback/fr/',
    ): void {
        $requestStack = new RequestStack();
        if ($requestUrl !== null) {
            $requestStack->push(Request::create($requestUrl));
        }

        $generator = $this->createGenerator($requestStack, $shopUrls, 'admin123', $fallbackBaseUrl);

        self::assertSame($expected, $generator->generateAdminUrl('/reset-password/token'));
    }

    public static function provideConfiguredUrlCases(): iterable
    {
        $fallback = 'https://default.example/fallback/admin123/index.php/reset-password/token';
        $secondShop = 'https://secure.second.example/shop/admin123/index.php/reset-password/token';
        $request = 'https://second.example/shop/admin123/index.php/login';

        yield 'different shop domain and physical URI, virtual URI excluded' => [$request, [[]], $secondShop];
        yield 'SSL domain matches' => ['https://secure.second.example/shop/admin123/', [[]], $secondShop];
        yield 'configured alias retained' => [
            'https://alias.example/shop/admin123/',
            [[], ['domain' => 'alias.example', 'domain_ssl' => 'alias.example', 'main' => false]],
            'https://alias.example/shop/admin123/index.php/reset-password/token',
        ];
        yield 'unknown host' => ['https://evil.example/shop/admin123/', [[]], $fallback];
        yield 'deceptive suffix' => ['https://second.example.evil.example/shop/admin123/', [[]], $fallback];
        yield 'deceptive prefix' => ['https://evilsecond.example/shop/admin123/', [[]], $fallback];
        yield 'unconfigured subdomain' => ['https://evil.second.example/shop/admin123/', [[]], $fallback];
        yield 'wrong physical URI' => ['https://second.example/other/admin123/', [[]], $fallback];
        yield 'physical URI boundary' => ['https://second.example/shopping/admin123/', [[]], $fallback];
        yield 'most specific physical URI' => [
            'https://second.example/shop/nested/admin123/',
            [['physical_uri' => '/'], [], ['physical_uri' => '/shop/nested/']],
            'https://secure.second.example/shop/nested/admin123/index.php/reset-password/token',
        ];
        yield 'equal physical URIs use stable repository order' => [
            $request,
            [[], ['domain_ssl' => 'other-ssl.example']],
            $secondShop,
        ];
        yield 'inactive URL' => [$request, [['active' => false]], $fallback];
        yield 'inactive shop' => [$request, [['shop_active' => false]], $fallback];
        yield 'deleted shop' => [$request, [['deleted' => true]], $fallback];
        yield 'inactive specific URL does not mask active URL' => [
            'https://second.example/shop/nested/admin123/',
            [[], ['physical_uri' => '/shop/nested/', 'active' => false]],
            $secondShop,
        ];
        yield 'no HTTP request' => [null, [[]], $fallback];
        yield 'no configured URL' => [$request, [], $fallback];
        yield 'fallback without virtual URI' => [null, [], $fallback, 'https://default.example/fallback/'];
        yield 'fallback preserves configured port' => [
            'https://evil.example/',
            [[]],
            'https://default.example:8443/fallback/admin123/index.php/reset-password/token',
            'https://default.example:8443/fallback/fr/',
        ];
        yield 'configured nonstandard port' => [
            'https://second.example:8443/shop/admin123/',
            [['domain' => 'second.example:8443', 'domain_ssl' => 'secure.second.example:9443']],
            'https://secure.second.example:9443/shop/admin123/index.php/reset-password/token',
        ];
        yield 'unconfigured port' => ['https://second.example:8443/shop/admin123/', [[]], $fallback];
        yield 'missing configured port' => [$request, [['domain' => 'second.example:8443']], $fallback];
        yield 'explicit default HTTPS port' => ['https://second.example:443/shop/admin123/', [[]], $secondShop];
        yield 'configured default HTTPS port' => [
            $request,
            [['domain' => 'second.example:443']],
            $secondShop,
        ];
        yield 'case insensitive DNS host' => ['https://SECOND.EXAMPLE/shop/admin123/', [[]], $secondShop];
        yield 'HTTP uses configured HTTP domain' => [
            'http://secure.second.example/shop/admin123/',
            [[]],
            'http://second.example/shop/admin123/index.php/reset-password/token',
            'http://default.example/fallback/fr/',
        ];
        yield 'explicit default HTTP port' => [
            'http://second.example:80/shop/admin123/',
            [[]],
            'http://second.example/shop/admin123/index.php/reset-password/token',
            'http://default.example/fallback/fr/',
        ];
        yield 'HTTPS does not match configured HTTP port' => [
            $request,
            [['domain' => 'second.example:80']],
            $fallback,
        ];
        yield 'HTTPS request does not override context SSL policy' => [
            $request,
            [[]],
            'http://second.example/shop/admin123/index.php/reset-password/token',
            'http://default.example/fallback/fr/',
        ];
    }

    /**
     * @dataProvider provideForwardedHostCases
     */
    public function testForwardedHost(bool $trustedProxy, string $forwardedHost, string $expected): void
    {
        $previousProxies = Request::getTrustedProxies();
        $previousHeaders = Request::getTrustedHeaderSet();
        try {
            Request::setTrustedProxies($trustedProxy ? ['192.0.2.1'] : [], Request::HEADER_X_FORWARDED_HOST | Request::HEADER_X_FORWARDED_PROTO | Request::HEADER_X_FORWARDED_PORT);
            $request = Request::create('http://internal.example/shop/admin123/', 'GET', [], [], [], [
                'REMOTE_ADDR' => '192.0.2.1',
                'HTTP_X_FORWARDED_HOST' => $forwardedHost,
                'HTTP_X_FORWARDED_PROTO' => 'https',
                'HTTP_X_FORWARDED_PORT' => '443',
            ]);
            $requestStack = new RequestStack();
            $requestStack->push($request);
            $generator = $this->createGenerator($requestStack, [[]]);

            self::assertSame($expected, $generator->generateAdminUrl('/reset-password/token'));
        } finally {
            Request::setTrustedProxies($previousProxies, $previousHeaders);
        }
    }

    public static function provideForwardedHostCases(): iterable
    {
        $fallback = 'https://default.example/fallback/admin123/index.php/reset-password/token';
        yield 'trusted proxy with configured host' => [true, 'second.example', 'https://secure.second.example/shop/admin123/index.php/reset-password/token'];
        yield 'trusted proxy with unknown host' => [true, 'evil.example', $fallback];
        yield 'untrusted forwarded host ignored' => [false, 'second.example', $fallback];
    }

    public function testMainRequestDeterminesShopUrl(): void
    {
        $requestStack = new RequestStack();
        $requestStack->push(Request::create('https://second.example/shop/admin123/'));
        $requestStack->push(Request::create('https://evil.example/'));
        $generator = $this->createGenerator($requestStack, [[]]);

        self::assertSame(
            'https://secure.second.example/shop/admin123/index.php/reset-password/token',
            $generator->generateAdminUrl('/reset-password/token'),
        );
    }

    public function testPhysicalUriWithSymfonyBaseUrl(): void
    {
        $requestStack = new RequestStack();
        $requestStack->push(Request::create('https://second.example/shop/admin123/index.php/login', 'GET', [], [], [], [
            'SCRIPT_NAME' => '/shop/admin123/index.php',
            'SCRIPT_FILENAME' => '/var/www/shop/admin123/index.php',
            'PHP_SELF' => '/shop/admin123/index.php/login',
        ]));
        $generator = $this->createGenerator($requestStack, [[]]);

        self::assertSame(
            'https://secure.second.example/shop/admin123/index.php/reset-password/token',
            $generator->generateAdminUrl('/reset-password/token'),
        );
    }

    private function createGenerator(
        RequestStack $requestStack,
        array $shopUrlConfigurations,
        string $adminFolder = 'admin123',
        string $fallbackBaseUrl = 'https://default.example/fallback/fr/',
    ): AdminUrlGenerator {
        $shopContext = $this->createMock(ShopContext::class);
        $shopContext->method('getBaseURL')->willReturn($fallbackBaseUrl);
        $shopContext->method('getVirtualUri')->willReturn(str_ends_with($fallbackBaseUrl, '/fr/') ? 'fr/' : '');

        $shopUrls = [];
        foreach ($shopUrlConfigurations as $configuration) {
            $shop = (new Shop())
                ->setActive($configuration['shop_active'] ?? true)
                ->setDeleted($configuration['deleted'] ?? false);
            $shopUrls[] = (new ShopUrl())
                ->setShop($shop)
                ->setDomain($configuration['domain'] ?? 'second.example')
                ->setDomainSsl($configuration['domain_ssl'] ?? 'secure.second.example')
                ->setPhysicalUri($configuration['physical_uri'] ?? '/shop/')
                ->setVirtualUri($configuration['virtual_uri'] ?? 'fr/')
                ->setMain($configuration['main'] ?? true)
                ->setActive($configuration['active'] ?? true);
        }

        $entityManager = $this->createMock(EntityManagerInterface::class);
        if ($requestStack->getMainRequest() === null) {
            $entityManager->expects(self::never())->method('getRepository');
        } else {
            $repository = $this->createMock(EntityRepository::class);
            $repository->expects(self::once())->method('findBy')->with(['active' => true], ['id' => 'ASC'])->willReturn($shopUrls);
            $entityManager->expects(self::once())->method('getRepository')->with(ShopUrl::class)->willReturn($repository);
        }

        return new AdminUrlGenerator($shopContext, $adminFolder, $requestStack, $entityManager);
    }
}
