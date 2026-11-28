<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider\CarrierForOrderChoiceProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CarrierForOrderChoiceProviderTest extends KernelTestCase
{
    private const UNKNOWN_ORDER_ID = 999999999;

    /**
     * `Cart::getCartByOrderId()` answers false for an order that has no cart. Reading properties off
     * that is only a warning on PHP 8, so the provider used to compute carriers from customer group
     * 0 and address 0 and hand back a plausible looking list for an order that has none.
     */
    public function testAnOrderWithoutACartOffersNoCarrier(): void
    {
        self::bootKernel();

        $provider = new CarrierForOrderChoiceProvider();

        $this->assertSame([], $provider->getChoices(['order_id' => self::UNKNOWN_ORDER_ID]));
    }
}
