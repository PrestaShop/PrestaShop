<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Context;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Feature\MultistoreFeature;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use PrestaShop\PrestaShop\Core\Context\ShopContextBuilder;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use Shop;
use ShopGroup;

class ShopContextBuilderTest extends TestCase
{
    public function testBuild(): void
    {
        $shop = $this->mockShop();
        $shopGroup = $this->createMock(ShopGroup::class);
        $shopGroup->share_order = 1;
        $shopGroup->share_customer = true;
        $shopGroup->share_stock = '1';
        $shop->expects(static::exactly(3))->method('getGroup')->willReturn($shopGroup);

        $builder = new ShopContextBuilder(
            $this->mockShopRepository($shop),
            $this->createMock(ContextStateManager::class),
            $this->mockMultistoreFeature()
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
        $this->assertEquals([1, 3], $shopContext->getAssociatedShopIds());
        $this->assertFalse($shopContext->isMultiShopEnabled());
        $this->assertFalse($shopContext->isMultiShopUsed());
        $this->assertTrue($shopContext->hasGroupSharingStocks());
        $this->assertTrue($shopContext->hasGroupSharingCustomers());
        $this->assertTrue($shopContext->hasGroupSharingOrders());
    }

    public function testNoShopId(): void
    {
        $builder = new ShopContextBuilder(
            $this->mockShopRepository(),
            $this->createMock(ContextStateManager::class),
            $this->mockMultistoreFeature()
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
            $this->createMock(ContextStateManager::class),
            $this->mockMultistoreFeature()
        );
        $builder->setShopId(42);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessageMatches('/Cannot build shop context as no shopConstraint has been defined/');
        $builder->build();
    }

    public function testMultistoreEnabled(): void
    {
        $builder = new ShopContextBuilder(
            $this->mockShopRepository(),
            $this->createMock(ContextStateManager::class),
            $this->mockMultistoreFeature(true, true)
        );
        $builder->setShopId(42);
        $builder->setShopConstraint(ShopConstraint::shop(42));

        $shopContext = $builder->build();
        $this->assertTrue($shopContext->isMultiShopEnabled());
        $this->assertTrue($shopContext->isMultiShopUsed());
    }

    private function mockShop(): Shop|MockObject
    {
        $shop = $this->createMock(Shop::class);
        $shop->id = 42;
        $shop->name = 'Shop name';
        $shop->id_shop_group = 51;
        $shop->id_category = 69;
        $shop->theme_name = Theme::getDefaultTheme();
        $shop->color = 'red';
        $shop->physical_uri = 'http://localhost';
        $shop->virtual_uri = '/virtual';
        $shop->domain = 'localhost';
        $shop->domain_ssl = 'secure.localhost';
        $shop->active = false;

        return $shop;
    }

    private function mockShopRepository(Shop|MockObject|null $shop = null): ShopRepository|MockObject
    {
        $repository = $this->createMock(ShopRepository::class);
        $repository
            ->method('get')
            ->willReturn($shop ?: $this->mockShop())
        ;
        $repository
            ->method('getAssociatedShopIds')
            ->willReturn([1, 3])
        ;

        return $repository;
    }

    private function mockMultistoreFeature(bool $isActive = false, bool $isUsed = false): MultistoreFeature|MockObject
    {
        $feature = $this->createMock(MultistoreFeature::class);
        $feature
            ->method('isActive')
            ->willReturn($isActive)
        ;
        $feature
            ->method('isUsed')
            ->willReturn($isUsed)
        ;

        return $feature;
    }
}
