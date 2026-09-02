<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\OrderState;

use OrderState;
use PrestaShop\PrestaShop\Core\Order\OrderStateDataProviderInterface;

/**
 * Class OrderStateDataProvider provides OrderState data using legacy code.
 */
final class OrderStateDataProvider implements OrderStateDataProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getOrderStates($languageId)
    {
        return OrderState::getOrderStates($languageId, false);
    }
}
