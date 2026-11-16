<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\CartRule;

use CartRule;
use Configuration;
use PrestaShop\PrestaShop\Adapter\CartRule\CartRuleQuantityReserver;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CartRuleQuantityReserverTest extends KernelTestCase
{
    private CartRuleQuantityReserver $cartRuleQuantityReserver;

    private ?CartRule $cartRule = null;

    /**
     * @var int[]
     */
    private array $orderCartRuleIds = [];

    protected function setUp(): void
    {
        self::bootKernel();

        $this->cartRuleQuantityReserver = self::getContainer()->get(CartRuleQuantityReserver::class);
    }

    protected function tearDown(): void
    {
        foreach ($this->orderCartRuleIds as $orderCartRuleId) {
            self::getContainer()->get('doctrine.dbal.default_connection')->delete(
                _DB_PREFIX_ . 'order_cart_rule',
                ['id_order_cart_rule' => $orderCartRuleId]
            );
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

    public function testReleaseGivesBackOneUnit(): void
    {
        $cartRuleId = $this->createCartRule(1);

        $this->assertTrue($this->cartRuleQuantityReserver->reserve($cartRuleId));
        $this->cartRuleQuantityReserver->release($cartRuleId);
        $this->assertSame(1, $this->getQuantity($cartRuleId));
    }

    public function testUnlimitedQuantityIsNeverChanged(): void
    {
        $cartRuleId = $this->createCartRule(null);

        $this->assertFalse($this->cartRuleQuantityReserver->reserve($cartRuleId));
        $this->cartRuleQuantityReserver->release($cartRuleId);
        $this->assertNull($this->getQuantity($cartRuleId));
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
        $connection = self::getContainer()->get('doctrine.dbal.default_connection');
        $connection->insert(_DB_PREFIX_ . 'order_cart_rule', [
            'id_order' => $orderId,
            'id_cart_rule' => $cartRuleId,
            'name' => 'Quantity reserver test',
            'deleted' => (int) $deleted,
        ]);
        $this->orderCartRuleIds[] = (int) $connection->lastInsertId();
    }

    private function createCartRule(?int $quantity): int
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

    private function getQuantity(int $cartRuleId): ?int
    {
        $quantity = self::getContainer()->get('doctrine.dbal.default_connection')->fetchOne(
            'SELECT `quantity` FROM `' . _DB_PREFIX_ . 'cart_rule` WHERE `id_cart_rule` = :cartRuleId',
            ['cartRuleId' => $cartRuleId]
        );

        return null === $quantity ? null : (int) $quantity;
    }
}
