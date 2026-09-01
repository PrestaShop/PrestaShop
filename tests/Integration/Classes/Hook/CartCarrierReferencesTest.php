<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Hook;

use Cart;
use Context;
use Db;
use Hook;
use Module;
use PHPUnit\Framework\TestCase;

/**
 * Payment modules are filtered by the carrier the cart uses, read from `id_carrier`. That column only
 * mirrors a delivery option holding a single carrier, so a cart split across carriers leaves it empty
 * and the restriction was skipped entirely. The carriers are now read from the delivery option.
 */
class CartCarrierReferencesTest extends TestCase
{
    public function testTheReferencesOfASingleCarrierOptionAreReturned(): void
    {
        $this->assertSame([1], $this->referencesFor([1 => '1,']));
    }

    public function testEveryCarrierOfASplitCartIsReturned(): void
    {
        $this->assertSame([1, 2], $this->referencesFor([1 => '1,', 2 => '2,']));
    }

    public function testACarrierIsNotRepeatedWhenSeveralPackagesShareIt(): void
    {
        $this->assertSame([2], $this->referencesFor([1 => '2,', 2 => '2,']));
    }

    public function testAnOptionKeyHoldingSeveralCarriersIsSplit(): void
    {
        $this->assertSame([2, 3], $this->referencesFor([1 => '2,3,']));
    }

    public function testACarrierThatNoLongerExistsIsIgnored(): void
    {
        $this->assertSame([1], $this->referencesFor([1 => '1,999999,']));
    }

    public function testNoDeliveryOptionYieldsNoReference(): void
    {
        $this->assertSame([], $this->referencesFor([]));
    }

    public function testAPaymentModuleWithheldFromOneCarrierOfASplitCartIsNotOffered(): void
    {
        // read from the database rather than assumed, so the test does not depend on fixture ids
        $moduleId = (int) Module::getModuleIdByName('bankwire');
        $carriers = Db::getInstance()->executeS(
            'SELECT `id_carrier`, `id_reference` FROM `' . _DB_PREFIX_ . 'carrier` WHERE `deleted` = 0 ORDER BY `id_carrier` LIMIT 2'
        );
        $this->assertGreaterThan(0, $moduleId);
        $this->assertCount(2, $carriers);
        [$allowed, $withheld] = $carriers;

        // allow the module on the first carrier only, for the duration of the test
        $shopId = (int) Context::getContext()->shop->id;
        $where = '`id_module` = ' . $moduleId . ' AND `id_shop` = ' . $shopId;
        $saved = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'module_carrier` WHERE ' . $where);
        Db::getInstance()->delete('module_carrier', $where);
        Db::getInstance()->insert('module_carrier', [
            'id_module' => $moduleId,
            'id_shop' => $shopId,
            'id_reference' => (int) $allowed['id_reference'],
        ]);

        $context = sprintf('module %d, shop %d, carriers %d (ref %d) and %d (ref %d)', $moduleId, $shopId, $allowed['id_carrier'], $allowed['id_reference'], $withheld['id_carrier'], $withheld['id_reference']);
        try {
            $this->assertContains('bankwire', $this->paymentModulesFor([1 => $allowed['id_carrier'] . ',']), $context);
            $split = $this->paymentModulesFor([1 => $allowed['id_carrier'] . ',' . $withheld['id_carrier'] . ',']);
            $this->assertNotContains('bankwire', $split, $context . ', offered: ' . implode(', ', $split));
        } finally {
            Db::getInstance()->delete('module_carrier', $where);
            foreach ($saved as $row) {
                Db::getInstance()->insert('module_carrier', $row);
            }
        }
    }

    /**
     * @param array<int, string> $deliveryOption
     *
     * @return string[]
     */
    private function paymentModulesFor(array $deliveryOption): array
    {
        // a split cart keeps id_carrier empty
        $cart = $this->createMock(Cart::class);
        $cart->id = 1;
        $cart->id_carrier = 0;
        $cart->method('getDeliveryOption')->willReturn($deliveryOption);

        $context = Context::getContext();
        $previousCart = $context->cart;
        $previousEmployee = $context->employee;
        $context->cart = $cart;
        $context->employee = null;
        try {
            return array_column(Hook::getHookModuleExecList('paymentOptions') ?: [], 'module');
        } finally {
            $context->cart = $previousCart;
            $context->employee = $previousEmployee;
        }
    }

    /**
     * @param array<int, string> $deliveryOption
     *
     * @return int[]
     */
    private function referencesFor(array $deliveryOption): array
    {
        $cart = $this->createMock(Cart::class);
        $cart->method('getDeliveryOption')->willReturn($deliveryOption);

        return TestableHook::references($cart);
    }
}

class TestableHook extends Hook
{
    /**
     * @return int[]
     */
    public static function references(Cart $cart): array
    {
        return self::getCartCarrierReferences($cart);
    }
}
