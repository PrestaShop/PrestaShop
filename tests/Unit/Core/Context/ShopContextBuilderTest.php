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

namespace Core\Context;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Core\Context\ShopContextBuilder;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use Shop;

class ShopContextBuilderTest extends TestCase
{
    public function testBuild(): void
    {
        $shop = $this->mockShop();
        $builder = new ShopContextBuilder(
            $this->mockShopRepository($shop),
            $this->createMock(ContextStateManager::class)
        );
        $shopConstraint = ShopConstraint::shop($shop->id);
        $builder->setShopId($shop->id);
        $builder->setShopConstraint($shopConstraint);

        $shopContext = $builder->build();
        $this->assertEquals($shopConstraint, $shopContext->getShopConstraint());
        $this->assertEquals($shop->id, $shopContext->getId());
        $this->assertEquals($shop->name, $shopContext->getName());
        $this->assertEquals($shop->id_shop_group, $shopContext->getShopGroupId());
        $this->assertEquals($shop->id_category, $shopContext->getCategoryId());
        $this->assertEquals($shop->theme_name, $shopContext->getThemeName());
        $this->assertEquals($shop->color, $shopContext->getColor());
        $this->assertEquals($shop->physical_uri, $shopContext->getPhysicalUri());
        $this->assertEquals($shop->virtual_uri, $shopContext->getVirtualUri());
        $this->assertEquals($shop->domain, $shopContext->getDomain());
        $this->assertEquals($shop->domain_ssl, $shopContext->getDomainSSL());
        $this->assertEquals($shop->active, $shopContext->isActive());
    }

    public function testNoShopId(): void
    {
        $builder = new ShopContextBuilder(
            $this->mockShopRepository(),
            $this->createMock(ContextStateManager::class)
        );
        $builder->setShopConstraint(ShopConstraint::allShops());

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Cannot build shop context as no shopId has been defined/');
        $builder->build();
    }

    public function testNoShopConstraint(): void
    {
        $builder = new ShopContextBuilder(
            $this->mockShopRepository(),
            $this->createMock(ContextStateManager::class)
        );
        $builder->setShopId(42);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Cannot build shop context as no shopConstraint has been defined/');
        $builder->build();
    }

    private function mockShop(): Shop|MockObject
    {
        $shop = $this->createMock(Shop::class);
        $shop->id = 42;
        $shop->name = 'Shop name';
        $shop->id_shop_group = 51;
        $shop->id_category = 69;
        $shop->theme_name = 'classic';
        $shop->color = 'red';
        $shop->physical_uri = 'http://localhost';
        $shop->virtual_uri = '/virtual';
        $shop->domain = 'localhost';
        $shop->domain_ssl = 'secure.localhost';
        $shop->active = false;

        return $shop;
    }

    private function mockShopRepository(Shop|MockObject $shop = null): ShopRepository|MockObject
    {
        $repository = $this->createMock(ShopRepository::class);
        $repository
            ->method('get')
            ->willReturn($shop ?: $this->mockShop())
        ;

        return $repository;
    }
}
