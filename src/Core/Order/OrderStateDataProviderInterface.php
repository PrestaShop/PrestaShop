<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Order;

/**
 * Interface OrderStateDataProviderInterface defines OrderState data provider.
 */
interface OrderStateDataProviderInterface
{
    /**
     * Get order states in given language.
     *
     * @param int $languageId
     *
     * @return array
     */
    public function getOrderStates($languageId);
}
