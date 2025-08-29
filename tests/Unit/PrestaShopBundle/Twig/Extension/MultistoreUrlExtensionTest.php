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
