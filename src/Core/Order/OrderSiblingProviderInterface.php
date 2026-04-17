<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Order;

interface OrderSiblingProviderInterface
{
    /**
     * @param int $orderId
     *
     * @return int returns previous order id or 0 if it does not exist
     */
    public function getNextOrderId(int $orderId): int;

    /**
     * @param int $orderId
     *
     * @return int returns next order id or 0 if it does not exist
     */
    public function getPreviousOrderId(int $orderId): int;
}
