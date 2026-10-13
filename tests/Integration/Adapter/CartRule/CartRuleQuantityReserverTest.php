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

namespace Tests\Integration\Adapter\CartRule;

use CartRule;
use Configuration;
use Db;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\CartRule\CartRuleQuantityReserver;
use PrestaShopDatabaseException;

class CartRuleQuantityReserverTest extends TestCase
{
    /**
     * @var CartRuleQuantityReserver
     */
    private $cartRuleQuantityReserver;

    /**
     * @var CartRule|null
     */
    private $cartRule;

    /**
     * @var int[]
     */
    private $orderCartRuleIds = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->cartRuleQuantityReserver = new CartRuleQuantityReserver();
    }

    protected function tearDown(): void
    {
        foreach ($this->orderCartRuleIds as $orderCartRuleId) {
            Db::getInstance()->delete('order_cart_rule', '`id_order_cart_rule` = ' . $orderCartRuleId);
        }
        $this->orderCartRuleIds = [];

        if (null !== $this->cartRule) {
            $this->cartRule->delete();
            $this->cartRule = null;
        }

        parent::tearDown();
    }

    public function testReserveTakesOneUnit(): void
    {
        $cartRuleId = $this->createCartRule(2);

        $this->assertTrue($this->cartRuleQuantityReserver->reserve($cartRuleId));
        $this->assertSame(1, $this->getQuantity($cartRuleId));
    }

    public function testReserveRefusesWhenNoUnitIsLeft(): void
    {
        $cartRuleId = $this->createCartRule(1);

        $this->assertTrue($this->cartRuleQuantityReserver->reserve($cartRuleId));
        $this->assertFalse($this->cartRuleQuantityReserver->reserve($cartRuleId));
        $this->assertSame(0, $this->getQuantity($cartRuleId));
    }

    public function testReserveThrowsWhenTheUpdateFails(): void
    {
        $db = $this->createMock(Db::class);
        $db->method('execute')->willReturn(false);
        $db->method('getMsgError')->willReturn('Lost connection');
        $db->expects($this->never())->method('Affected_Rows');
        Db::setInstanceForTesting($db);

        $this->expectException(PrestaShopDatabaseException::class);
        try {
            $this->cartRuleQuantityReserver->reserve(1);
        } finally {
            Db::deleteTestingInstance();
        }
    }

    public function testReleaseGivesBackOneUnit(): void
    {
        $cartRuleId = $this->createCartRule(1);

        $this->assertTrue($this->cartRuleQuantityReserver->reserve($cartRuleId));
        $this->cartRuleQuantityReserver->release($cartRuleId);
        $this->assertSame(1, $this->getQuantity($cartRuleId));
    }

    public function testGetCartRuleIdsKeptByOrders(): void
    {
        $cartRuleId = $this->createCartRule(1);
        $this->addOrderCartRule(999001, $cartRuleId, false);
        $this->addOrderCartRule(999002, $cartRuleId + 1, true);

        $this->assertSame([$cartRuleId], $this->cartRuleQuantityReserver->getCartRuleIdsKeptByOrders([999001, 999002]));
        $this->assertSame([], $this->cartRuleQuantityReserver->getCartRuleIdsKeptByOrders([999003]));
        $this->assertSame([], $this->cartRuleQuantityReserver->getCartRuleIdsKeptByOrders([]));
    }

    private function addOrderCartRule(int $orderId, int $cartRuleId, bool $deleted): void
    {
        Db::getInstance()->insert('order_cart_rule', [
            'id_order' => $orderId,
            'id_cart_rule' => $cartRuleId,
            'name' => 'Quantity reserver test',
            'deleted' => (int) $deleted,
        ]);
        $this->orderCartRuleIds[] = (int) Db::getInstance()->Insert_ID();
    }

    private function createCartRule(int $quantity): int
    {
        $this->cartRule = new CartRule();
        $this->cartRule->name = [(int) Configuration::get('PS_LANG_DEFAULT') => 'Quantity reserver test'];
        $this->cartRule->code = 'QUANTITYRESERVERTEST';
        $this->cartRule->quantity = $quantity;
        $this->cartRule->quantity_per_user = 1;
        $this->cartRule->reduction_amount = 10;
        $this->cartRule->date_from = date('Y-m-d H:i:s', strtotime('-1 day'));
        $this->cartRule->date_to = date('Y-m-d H:i:s', strtotime('+1 day'));
        $this->cartRule->add();

        return (int) $this->cartRule->id;
    }

    private function getQuantity(int $cartRuleId): int
    {
        // Read from the database directly, the CartRule object may come from the object cache
        return (int) Db::getInstance()->getValue(
            'SELECT `quantity` FROM `' . _DB_PREFIX_ . 'cart_rule` WHERE `id_cart_rule` = ' . $cartRuleId,
            false
        );
    }
}
