<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\Controller;

use Context;
use FrontControllerCore;
use Link;
use PHPUnit\Framework\TestCase;
use Tools;

class FrontControllerTest extends TestCase
{
    protected function setUp(): void
    {
        $_GET = [];
        $_POST = [];
        $_SERVER['SERVER_NAME'] = 'shop.example.test';
        Tools::resetRequest();
        Tools::setFallbackParameters([]);
    }

    protected function tearDown(): void
    {
        $_GET = [];
        $_POST = [];
        unset($_SERVER['HTTP_REFERER']);
        Tools::resetRequest();
        Tools::setFallbackParameters([]);
    }

    /**
     * @dataProvider backAwarePageProvider
     */
    public function testItAddsBackParameterToBackAwarePageLink(string $pageName): void
    {
        $_GET['back'] = 'https://shop.example.test/order';

        $pageLink = $this
            ->getController($pageName, ['back' => 'https://shop.example.test/order'], $pageName . '?back=https://shop.example.test/order')
            ->getPageLinkForTemplate($pageName);

        $this->assertSame($pageName . '?back=https://shop.example.test/order', $pageLink);
    }

    /**
     * @dataProvider invalidBackProvider
     */
    public function testItDoesNotAddBackParameterWhenBackIsNotValid(?string $back): void
    {
        if ($back !== null) {
            $_GET['back'] = $back;
        }

        $pageLink = $this
            ->getController('authentication', null, 'authentication')
            ->getPageLinkForTemplate('authentication');

        $this->assertSame('authentication', $pageLink);
    }

    public function testItDoesNotAddBackParameterToOtherPageLink(): void
    {
        $_GET['back'] = 'https://shop.example.test/order';

        $pageLink = $this
            ->getController('cart', null, 'cart')
            ->getPageLinkForTemplate('cart');

        $this->assertSame('cart', $pageLink);
    }

    public static function backAwarePageProvider(): iterable
    {
        yield ['authentication'];
        yield ['registration'];
        yield ['password'];
    }

