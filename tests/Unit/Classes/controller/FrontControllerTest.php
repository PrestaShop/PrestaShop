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
}
