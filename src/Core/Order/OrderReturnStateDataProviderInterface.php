<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Order;

/**
 * Interface OrderReturnStateDataProviderInterface defines OrderReturnState data provider.
 */
interface OrderReturnStateDataProviderInterface
{
    /**
     * Get order return states in given language.
     *
     * @param int $languageId
     *
     * @return array
     */
    public function getOrderReturnStates($languageId);
}