    /**
     * @dataProvider logoutReferrerProvider
     */
    public function testLogoutRedirectUsesOnlyAnAllowedReferrerPath(
        mixed $referrer,
        ?string $expectedPath,
        string $shopBaseUrl = 'https://shop.example.test:8443/shop/virtual/',
        string $shopOrigin = 'https://shop.example.test:8443'
    ): void {
        unset($_SERVER['HTTP_REFERER']);
        if ($referrer !== null) {
            $_SERVER['HTTP_REFERER'] = $referrer;
        }

        $link = $this->getMockBuilder(Link::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getBaseLink', 'getPageLink'])
            ->getMock();
        $link->expects($expectedPath === null ? $this->never() : $this->once())
            ->method('getBaseLink')
            ->willReturn($shopBaseUrl);
        $link->expects($expectedPath === null ? $this->once() : $this->never())
            ->method('getPageLink')
            ->with('index')
            ->willReturn($shopBaseUrl);

        $context = new Context();
        $context->link = $link;
        $controller = new TestFrontController($context);
        // The shape returned by Tools::getDomains(), including SSL, alternative and other-shop domains.
        $domains = [
            'shop.example.test' => [['id_shop' => 1, 'physical' => '/shop/', 'virtual' => 'virtual/']],
            'secure.example.test:8443' => [['id_shop' => 1, 'physical' => '/shop/', 'virtual' => 'virtual/']],
            'alias.example.test:8080' => [['id_shop' => 1, 'physical' => '/shop/', 'virtual' => 'virtual/']],
            'other-shop.example.test' => [['id_shop' => 2, 'physical' => '/shop/', 'virtual' => 'second/']],
        ];

        $this->assertSame(
            $expectedPath === null ? $shopBaseUrl : $shopOrigin . $expectedPath,
            $controller->getLogoutRedirectUrl($domains)
        );
    }

    public static function logoutReferrerProvider(): iterable
    {
        yield 'shop HTTP' => ['http://shop.example.test/catalog', '/catalog'];
        yield 'HTTP shop at root' => ['https://shop.example.test/catalog', '/catalog', 'http://shop.example.test/', 'http://shop.example.test'];
        yield 'HTTPS shop at root' => ['http://shop.example.test/catalog', '/catalog', 'https://shop.example.test/', 'https://shop.example.test'];
        yield 'HTTP root fallback' => [null, null, 'http://shop.example.test/', 'http://shop.example.test'];
        yield 'shop HTTPS and subfolders' => ['https://shop.example.test/shop/virtual/fr/3-clothes', '/shop/virtual/fr/3-clothes'];
        yield 'query preserved verbatim' => ['https://shop.example.test/shop/index.php?controller=search&s=a%26b&x=one?two', '/shop/index.php?controller=search&s=a%26b&x=one?two'];
        yield 'fragment removed' => ['https://shop.example.test/catalog?page=2#products', '/catalog?page=2'];
        yield 'encoded path' => ['https://shop.example.test/caf%C3%A9', '/caf%C3%A9'];
        yield 'case insensitive hostname and scheme' => ['HTTPS://SHOP.EXAMPLE.TEST/catalog', '/catalog'];
        yield 'SSL domain and port' => ['https://secure.example.test:8443/catalog', '/catalog'];
        yield 'alternative domain' => ['http://alias.example.test:8080/catalog', '/catalog'];
        yield 'referrer port is not reused' => ['https://shop.example.test:9443/catalog', '/catalog'];
        yield 'other configured shop' => ['https://other-shop.example.test/shop/second/catalog', '/shop/second/catalog'];
        yield 'protocol relative internal' => ['//shop.example.test/catalog?q=1', '/catalog?q=1'];
        yield 'no path' => ['https://shop.example.test', '/'];
        yield 'no path with query' => ['https://shop.example.test?q=1', '/?q=1'];
        yield 'path resembling authority stays internal' => ['https://shop.example.test//external.example.test/path', '//external.example.test/path'];
        yield 'encoded slash stays encoded' => ['https://shop.example.test/%2fexternal.example.test', '/%2fexternal.example.test'];
        yield 'missing' => [null, null];
        yield 'empty' => ['', null];
        yield 'unexpected array' => [[], null];
        yield 'external' => ['https://external.example.test/catalog', null];
        yield 'protocol relative external' => ['//external.example.test/catalog', null];
        yield 'domain suffix' => ['https://shop.example.test.external.example.test/catalog', null];
        yield 'unconfigured subdomain' => ['https://sub.shop.example.test/catalog', null];
        yield 'credentials hiding external host' => ['https://shop.example.test@external.example.test/catalog', null];
        yield 'username on internal host is discarded' => ['https://external.example.test@shop.example.test/catalog', '/catalog'];
        yield 'credentials on internal host are discarded' => ['https://user:password@shop.example.test/catalog?q=1', '/catalog?q=1'];
        yield 'backslash ambiguity' => ['https://external.example.test\\@shop.example.test/catalog', null];
        yield 'backslash in path' => ['https://shop.example.test/\\external.example.test', null];
        yield 'relative path' => ['/catalog', null];
        yield 'host without scheme' => ['shop.example.test/catalog', null];
        yield 'unsupported scheme' => ['ftp://shop.example.test/catalog', null];
        yield 'script scheme' => ['javascript:alert(1)', null];
        yield 'missing hostname' => ['https:///catalog', null];
        yield 'too many leading slashes' => ['///shop.example.test/catalog', null];
        yield 'invalid port' => ['https://shop.example.test:99999/catalog', null];
        yield 'invalid percent encoding' => ['https://shop.example.test/catalog%zz', null];
        yield 'raw whitespace' => [' https://shop.example.test/catalog', null];
        yield 'raw space in path' => ['https://shop.example.test/a b', null];
        yield 'header injection' => ["https://shop.example.test/catalog\r\nLocation: https://external.example.test", null];
        yield 'null byte' => ["https://shop.example.test/cata\0log", null];
        yield 'tab in hostname' => ["https://shop.example.\ttest/catalog", null];
    }

    public static function invalidBackProvider(): iterable
    {
        yield 'empty back' => [''];
        yield 'missing back' => [null];
        yield 'external back' => ['https://external.example.test/order'];
    }

    private function getController(string $expectedPageName, ?array $expectedRequest, string $expectedPageLink): TestFrontController
    {
        $context = new Context();
        $context->link = $this->getLinkMock($expectedPageName, $expectedRequest, $expectedPageLink);

        return new TestFrontController($context);
    }

    private function getLinkMock(string $expectedPageName, ?array $expectedRequest, string $expectedPageLink): Link
    {
        $link = $this->getMockBuilder(Link::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getPageLink'])
            ->getMock();
        $link
            ->expects($this->once())
            ->method('getPageLink')
            ->with($expectedPageName, null, null, $expectedRequest, false, null, false)
            ->willReturn($expectedPageLink);

        return $link;
    }
}

class TestFrontController extends FrontControllerCore
{
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function getPageLinkForTemplate(string $pageName): string
    {
        return parent::getPageLinkForTemplate($pageName);
    }

    public function getLogoutRedirectUrl(array $shopDomains): string
    {
        return parent::getLogoutRedirectUrl($shopDomains);
    }
}
