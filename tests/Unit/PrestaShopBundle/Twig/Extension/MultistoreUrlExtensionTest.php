<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Twig\Extension;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Entity\Shop;
use PrestaShopBundle\Entity\ShopGroup;
use PrestaShopBundle\Twig\Extension\MultistoreUrlExtension;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class MultistoreUrlExtensionTest extends TestCase
{
    /**
     * @var MockObject|RequestStack
     */
    private $requestStackMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->requestStackMock = $this
            ->getMockBuilder(RequestStack::class)
            ->getMock()
        ;
    }

    public function testItShouldReturnsThreeFunctions(): void
    {
        $extension = new MultistoreUrlExtension(
            $this->requestStackMock
        );

        $functions = $extension->getFunctions();
        $this->assertCount(3, $functions);
        $this->assertEquals('multistore_url', $functions[0]->getName());
        $this->assertEquals('multistore_group_url', $functions[1]->getName());
        $this->assertEquals('multistore_shop_url', $functions[2]->getName());
    }

    public function testItSetShopContext(): void
    {
        $extension = new MultistoreUrlExtension(
            $this->requestStackMock
        );

        $this->mockRequest(new InputBag());

        $result = $extension->generateUrl(25);

        $this->assertEquals('/admin/index.php/categories/test?setShopContext=25', $result);
    }

    public function testItSetShopContextWithPrefix(): void
    {
        $extension = new MultistoreUrlExtension(
            $this->requestStackMock
        );

        $this->mockRequest(new InputBag());

        $result = $extension->generateUrl(25, 'ps-');

        $this->assertEquals('/admin/index.php/categories/test?setShopContext=ps-25', $result);
    }

    public function testItSetShopContextWithMultipleParameter(): void
    {
        $extension = new MultistoreUrlExtension(
            $this->requestStackMock
        );

        $query = new InputBag();
        $query->set('category_id', 12);
        $this->mockRequest($query);

        $result = $extension->generateUrl(25);

        $this->assertEquals('/admin/index.php/categories/test?category_id=12&setShopContext=25', $result);
    }

    public function testItSetShopContextFromShop(): void
    {
        $extension = new MultistoreUrlExtension(
            $this->requestStackMock
        );

        $this->mockRequest(new InputBag());

        $shop = $this
            ->getMockBuilder(Shop::class)
            ->getMock();

        $shop->method('getId')
            ->willReturn(42);

        $result = $extension->generateShopUrl($shop);

        $this->assertEquals('/admin/index.php/categories/test?setShopContext=s-42', $result);
    }

    public function testItSetShopContextFromShopGroup(): void
    {
        $extension = new MultistoreUrlExtension(
            $this->requestStackMock
        );

        $this->mockRequest(new InputBag());

        $shop = $this
            ->getMockBuilder(ShopGroup::class)
            ->getMock();

        $shop->method('getId')
            ->willReturn(43);

        $result = $extension->generateGroupUrl($shop);

        $this->assertEquals('/admin/index.php/categories/test?setShopContext=g-43', $result);
    }

    protected function mockRequest(InputBag $query): void
    {
        $requestMock = $this
            ->getMockBuilder(Request::class)
            ->getMock()
        ;

        $requestMock->query = $query;

        $this->requestStackMock
            ->method('getCurrentRequest')
            ->willReturn($requestMock)
        ;

        $requestMock
            ->method('getBaseUrl')
            ->willReturn('/admin/index.php')
        ;

        $requestMock
            ->method('getPathInfo')
            ->willReturn('/categories/test')
        ;
    }
}
