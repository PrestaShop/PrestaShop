<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Order\Checkout;

use CheckoutProcess;
use CheckoutSession;
use PrestaShopBundle\Translation\TranslatorComponent;

/**
 * Contract for modules that provide their own checkout process.
 * If exactly one enabled provider is returned by hooked modules, its checkout
 * process replaces the native one. Otherwise, we falls back to the
 * native checkout.
 */
interface CheckoutProcessProviderInterface
{
    /**
     * Indicates whether the module checkout can be used.
     */
    public function isEnabled(): bool;

    /**
     * Builds the checkout process for the current customer session.
     */
    public function buildCheckoutProcess(
        CheckoutSession $session,
        TranslatorComponent $translator
    ): CheckoutProcess;
}
