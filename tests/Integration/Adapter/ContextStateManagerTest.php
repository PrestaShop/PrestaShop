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

namespace Tests\Integration\Adapter;

use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Context\LegacyControllerContext;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Shop;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tests\TestCase\ContextStateTestCase;

class ContextStateManagerTest extends ContextStateTestCase
{
    protected LegacyContext $legacyContext;
    protected Shop $basicShop;
    protected Shop $shop1;
    protected Shop $shop2;

    protected LegacyControllerContext $legacyControllerContext1;
    protected LegacyControllerContext $legacyControllerContext2;

    protected function setUp(): void
    {
        parent::setUp();
        $this->legacyContext = new LegacyContext();

        $this->basicShop = new Shop(1);

        $this->shop1 = new Shop();
        $this->shop1->name = 'Name 2';
        $this->shop1->id_category = 2;
        $this->shop1->id_shop_group = 1;
        $this->shop1->add();

        $this->shop2 = new Shop();
        $this->shop2->name = 'Name 3';
        $this->shop2->id_category = 2;
        $this->shop2->id_shop_group = 1;
        $this->shop2->add();

        $this->legacyControllerContext1 = new LegacyControllerContext(
            $this->createMock(ContainerInterface::class),
            'AdminProducts',
            'admin',
            ShopConstraint::ALL_SHOPS,
            'Product',
            20,
            'token',
            'override_folder/',
            'index.php?controller=AdminProducts',
            'product'
        );

        $this->legacyControllerContext2 = new LegacyControllerContext(
            $this->createMock(ContainerInterface::class),
            'AdminCarts',
            'admin',
            ShopConstraint::ALL_SHOPS,
            'Cart',
            10,
            'token',
            'override_folder/',
            'index.php?controller=AdminCarts',
            'cart'
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->shop1->delete();
        $this->shop2->delete();
    }

    public function testControllerState(): void
    {
        $this->legacyContext->getContext()->controller = $this->legacyControllerContext1;
        $this->assertEquals($this->legacyControllerContext1->controller_name, $this->legacyContext->getContext()->controller->controller_name);

        $contextStateManager = new ContextStateManager($this->legacyContext);
        $this->assertNull($contextStateManager->getContextFieldsStack());

        $contextStateManager->setController($this->legacyControllerContext2);
        $this->assertEquals($this->legacyControllerContext2->controller_name, $this->legacyContext->getContext()->controller->controller_name);
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
        $this->assertCount(1, $contextStateManager->getContextFieldsStack());

        $contextStateManager->restorePreviousContext();
        $this->assertEquals($this->legacyControllerContext1->controller_name, $this->legacyContext->getContext()->controller->controller_name);
        $this->assertNull($contextStateManager->getContextFieldsStack());
    }

    public function testShopState(): void
    {
        $this->legacyContext->getContext()->shop = $this->basicShop;

        Shop::setContext(Shop::CONTEXT_SHOP, $this->basicShop->id);
        $this->assertEquals($this->basicShop->id, $this->legacyContext->getContext()->shop->id);
        $this->assertEquals($this->basicShop->id, Shop::getContextShopID());
        $this->assertEquals($this->basicShop->id, Shop::getContextShopGroupID());
        $this->assertEquals(Shop::CONTEXT_SHOP, Shop::getContext());

        $contextStateManager = new ContextStateManager($this->legacyContext);
        $this->assertNull($contextStateManager->getContextFieldsStack());

        $contextStateManager->setShop($this->shop1);
        $this->assertEquals($this->shop1->id, $this->legacyContext->getContext()->shop->id);
        $this->assertEquals($this->shop1->id, Shop::getContextShopID());
        $this->assertEquals($this->shop1->id_shop_group, Shop::getContextShopGroupID());
        $this->assertEquals(Shop::CONTEXT_SHOP, Shop::getContext());
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
        $this->assertCount(1, $contextStateManager->getContextFieldsStack());

        $contextStateManager->setShop($this->shop2);
        $this->assertEquals($this->shop2->id, $this->legacyContext->getContext()->shop->id);
        $this->assertEquals($this->shop2->id, Shop::getContextShopID());
        $this->assertEquals($this->shop2->id_shop_group, Shop::getContextShopGroupID());
        $this->assertEquals(Shop::CONTEXT_SHOP, Shop::getContext());
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
        $this->assertCount(1, $contextStateManager->getContextFieldsStack());

        $contextStateManager->restorePreviousContext();
        $this->assertEquals($this->basicShop->id, $this->legacyContext->getContext()->shop->id);
        $this->assertEquals($this->basicShop->id, Shop::getContextShopID());
        $this->assertEquals($this->basicShop->id, Shop::getContextShopGroupID());
        $this->assertEquals(Shop::CONTEXT_SHOP, Shop::getContext());
        $this->assertNull($contextStateManager->getContextFieldsStack());

        $contextStateManager->restorePreviousContext();
        $this->assertEquals(1, $this->legacyContext->getContext()->shop->id);
        $this->assertEquals(1, Shop::getContextShopID());
        $this->assertEquals(1, Shop::getContextShopGroupID());
        $this->assertEquals(Shop::CONTEXT_SHOP, Shop::getContext());
        $this->assertNull($contextStateManager->getContextFieldsStack());
    }

    public function testShopStateAll(): void
    {
        $this->legacyContext->getContext()->shop = $this->basicShop;

        Shop::setContext(Shop::CONTEXT_ALL);
        $this->assertEquals($this->basicShop->id, $this->legacyContext->getContext()->shop->id);
        $this->assertEquals(null, Shop::getContextShopID());
        $this->assertEquals(null, Shop::getContextShopGroupID());
        $this->assertEquals(Shop::CONTEXT_ALL, Shop::getContext());

        $contextStateManager = new ContextStateManager($this->legacyContext);
        $this->assertNull($contextStateManager->getContextFieldsStack());

        $contextStateManager->setShop($this->shop1);
        $this->assertEquals($this->shop1->id, $this->legacyContext->getContext()->shop->id);
        $this->assertEquals($this->shop1->id, Shop::getContextShopID());
        $this->assertEquals($this->shop1->id_shop_group, Shop::getContextShopGroupID());
        $this->assertEquals(Shop::CONTEXT_SHOP, Shop::getContext());
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
        $this->assertCount(1, $contextStateManager->getContextFieldsStack());

        $contextStateManager->setShop($this->shop2);
        $this->assertEquals($this->shop2->id, $this->legacyContext->getContext()->shop->id);
        $this->assertEquals($this->shop2->id, Shop::getContextShopID());
        $this->assertEquals($this->shop2->id_shop_group, Shop::getContextShopGroupID());
        $this->assertEquals(Shop::CONTEXT_SHOP, Shop::getContext());
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
        $this->assertCount(1, $contextStateManager->getContextFieldsStack());

        $contextStateManager->restorePreviousContext();
        $this->assertEquals($this->basicShop->id, $this->legacyContext->getContext()->shop->id);
        $this->assertEquals(null, Shop::getContextShopID());
        $this->assertEquals(null, Shop::getContextShopGroupID());
        $this->assertEquals(Shop::CONTEXT_ALL, Shop::getContext());
        $this->assertNull($contextStateManager->getContextFieldsStack());

        $contextStateManager->restorePreviousContext();
        $this->assertEquals($this->basicShop->id, $this->legacyContext->getContext()->shop->id);
        $this->assertEquals(null, Shop::getContextShopID());
        $this->assertEquals(null, Shop::getContextShopGroupID());
        $this->assertEquals(Shop::CONTEXT_ALL, Shop::getContext());
        $this->assertNull($contextStateManager->getContextFieldsStack());
    }

    public function testShopStateGroup(): void
    {
        $this->legacyContext->getContext()->shop = $this->basicShop;
        Shop::setContext(Shop::CONTEXT_GROUP, $this->basicShop->id);
        $this->assertEquals($this->basicShop->id, $this->legacyContext->getContext()->shop->id);
        $this->assertEquals(null, Shop::getContextShopID());
        $this->assertEquals($this->basicShop->id_shop_group, Shop::getContextShopGroupID());
        $this->assertEquals(Shop::CONTEXT_GROUP, Shop::getContext());

        $contextStateManager = new ContextStateManager($this->legacyContext);
        $this->assertNull($contextStateManager->getContextFieldsStack());

        $contextStateManager->setShop($this->shop1);
        $this->assertEquals($this->shop1->id, $this->legacyContext->getContext()->shop->id);
        $this->assertEquals($this->shop1->id, Shop::getContextShopID());
        $this->assertEquals($this->shop1->id_shop_group, Shop::getContextShopGroupID());
        $this->assertEquals(Shop::CONTEXT_SHOP, Shop::getContext());
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
        $this->assertCount(1, $contextStateManager->getContextFieldsStack());

        $contextStateManager->setShop($this->shop2);
        $this->assertEquals($this->shop2->id, $this->legacyContext->getContext()->shop->id);
        $this->assertEquals($this->shop2->id, Shop::getContextShopID());
        $this->assertEquals($this->shop2->id_shop_group, Shop::getContextShopGroupID());
        $this->assertEquals(Shop::CONTEXT_SHOP, Shop::getContext());
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
        $this->assertCount(1, $contextStateManager->getContextFieldsStack());

        $contextStateManager->restorePreviousContext();
        $this->assertEquals($this->basicShop->id, $this->legacyContext->getContext()->shop->id);
        $this->assertEquals(null, Shop::getContextShopID());
        $this->assertEquals($this->basicShop->id_shop_group, Shop::getContextShopGroupID());
        $this->assertEquals(Shop::CONTEXT_GROUP, Shop::getContext());
        $this->assertNull($contextStateManager->getContextFieldsStack());

        $contextStateManager->restorePreviousContext();
        $this->assertEquals($this->basicShop->id, $this->legacyContext->getContext()->shop->id);
        $this->assertEquals(null, Shop::getContextShopID());
        $this->assertEquals($this->basicShop->id_shop_group, Shop::getContextShopGroupID());
        $this->assertEquals(Shop::CONTEXT_GROUP, Shop::getContext());
        $this->assertNull($contextStateManager->getContextFieldsStack());
    }

    public function testSetShopContext(): void
    {
        $this->legacyContext->getContext()->shop = $this->basicShop;

        Shop::setContext(Shop::CONTEXT_SHOP, $this->basicShop->id);
        $this->assertEquals($this->basicShop->id, $this->legacyContext->getContext()->shop->id);
        $this->assertEquals($this->basicShop->id, Shop::getContextShopID());
        $this->assertEquals($this->basicShop->id, Shop::getContextShopGroupID());
        $this->assertEquals(Shop::CONTEXT_SHOP, Shop::getContext());

        $contextStateManager = new ContextStateManager($this->legacyContext);
        $this->assertNull($contextStateManager->getContextFieldsStack());

        $contextStateManager->setShopContext(Shop::CONTEXT_SHOP, $this->shop1->id);
        $this->assertEquals($this->shop1->id, $this->legacyContext->getContext()->shop->id);
        $this->assertEquals($this->shop1->id, Shop::getContextShopID());
        $this->assertEquals($this->shop1->id_shop_group, Shop::getContextShopGroupID());
        $this->assertEquals(Shop::CONTEXT_SHOP, Shop::getContext());
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
        $this->assertCount(1, $contextStateManager->getContextFieldsStack());

        $contextStateManager->setShopContext(Shop::CONTEXT_SHOP, $this->shop2->id);
        $this->assertEquals($this->shop2->id, $this->legacyContext->getContext()->shop->id);
        $this->assertEquals($this->shop2->id, Shop::getContextShopID());
        $this->assertEquals($this->shop2->id_shop_group, Shop::getContextShopGroupID());
        $this->assertEquals(Shop::CONTEXT_SHOP, Shop::getContext());
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
        $this->assertCount(1, $contextStateManager->getContextFieldsStack());

        $contextStateManager->setShopContext(Shop::CONTEXT_GROUP, $this->shop1->id_shop_group);
        $this->assertEquals($this->shop2->id, $this->legacyContext->getContext()->shop->id);
        $this->assertEquals(null, Shop::getContextShopID());
        $this->assertEquals($this->shop2->id_shop_group, Shop::getContextShopGroupID());
        $this->assertEquals(Shop::CONTEXT_GROUP, Shop::getContext());
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
        $this->assertCount(1, $contextStateManager->getContextFieldsStack());

        $contextStateManager->setShopContext(Shop::CONTEXT_ALL);
        $this->assertEquals($this->shop2->id, $this->legacyContext->getContext()->shop->id);
        $this->assertEquals(null, Shop::getContextShopID());
        $this->assertEquals(null, Shop::getContextShopGroupID());
        $this->assertEquals(Shop::CONTEXT_ALL, Shop::getContext());
        $this->assertIsArray($contextStateManager->getContextFieldsStack());
        $this->assertCount(1, $contextStateManager->getContextFieldsStack());

        $contextStateManager->restorePreviousContext();
        $this->assertEquals($this->basicShop->id, $this->legacyContext->getContext()->shop->id);
        $this->assertEquals($this->basicShop->id, Shop::getContextShopID());
        $this->assertEquals($this->basicShop->id_shop_group, Shop::getContextShopGroupID());
        $this->assertEquals(Shop::CONTEXT_SHOP, Shop::getContext());
        $this->assertNull($contextStateManager->getContextFieldsStack());
    }
}
