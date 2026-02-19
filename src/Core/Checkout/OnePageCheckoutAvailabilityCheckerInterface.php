<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Checkout;

/**
 * Defines how to check One Page Checkout availability for a specific shop.
 */
interface OnePageCheckoutAvailabilityCheckerInterface
{
    /**
     * Returns whether One Page Checkout is enabled for the given shop.
     *
     * @param int $shopId
     *
     * @return bool
     */
    public function isEnabledForShop(int $shopId): bool;
}
