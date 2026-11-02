<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Module\Configuration;

/**
 * Configures which payment modules charge the customer trade credit.
 */
interface TradeCreditPaymentModuleConfiguratorInterface
{
    /**
     * Replaces the trade credit payment modules of the shop.
     *
     * @param int[] $moduleIds
     */
    public function setTradeCreditModules(int $shopId, array $moduleIds): void;
}
