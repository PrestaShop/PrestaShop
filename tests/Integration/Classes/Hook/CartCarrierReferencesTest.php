<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Hook;

use Cart;
use Hook;
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
